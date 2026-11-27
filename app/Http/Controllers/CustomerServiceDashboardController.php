<?php

namespace App\Http\Controllers;

use App\Http\Requests\EscalateCustomerComplaintRequest;
use App\Http\Requests\StoreCustomerComplaintRequest;
use App\Http\Requests\UpdateCustomerComplaintRequest;
use App\Models\Booking;
use App\Models\CsConversation;
use App\Models\CsMessage;
use App\Models\CustomerComplaint;
use App\Models\Refund;
use App\Models\Schedule;
use App\Models\TripIncident;
use App\Models\User;
use App\Notifications\DriverOperationalNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerServiceDashboardController extends Controller
{
    /**
     * Beranda Customer Service: sapaan dan statistik kendala bulan ini.
     */
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $today = now()->toDateString();

        $monthlyBase = CustomerComplaint::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $monthlyStats = [
            'total' => (clone $monthlyBase)->count(),
            'baru_masuk' => (clone $monthlyBase)->where('status', 'baru_masuk')->count(),
            'sedang_dicek' => (clone $monthlyBase)->where('status', 'sedang_dicek')->count(),
            'selesai' => (clone $monthlyBase)->where('status', 'selesai')->count(),
            'escalated' => (clone $monthlyBase)->where('is_escalated', true)->count(),
        ];

        $categoryCountsRaw = (clone $monthlyBase)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $monthlyByCategory = collect(CustomerComplaint::CATEGORIES)
            ->map(fn (string $label, string $key): object => (object) [
                'key' => $key,
                'label' => $label,
                'count' => (int) ($categoryCountsRaw[$key] ?? 0),
                'percent' => $monthlyStats['total'] > 0
                    ? (int) round(((int) ($categoryCountsRaw[$key] ?? 0) / $monthlyStats['total']) * 100)
                    : 0,
            ])
            ->values();

        $todaysSchedulesCount = Schedule::query()
            ->whereDate('departure_date', $today)
            ->whereNotIn('status', ['cancelled', 'Cancelled', 'batal', 'Batal'])
            ->count();

        return view('dashboard.customer-service.index', [
            'monthLabel' => now()->translatedFormat('F Y'),
            'monthlyStats' => $monthlyStats,
            'monthlyByCategory' => $monthlyByCategory,
            // Dipertahankan untuk kompatibilitas backward dengan pengujian dashboard CS yang ada
            'query' => $query,
            'searchResults' => $query !== '' ? $this->searchBookings($query) : collect(),
            'stats' => [
                'departures_today' => $todaysSchedulesCount,
                'pending_payments' => Booking::where('status', 'pending')->where('expired_at', '>', now())->count(),
                'pending_refunds' => Refund::whereIn('status', ['Pending', 'pending', 'Menunggu', 'menunggu', 'Diajukan', 'diajukan'])->count(),
            ],
        ]);
    }

    /**
     * Halaman 1: Pencarian & Detail Tiket Penumpang (Read-Only).
     */
    public function cekTiket(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $searchResults = $query !== '' ? $this->searchBookings($query) : collect();

        return view('dashboard.customer-service.cek-tiket', [
            'query' => $query,
            'searchResults' => $searchResults,
        ]);
    }

    /**
     * Halaman 2: Manajemen Laporan & Tiket Kendala (Complaint / Issue Log).
     */
    public function laporanKendala(Request $request): View
    {
        $complaintStatus = (string) $request->query('complaint_status', 'all');
        $complaintCategory = (string) $request->query('complaint_category', 'all');
        $complaintSearch = trim((string) $request->query('complaint_q', ''));

        $prefilledBookingCode = trim((string) $request->query('booking_code', ''));
        $prefilledCustomerName = trim((string) $request->query('customer_name', ''));
        $prefilledCustomerPhone = trim((string) $request->query('customer_phone', ''));

        $complaintsQuery = CustomerComplaint::query()
            ->with([
                'booking.schedule.route.origin',
                'booking.schedule.route.destination',
                'reporter',
                'escalatedBy',
            ])
            ->latest();

        if ($complaintStatus === 'escalated') {
            $complaintsQuery->where('is_escalated', true);
        } elseif (array_key_exists($complaintStatus, CustomerComplaint::STATUSES)) {
            $complaintsQuery->where('status', $complaintStatus);
        }

        if (array_key_exists($complaintCategory, CustomerComplaint::CATEGORIES)) {
            $complaintsQuery->where('category', $complaintCategory);
        }

        if ($complaintSearch !== '') {
            $like = '%'.$complaintSearch.'%';
            $complaintsQuery->where(function (Builder $builder) use ($like) {
                $builder->where('ticket_code', 'like', $like)
                    ->orWhere('booking_code', 'like', $like)
                    ->orWhere('customer_name', 'like', $like)
                    ->orWhere('customer_phone', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        $complaints = $complaintsQuery->take(50)->get();

        $complaintStats = [
            'total' => CustomerComplaint::count(),
            'baru_masuk' => CustomerComplaint::where('status', 'baru_masuk')->count(),
            'sedang_dicek' => CustomerComplaint::where('status', 'sedang_dicek')->count(),
            'selesai' => CustomerComplaint::where('status', 'selesai')->count(),
            'escalated' => CustomerComplaint::where('is_escalated', true)->count(),
        ];

        return view('dashboard.customer-service.laporan-kendala', [
            'complaints' => $complaints,
            'complaintStats' => $complaintStats,
            'complaintStatus' => $complaintStatus,
            'complaintCategory' => $complaintCategory,
            'complaintSearch' => $complaintSearch,
            'complaintCategories' => CustomerComplaint::CATEGORIES,
            'complaintStatuses' => CustomerComplaint::STATUSES,
            'prefilledBookingCode' => $prefilledBookingCode,
            'prefilledCustomerName' => $prefilledCustomerName,
            'prefilledCustomerPhone' => $prefilledCustomerPhone,
        ]);
    }

    /**
     * Halaman 3: Pantauan Armada Hari Ini & Kontak Cepat Sopir (View-Only).
     */
    public function pantauanArmada(): View
    {
        $today = now()->toDateString();

        $todaysSchedules = Schedule::query()
            ->with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver.user',
                'incidents' => fn ($q) => $q->latest('reported_at')->latest('id'),
            ])
            ->whereDate('departure_date', $today)
            ->whereNotIn('status', ['cancelled', 'Cancelled', 'batal', 'Batal'])
            ->orderBy('departure_time')
            ->get();

        $fleetSchedules = $todaysSchedules->map(function (Schedule $schedule): object {
            $tripKey = $schedule->tripStatusKey();
            [$tripLabel, $tripTone] = match ($tripKey) {
                'pickup' => ['Proses Penjemputan', 'amber'],
                'departed' => ['Dalam Perjalanan', 'blue'],
                'arrived', 'completed' => ['Selesai', 'emerald'],
                default => ['Belum Berangkat', 'slate'],
            };

            $activeIncidents = $schedule->incidents
                ->filter(fn (TripIncident $incident) => ! in_array(strtolower((string) $incident->status), ['resolved', 'selesai'], true))
                ->values();

            $driverName = $schedule->driver?->user?->name ?? 'Belum Ditugaskan';
            $rawPhone = $schedule->driver?->phone ?: ($schedule->driver?->user?->phone ?? '');
            $telPhone = $this->formatTelPhone($rawPhone);
            $waPhone = $this->formatWaPhone($rawPhone);
            $routeText = ($schedule->route?->origin?->city ?? $schedule->route?->origin?->name ?? '-')
                .' → '
                .($schedule->route?->destination?->city ?? $schedule->route?->destination?->name ?? '-');
            $waMessage = "Halo Pak {$driverName}, saya dari Customer Service TravelKu. Mohon info operasional untuk jadwal {$routeText} keberangkatan {$schedule->formattedDepartureTime()}.";

            return (object) [
                'schedule' => $schedule,
                'trip_key' => $tripKey,
                'trip_label' => $tripLabel,
                'trip_tone' => $tripTone,
                'driver_name' => $driverName,
                'driver_phone' => $rawPhone,
                'tel_phone' => $telPhone,
                'wa_url' => $waPhone !== '' ? 'https://wa.me/'.$waPhone.'?text='.rawurlencode($waMessage) : null,
                'active_incidents' => $activeIncidents,
                'has_emergency' => $activeIncidents->contains(fn (TripIncident $i) => in_array($i->severity, ['darurat', 'tinggi'], true) || $i->category === 'sos_emergency'),
            ];
        });

        $fleetSummary = [
            'total' => $fleetSchedules->count(),
            'belum_berangkat' => $fleetSchedules->where('trip_label', 'Belum Berangkat')->count(),
            'proses_penjemputan' => $fleetSchedules->where('trip_label', 'Proses Penjemputan')->count(),
            'dalam_perjalanan' => $fleetSchedules->where('trip_label', 'Dalam Perjalanan')->count(),
            'selesai' => $fleetSchedules->where('trip_label', 'Selesai')->count(),
            'active_alerts' => $fleetSchedules->sum(fn (object $item) => $item->active_incidents->count()),
        ];

        return view('dashboard.customer-service.pantauan-armada', [
            'fleetSchedules' => $fleetSchedules,
            'fleetSummary' => $fleetSummary,
        ]);
    }

    /**
     * Halaman Profil Akun Customer Service.
     */
    public function profil(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $agentStats = [
            'reported' => CustomerComplaint::where('reported_by', $user->id)->count(),
            'resolved' => CustomerComplaint::where('reported_by', $user->id)->where('status', 'selesai')->count(),
            'escalated' => CustomerComplaint::where('escalated_by', $user->id)->count(),
        ];

        return view('dashboard.customer-service.profil', [
            'user' => $user,
            'agentStats' => $agentStats,
        ]);
    }

    /**
     * Perbarui informasi profil akun Customer Service.
     */
    public function updateProfil(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{8,20}$/'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email tersebut sudah digunakan akun lain.',
            'phone.regex' => 'Nomor HP harus berupa 8–20 digit angka tanpa spasi atau simbol.',
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'phone' => isset($validated['phone']) && trim((string) $validated['phone']) !== ''
                ? trim((string) $validated['phone'])
                : null,
        ]);

        return redirect()
            ->route('dashboard.customer-service.profil')
            ->with('success', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun Customer Service.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('dashboard.customer-service.profil')
            ->with('success', 'Password akun berhasil diperbarui.');
    }

    /**
     * Simpan laporan kendala baru dari pelanggan.
     */
    public function storeComplaint(StoreCustomerComplaintRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $rawBookingCode = trim((string) ($validated['booking_code'] ?? ''));
        $booking = null;

        if ($rawBookingCode !== '') {
            $booking = Booking::query()
                ->with(['user', 'passengers'])
                ->where('booking_code', $rawBookingCode)
                ->first();
        }

        $customerName = trim((string) ($validated['customer_name'] ?? ''));
        if ($customerName === '' && $booking) {
            $customerName = $booking->contact_name
                ?: ($booking->user?->name
                ?: ($booking->passengers->first()?->name ?? ''));
        }

        $customerPhone = trim((string) ($validated['customer_phone'] ?? ''));
        if ($customerPhone === '' && $booking) {
            $customerPhone = $booking->contact_phone
                ?: ($booking->user?->phone
                ?: ($booking->passengers->first()?->phone ?? ''));
        }

        $complaint = CustomerComplaint::create([
            'ticket_code' => CustomerComplaint::generateTicketCode(),
            'booking_id' => $booking?->id,
            'booking_code' => $booking?->booking_code ?? ($rawBookingCode !== '' ? strtoupper($rawBookingCode) : null),
            'reported_by' => auth()->id(),
            'customer_name' => $customerName !== '' ? $customerName : null,
            'customer_phone' => $customerPhone !== '' ? $customerPhone : null,
            'category' => $validated['category'],
            'description' => trim($validated['description']),
            'status' => 'baru_masuk',
        ]);

        return redirect()
            ->route('dashboard.customer-service.laporan-kendala')
            ->with('success', "Tiket kendala {$complaint->ticket_code} berhasil dicatat dengan status Baru Masuk.");
    }

    /**
     * Perbarui status penanganan dan catatan tindak lanjut / solusi tiket kendala.
     */
    public function updateComplaint(UpdateCustomerComplaintRequest $request, CustomerComplaint $complaint): RedirectResponse
    {
        $validated = $request->validated();
        $newStatus = $validated['status'];

        $complaint->update([
            'status' => $newStatus,
            'follow_up_notes' => isset($validated['follow_up_notes']) && trim((string) $validated['follow_up_notes']) !== ''
                ? trim((string) $validated['follow_up_notes'])
                : $complaint->follow_up_notes,
            'resolved_at' => $newStatus === 'selesai' ? ($complaint->resolved_at ?? now()) : null,
        ]);

        return redirect()
            ->route('dashboard.customer-service.laporan-kendala', array_filter([
                'complaint_status' => $request->input('redirect_status'),
                'complaint_category' => $request->input('redirect_category'),
                'complaint_q' => $request->input('redirect_q'),
            ]))
            ->with('success', "Tiket {$complaint->ticket_code} diperbarui menjadi \"{$complaint->statusLabel()}\".");
    }

    /**
     * Eskalasi tiket kendala ke Admin untuk penanganan tingkat lanjut (refund / ganti rugi).
     */
    public function escalateComplaint(EscalateCustomerComplaintRequest $request, CustomerComplaint $complaint): RedirectResponse
    {
        $validated = $request->validated();

        $complaint->update([
            'is_escalated' => true,
            'escalation_reason' => trim($validated['escalation_reason']),
            'escalated_at' => now(),
            'escalated_by' => auth()->id(),
            'status' => $complaint->status === 'baru_masuk' ? 'sedang_dicek' : $complaint->status,
        ]);

        $this->notifyAdminsOfEscalation($complaint);

        return redirect()
            ->route('dashboard.customer-service.laporan-kendala', array_filter([
                'complaint_status' => $request->input('redirect_status'),
                'complaint_category' => $request->input('redirect_category'),
                'complaint_q' => $request->input('redirect_q'),
            ]))
            ->with('success', "Tiket {$complaint->ticket_code} berhasil dieskalasi ke Admin untuk penanganan lanjutan.");
    }

    /**
     * Kirim notifikasi eskalasi CS ke seluruh akun Admin aktif.
     */
    private function notifyAdminsOfEscalation(CustomerComplaint $complaint): void
    {
        $admins = User::query()
            ->whereIn('role', ['admin_utama', 'admin', 'super_admin'])
            ->get();

        $bookingRef = $complaint->booking_code ? " (Booking {$complaint->booking_code})" : '';
        $csName = auth()->user()?->name ?? 'Customer Service';

        foreach ($admins as $admin) {
            $admin->notify(new DriverOperationalNotification(
                title: "Eskalasi CS: {$complaint->ticket_code} — {$complaint->categoryLabel()}",
                message: "{$csName} mengeskalasi kendala{$bookingRef}: {$complaint->escalation_reason}",
                statusType: 'cs_escalation',
                deepLinkUrl: route('dashboard.admin.eskalasi.index'),
                actionLabel: 'Lihat Eskalasi CS',
                meta: [
                    'complaint_id' => $complaint->id,
                    'ticket_code' => $complaint->ticket_code,
                    'booking_code' => $complaint->booking_code,
                    'category' => $complaint->category,
                ],
            ));
        }
    }

    /**
     * Find bookings by booking code, ticket number, or the customer's / passenger's name, email or phone.
     *
     * @return Collection<int, Booking>
     */
    private function searchBookings(string $query): Collection
    {
        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return collect();
        }

        $like = '%'.$cleanQuery.'%';

        return Booking::query()
            ->with([
                'user',
                'ticket',
                'payment',
                'passengers.seat',
                'schedule.route.origin',
                'schedule.route.destination',
                'schedule.vehicle',
                'schedule.driver.user',
                'schedule.incidents',
            ])
            ->where(function (Builder $builder) use ($like) {
                $builder->where('booking_code', 'like', $like)
                    ->orWhere('contact_name', 'like', $like)
                    ->orWhere('contact_phone', 'like', $like)
                    ->orWhere('contact_email', 'like', $like)
                    ->orWhereHas('ticket', fn (Builder $ticket) => $ticket->where('ticket_number', 'like', $like))
                    ->orWhereHas('user', fn (Builder $user) => $user->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like))
                    ->orWhereHas('passengers', fn (Builder $passenger) => $passenger->where('name', 'like', $like)
                        ->orWhere('phone', 'like', $like));
            })
            ->latest()
            ->take(20)
            ->get();
    }

    /**
     * Format nomor telepon untuk tautan WhatsApp (62xxx).
     */
    private function formatWaPhone(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone);
        if ($digits === '' || $digits === null) {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Format nomor telepon untuk tautan tel:.
     */
    private function formatTelPhone(?string $phone): string
    {
        return (string) preg_replace('/[^0-9+]/', '', (string) $phone);
    }

    /**
     * Halaman Live Chat & SLA Timer dengan Konteks Booking Otomatis, Template Balasan Cepat,
     * Kirim E-Tiket Satu Klik, dan Eskalasi ke Admin.
     */
    public function liveChat(Request $request): View
    {
        $tab = (string) $request->query('tab', 'all');
        $search = trim((string) $request->query('q', ''));
        $selectedId = $request->integer('conversation');

        $allConversations = CsConversation::query()
            ->with([
                'booking.schedule.route.origin',
                'booking.schedule.route.destination',
                'booking.schedule.vehicle',
                'booking.schedule.driver.user',
                'booking.passengers.seat',
                'booking.ticket',
                'booking.payment',
                'assignedAgent',
                'messages' => fn ($q) => $q->latest('id')->limit(1),
            ])
            ->get()
            ->sortBy(function (CsConversation $conv): array {
                $urgency = $conv->departureUrgency();
                $isResolved = $conv->status === 'resolved' ? 1 : 0;
                $waitStamp = $conv->last_customer_message_at?->timestamp ?? $conv->created_at?->timestamp ?? PHP_INT_MAX;

                return [$isResolved, $urgency['score'], $waitStamp];
            })
            ->values();

        $tabCounts = [
            'all' => $allConversations->where('status', '!=', 'resolved')->count(),
            'unassigned' => $allConversations->where('status', 'unassigned')->count(),
            'assigned' => $allConversations->where('status', 'assigned')->where('assigned_to', auth()->id())->count(),
            'resolved' => $allConversations->where('status', 'resolved')->count(),
        ];

        $filteredConversations = $allConversations->filter(function (CsConversation $conv) use ($tab, $search): bool {
            $matchesTab = match ($tab) {
                'unassigned' => $conv->status === 'unassigned',
                'assigned' => $conv->status === 'assigned' && (int) $conv->assigned_to === (int) auth()->id(),
                'resolved' => $conv->status === 'resolved',
                default => $conv->status !== 'resolved',
            };

            if (! $matchesTab) {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $needle = mb_strtolower($search);

            return str_contains(mb_strtolower($conv->customer_name), $needle)
                || str_contains(mb_strtolower((string) $conv->customer_phone), $needle)
                || str_contains(mb_strtolower((string) $conv->conversation_code), $needle)
                || str_contains(mb_strtolower((string) $conv->booking?->booking_code), $needle)
                || str_contains(mb_strtolower((string) $conv->subject), $needle);
        })->values();

        $activeConversation = null;
        if ($selectedId > 0) {
            $activeConversation = CsConversation::query()
                ->with([
                    'booking.schedule.route.origin',
                    'booking.schedule.route.destination',
                    'booking.schedule.vehicle',
                    'booking.schedule.driver.user',
                    'booking.passengers.seat',
                    'booking.ticket',
                    'booking.payment',
                    'assignedAgent',
                    'messages.sender',
                ])
                ->find($selectedId);
        }

        if (! $activeConversation && $filteredConversations->isNotEmpty()) {
            $firstId = $filteredConversations->first()->id;
            $activeConversation = CsConversation::query()
                ->with([
                    'booking.schedule.route.origin',
                    'booking.schedule.route.destination',
                    'booking.schedule.vehicle',
                    'booking.schedule.driver.user',
                    'booking.passengers.seat',
                    'booking.ticket',
                    'booking.payment',
                    'assignedAgent',
                    'messages.sender',
                ])
                ->find($firstId);
        }

        $quickTemplates = $this->buildQuickReplyTemplates($activeConversation);

        $driverContact = null;
        if ($activeConversation?->booking?->schedule?->driver) {
            $driver = $activeConversation->booking->schedule->driver;
            $rawPhone = $driver->phone ?: ($driver->user?->phone ?? '');
            $waPhone = $this->formatWaPhone($rawPhone);
            $telPhone = $this->formatTelPhone($rawPhone);
            $driverName = $driver->user?->name ?? 'Sopir TravelKu';
            $bookingCode = $activeConversation->booking->booking_code;

            $driverContact = (object) [
                'name' => $driverName,
                'phone' => $rawPhone,
                'tel_phone' => $telPhone,
                'wa_url' => $waPhone !== ''
                    ? 'https://wa.me/'.$waPhone.'?text='.rawurlencode("Halo Pak {$driverName}, saya dari CS TravelKu terkait penumpang kode booking {$bookingCode}.")
                    : null,
            ];
        }

        return view('dashboard.customer-service.live-chat', [
            'tab' => $tab,
            'search' => $search,
            'tabCounts' => $tabCounts,
            'conversations' => $filteredConversations,
            'activeConversation' => $activeConversation,
            'quickTemplates' => $quickTemplates,
            'driverContact' => $driverContact,
        ]);
    }

    /**
     * Buat atau mulai percakapan Live Chat baru (otomatis menghubungkan konteks booking bila ada).
     */
    public function storeConversation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_code' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:160'],
            'initial_message' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'initial_message.required' => 'Pesan awal pelanggan wajib diisi.',
            'initial_message.min' => 'Pesan awal minimal 3 karakter.',
        ]);

        $rawBookingCode = trim((string) ($validated['booking_code'] ?? ''));
        $booking = null;
        if ($rawBookingCode !== '') {
            $booking = Booking::query()
                ->with(['user', 'passengers'])
                ->where('booking_code', $rawBookingCode)
                ->first();
        }

        $customerName = trim((string) ($validated['customer_name'] ?? ''));
        if ($customerName === '' && $booking) {
            $customerName = $booking->contact_name
                ?: ($booking->user?->name
                ?: ($booking->passengers->first()?->name ?? 'Pelanggan TravelKu'));
        }
        if ($customerName === '') {
            $customerName = 'Pelanggan TravelKu';
        }

        $customerPhone = trim((string) ($validated['customer_phone'] ?? ''));
        if ($customerPhone === '' && $booking) {
            $customerPhone = $booking->contact_phone
                ?: ($booking->user?->phone
                ?: ($booking->passengers->first()?->phone ?? ''));
        }

        $now = now();

        $conversation = CsConversation::create([
            'conversation_code' => CsConversation::generateCode(),
            'user_id' => $booking?->user_id,
            'booking_id' => $booking?->id,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone !== '' ? $customerPhone : null,
            'subject' => trim((string) ($validated['subject'] ?? '')) ?: ($booking ? "Tanya Booking {$booking->booking_code}" : 'Pertanyaan Pelanggan'),
            'status' => 'unassigned',
            'last_customer_message_at' => $now,
            'last_message_at' => $now,
        ]);

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $booking?->user_id,
            'sender_type' => 'customer',
            'message_type' => 'text',
            'body' => trim($validated['initial_message']),
        ]);

        return redirect()
            ->route('dashboard.customer-service.live-chat', ['conversation' => $conversation->id])
            ->with('success', "Percakapan {$conversation->conversation_code} masuk ke antrean Live Chat.");
    }

    /**
     * Kirim balasan CS atau catatan internal ke percakapan Live Chat.
     */
    public function replyConversation(Request $request, CsConversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
            'is_internal_note' => ['nullable', 'boolean'],
        ], [
            'body.required' => 'Isi pesan tidak boleh kosong.',
        ]);

        $isInternal = (bool) ($validated['is_internal_note'] ?? false);
        $now = now();

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'agent',
            'message_type' => $isInternal ? 'internal_note' : 'text',
            'body' => trim($validated['body']),
        ]);

        $updates = [
            'last_message_at' => $now,
        ];

        if (! $isInternal) {
            $updates['last_agent_reply_at'] = $now;
        }

        if ($conversation->status === 'unassigned') {
            $updates['status'] = 'assigned';
            $updates['assigned_to'] = auth()->id();
        }

        $conversation->update($updates);

        return redirect()
            ->route('dashboard.customer-service.live-chat', ['conversation' => $conversation->id])
            ->with('success', $isInternal ? 'Catatan internal disimpan.' : 'Balasan berhasil dikirim ke pelanggan.');
    }

    /**
     * Kirim E-Tiket resmi satu klik langsung ke dalam percakapan Live Chat.
     */
    public function sendEticket(CsConversation $conversation): RedirectResponse
    {
        $conversation->loadMissing([
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.schedule.vehicle',
            'booking.schedule.driver.user',
            'booking.passengers.seat',
            'booking.ticket',
        ]);

        $booking = $conversation->booking;
        if (! $booking) {
            return redirect()
                ->route('dashboard.customer-service.live-chat', ['conversation' => $conversation->id])
                ->with('error', 'Percakapan ini belum terhubung ke Kode Booking sehingga E-Tiket tidak dapat dikirim.');
        }

        $origin = $booking->schedule?->route?->origin?->city ?: ($booking->schedule?->route?->origin?->name ?: '-');
        $destination = $booking->schedule?->route?->destination?->city ?: ($booking->schedule?->route?->destination?->name ?: '-');
        $departureText = $booking->schedule
            ? $booking->schedule->departureCarbon()->translatedFormat('d M Y, H:i').' WIB'
            : '-';
        $seats = $booking->passengerSeatNumbers() ?: $booking->total_passenger.' kursi';
        $plate = $booking->schedule?->vehicle?->plate_number ?? 'Menunggu penugasan';
        $ticketNumber = $booking->ticket?->ticket_number ?? $booking->booking_code;

        $body = "E-Tiket Resmi TravelKu — {$booking->booking_code}\n"
            ."Rute: {$origin} → {$destination}\n"
            ."Keberangkatan: {$departureText}\n"
            ."Kursi: {$seats} | Armada: {$plate}\n"
            ."No. Tiket: {$ticketNumber}";

        $now = now();

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'agent',
            'message_type' => 'e_ticket',
            'body' => $body,
            'meta' => [
                'booking_code' => $booking->booking_code,
                'ticket_number' => $ticketNumber,
                'route' => "{$origin} → {$destination}",
                'departure' => $departureText,
                'seats' => $seats,
                'plate_number' => $plate,
                'status' => $booking->statusLabel(),
            ],
        ]);

        $updates = [
            'last_agent_reply_at' => $now,
            'last_message_at' => $now,
        ];
        if ($conversation->status === 'unassigned') {
            $updates['status'] = 'assigned';
            $updates['assigned_to'] = auth()->id();
        }
        $conversation->update($updates);

        return redirect()
            ->route('dashboard.customer-service.live-chat', ['conversation' => $conversation->id])
            ->with('success', "E-Tiket {$booking->booking_code} berhasil dikirim ke percakapan.");
    }

    /**
     * Ubah status percakapan: Ambil (assign), Selesai (resolve), atau Buka Kembali (reopen).
     */
    public function updateConversationStatus(Request $request, CsConversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', Rule::in(['assign', 'resolve', 'reopen'])],
        ]);

        if ($validated['action'] === 'assign') {
            $conversation->update([
                'status' => 'assigned',
                'assigned_to' => auth()->id(),
                'resolved_at' => null,
            ]);
            $msg = 'Percakapan berhasil diambil oleh Anda.';
        } elseif ($validated['action'] === 'resolve') {
            $conversation->update([
                'status' => 'resolved',
                'assigned_to' => $conversation->assigned_to ?: auth()->id(),
                'resolved_at' => now(),
            ]);
            $msg = 'Percakapan ditandai selesai.';
        } else {
            $conversation->update([
                'status' => 'assigned',
                'assigned_to' => auth()->id(),
                'resolved_at' => null,
            ]);
            $msg = 'Percakapan dibuka kembali.';
        }

        return redirect()
            ->route('dashboard.customer-service.live-chat', ['conversation' => $conversation->id])
            ->with('success', $msg);
    }

    /**
     * Eskalasi kasus dari Live Chat ke Admin (refund, barang hilang, atau armada mogok).
     */
    public function escalateConversation(Request $request, CsConversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'escalation_type' => ['required', 'string', Rule::in(['refund', 'barang_hilang', 'armada_mogok', 'lainnya'])],
            'escalation_reason' => ['required', 'string', 'min:5', 'max:1500'],
        ], [
            'escalation_type.required' => 'Jenis kasus eskalasi wajib dipilih.',
            'escalation_reason.required' => 'Ringkasan alasan eskalasi ke Admin wajib diisi.',
            'escalation_reason.min' => 'Alasan eskalasi minimal 5 karakter.',
        ]);

        $typeLabels = [
            'refund' => 'Pengajuan Refund / Keuangan',
            'barang_hilang' => 'Barang Hilang / Tertinggal',
            'armada_mogok' => 'Armada Mogok / Ganti Mobil',
            'lainnya' => 'Kendala Khusus Lainnya',
        ];

        $categoryMap = [
            'refund' => 'lainnya',
            'barang_hilang' => 'barang_tertinggal',
            'armada_mogok' => 'keluhan_armada_ac',
            'lainnya' => 'lainnya',
        ];

        $typeLabel = $typeLabels[$validated['escalation_type']];
        $reason = trim($validated['escalation_reason']);

        $complaint = CustomerComplaint::create([
            'ticket_code' => CustomerComplaint::generateTicketCode(),
            'booking_id' => $conversation->booking_id,
            'booking_code' => $conversation->booking?->booking_code,
            'reported_by' => auth()->id(),
            'customer_name' => $conversation->customer_name,
            'customer_phone' => $conversation->customer_phone,
            'category' => $categoryMap[$validated['escalation_type']],
            'description' => "[{$typeLabel} via Live Chat {$conversation->conversation_code}] {$reason}",
            'status' => 'sedang_dicek',
            'is_escalated' => true,
            'escalation_reason' => "{$typeLabel}: {$reason}",
            'escalated_at' => now(),
            'escalated_by' => auth()->id(),
        ]);

        $this->notifyAdminsOfEscalation($complaint);

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'system',
            'message_type' => 'escalation',
            'body' => "Kasus dieskalasi ke Admin ({$typeLabel}) — Tiket {$complaint->ticket_code}: {$reason}",
            'meta' => [
                'complaint_id' => $complaint->id,
                'ticket_code' => $complaint->ticket_code,
                'escalation_type' => $typeLabel,
            ],
        ]);

        $conversation->update([
            'status' => 'assigned',
            'assigned_to' => $conversation->assigned_to ?: auth()->id(),
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('dashboard.customer-service.live-chat', ['conversation' => $conversation->id])
            ->with('success', "Kasus berhasil dieskalasi ke Admin dengan kode tiket {$complaint->ticket_code}.");
    }

    /**
     * Susun template balasan cepat yang otomatis terisi data booking aktif.
     *
     * @return array<int, array{label: string, text: string}>
     */
    private function buildQuickReplyTemplates(?CsConversation $conversation): array
    {
        $name = $conversation?->customer_name ?: 'Kak';
        $booking = $conversation?->booking;
        $code = $booking?->booking_code ?? '-';
        $origin = $booking?->schedule?->route?->origin?->name ?? 'Pool Keberangkatan';
        $destination = $booking?->schedule?->route?->destination?->name ?? 'Pool Tujuan';
        $departure = $booking?->schedule
            ? $booking->schedule->departureCarbon()->translatedFormat('d M Y, H:i').' WIB'
            : 'sesuai jadwal tiket';
        $seats = $booking ? ($booking->passengerSeatNumbers() ?: $booking->total_passenger.' kursi') : '-';
        $plate = $booking?->schedule?->vehicle?->plate_number ?? 'akan segera diinformasikan';
        $driverName = $booking?->schedule?->driver?->user?->name ?? 'sopir yang bertugas';

        return [
            [
                'label' => 'Konfirmasi Jadwal & Kursi',
                'text' => "Halo {$name}, pesanan Anda dengan kode {$code} tercatat untuk rute {$origin} → {$destination} pada {$departure} (Kursi {$seats}). Mohon hadir 15 menit sebelum keberangkatan.",
            ],
            [
                'label' => 'Info Armada & Sopir',
                'text' => "Halo {$name}, perjalanan Anda menggunakan armada berpelat {$plate} bersama {$driverName}. Kami siap membantu apabila ada kendala penjemputan.",
            ],
            [
                'label' => 'Mohon Tunggu Pengecekan',
                'text' => "Terima kasih sudah menghubungi TravelKu, {$name}. Laporan Anda sedang kami koordinasikan dengan tim operasional & sopir di lapangan. Mohon ditunggu sebentar ya.",
            ],
            [
                'label' => 'Eskalasi Diproses Admin',
                'text' => "Halo {$name}, permohonan Anda untuk kode booking {$code} telah kami teruskan ke tim Admin untuk penanganan lebih lanjut. Kami akan segera mengabari perkembangannya.",
            ],
        ];
    }

    /**
     * Halaman Melihat Konteks Booking (legacy)
     */
    public function bookingContext(): View
    {
        return view('dashboard.customer-service.booking-context');
    }

    /**
     * Halaman Melihat Riwayat Chat (legacy)
     */
    public function chatHistory(): View
    {
        return view('dashboard.customer-service.chat-history');
    }

    /**
     * Halaman Mengirim Gambar / File & Generator Tiket Instan (legacy)
     */
    public function sendFile(): View
    {
        return view('dashboard.customer-service.send-file');
    }

    /**
     * Halaman Melakukan Eskalasi ke Admin & Pelacakan Status (legacy)
     */
    public function escalation(): View
    {
        return view('dashboard.customer-service.escalation');
    }
}
