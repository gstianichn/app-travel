<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\LoginSession;
use App\Models\OperationalLocation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Admin Utama
     */
    public function index()
    {
        $user = Auth::user();

        $totalUsers = User::where('role', 'user')->count();
        $totalDrivers = Driver::count();
        $totalVehicles = Vehicle::count();
        $totalBookings = Booking::count();

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get();

        // 1. Metrik Utama Hari Ini
        $today = now()->startOfDay();

        $ticketsSoldToday = Booking::whereDate('created_at', $today)
            ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
            ->sum('total_passenger');
        if ($ticketsSoldToday <= 0) {
            $ticketsSoldToday = Booking::whereDate('created_at', $today)
                ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
                ->count();
        }

        $revenueToday = Booking::whereDate('created_at', $today)
            ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
            ->sum('total_price');

        // Jumlah armada yang dijadwalkan jalan hari ini
        $activeFleetCount = Schedule::whereDate('departure_date', $today)
            ->whereNotNull('vehicle_id')
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        // Total kursi kosong hari ini
        $emptySeatsToday = Schedule::whereDate('departure_date', $today)
            ->sum('available_seats');

        // 2. Jadwal Perjalanan Terdekat
        $upcomingSchedules = Schedule::with(['route.origin', 'route.destination', 'travelRoute', 'vehicle', 'driver.user', 'bookings'])
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(60)
            ->get()
            // Departures still ahead, or that left less than 30 minutes ago.
            ->filter(fn (Schedule $schedule) => $schedule->departureCarbon()->greaterThanOrEqualTo(now()->subMinutes(30)))
            ->take(6)
            ->values();

        // 3. Notifikasi & Pengingat Penting
        $pendingRefunds = Refund::with(['booking', 'user'])
            ->where('status', 'Pending')
            ->latest()
            ->take(4)
            ->get();
        $totalPendingRefunds = Refund::where('status', 'Pending')->count();

        $maintenanceVehicles = Vehicle::whereIn('status', ['Maintenance', 'Perbaikan', 'maintenance'])
            ->take(4)
            ->get();

        $expiringDrivers = Driver::with('user')
            ->where('license_expired_at', '<=', now()->addDays(30))
            ->orderBy('license_expired_at')
            ->take(4)
            ->get();

        $expiringVehicles = Vehicle::where(function ($q) {
            $q->whereNotNull('kir_expiry_date')->where('kir_expiry_date', '<=', now()->addDays(30));
        })->orWhere(function ($q) {
            $q->whereNotNull('stnk_tax_due_date')->where('stnk_tax_due_date', '<=', now()->addDays(30));
        })
            ->take(4)
            ->get();

        return view(
            'dashboard.admin.index',
            compact(
                'user',
                'totalUsers',
                'totalDrivers',
                'totalVehicles',
                'totalBookings',
                'recentUsers',
                'ticketsSoldToday',
                'revenueToday',
                'activeFleetCount',
                'emptySeatsToday',
                'upcomingSchedules',
                'pendingRefunds',
                'totalPendingRefunds',
                'maintenanceVehicles',
                'expiringDrivers',
                'expiringVehicles'
            )
        );
    }

    /**
     * =========================================================
     * KELOLA USER
     * =========================================================
     */

    /**
     * Menampilkan daftar user.
     */
    public function users(Request $request)
    {
        $user = Auth::user();

        $query = User::query();

        /*
        |--------------------------------------------------------------------------
        | Hanya mengambil akun dengan role user
        |--------------------------------------------------------------------------
        */

        $query->where('role', 'user');

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Verifikasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('verification')) {

            if (
                $request->verification ===
                'verified'
            ) {

                $query->whereNotNull(
                    'email_verified_at'
                );

            }

            if (
                $request->verification ===
                'unverified'
            ) {

                $query->whereNull(
                    'email_verified_at'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Daftar User
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::where(
            'role',
            'user'
        )->count();

        $activeUsers = User::where(
            'role',
            'user'
        )
            ->where(
                'status',
                'active'
            )
            ->count();

        $inactiveUsers = User::where(
            'role',
            'user'
        )
            ->where(
                'status',
                'inactive'
            )
            ->count();

        $unverifiedUsers = User::where(
            'role',
            'user'
        )
            ->whereNull(
                'email_verified_at'
            )
            ->count();

        return view(
            'dashboard.admin.kelola-user.index',
            compact(
                'user',
                'users',
                'totalUsers',
                'activeUsers',
                'inactiveUsers',
                'unverifiedUsers'
            )
        );
    }

    /**
     * Menampilkan form tambah user.
     */
    public function createUser()
    {
        $user = Auth::user();

        return view(
            'dashboard.admin.kelola-user.create',
            compact('user')
        );
    }

    /**
     * Menyimpan user baru.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

        ], [

            'name.required' => 'Nama wajib diisi.',

            'email.required' => 'Email wajib diisi.',

            'email.email' => 'Format email tidak valid.',

            'email.unique' => 'Email sudah digunakan.',

            'password.required' => 'Password wajib diisi.',

            'password.min' => 'Password minimal 8 karakter.',

            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

        ]);

        User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'phone' => $validated['phone'] ?? null,

            'password' => Hash::make(
                $validated['password']
            ),

            /*
            |--------------------------------------------------------------------------
            | User yang dibuat Admin Utama
            |--------------------------------------------------------------------------
            */

            'role' => 'user',

            'status' => 'active',

        ]);

        return redirect()
            ->route(
                'dashboard.admin.kelola-user.index'
            )
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail user.
     */
    public function showUser(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan akun adalah user biasa
        |--------------------------------------------------------------------------
        */

        abort_if(
            $user->role !== 'user',
            404
        );

        $admin = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Eager load riwayat perjalanan / bookings dan relasinya
        |--------------------------------------------------------------------------
        */
        $user->load([
            'bookings' => function ($query) {
                $query->with([
                    'schedule.route.origin',
                    'schedule.route.destination',
                    'schedule.travelRoute',
                    'schedule.vehicle',
                    'schedule.driver.user',
                    'ticket',
                    'payment',
                    'refund',
                ])->latest();
            },
        ]);

        $bookings = $user->bookings;
        $totalBookings = $bookings->count();
        $totalSpent = $bookings->whereIn('status', [
            'Paid', 'Confirmed', 'Completed', 'paid', 'confirmed', 'completed', 'success',
        ])->sum('total_price');

        return view(
            'dashboard.admin.kelola-user.show',
            compact(
                'user',
                'admin',
                'bookings',
                'totalBookings',
                'totalSpent'
            )
        );
    }

    /**
     * Menampilkan form edit user.
     */
    public function editUser(User $user)
    {
        abort_if(
            $user->role !== 'user',
            404
        );

        $admin = Auth::user();

        return view(
            'dashboard.admin.kelola-user.edit',
            compact(
                'user',
                'admin'
            )
        );
    }

    /**
     * Memperbarui data user.
     */
    public function updateUser(
        Request $request,
        User $user
    ) {

        abort_if(
            $user->role !== 'user',
            404
        );

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore(
                    $user->id
                ),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

        ], [

            'name.required' => 'Nama wajib diisi.',

            'email.required' => 'Email wajib diisi.',

            'email.email' => 'Format email tidak valid.',

            'email.unique' => 'Email sudah digunakan.',

            'password.min' => 'Password minimal 8 karakter.',

            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

            'status.required' => 'Status user wajib dipilih.',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Update data utama
        |--------------------------------------------------------------------------
        */

        $user->name =
            $validated['name'];

        $user->email =
            $validated['email'];

        $user->phone =
            $validated['phone'] ?? null;

        $user->status =
            $validated['status'];

        /*
        |--------------------------------------------------------------------------
        | Update password jika diisi
        |--------------------------------------------------------------------------
        */

        if (
            ! empty(
                $validated['password']
            )
        ) {

            $user->password =
                Hash::make(
                    $validated['password']
                );
        }
        $user->save();

        return redirect()
            ->route(
                'dashboard.admin.kelola-user.index'
            )
            ->with(
                'success',
                'Data user berhasil diperbarui.'
            );
    }

    /**
     * Mengaktifkan / menonaktifkan user.
     */
    public function toggleUserStatus(
        User $user
    ) {

        abort_if(
            $user->role !== 'user',
            404
        );

        if ($user->status === 'active') {
            $user->status = 'inactive';
            $message = "Akun pengguna {$user->name} berhasil dinonaktifkan / diblokir.";
        } else {
            $user->status = 'active';
            $message = "Akun pengguna {$user->name} berhasil diaktifkan kembali.";
        }

        $user->save();

        return redirect()
            ->back()
            ->with(
                'success',
                $message
            );
    }

    /**
     * Reset kata sandi user secara manual oleh admin utama.
     */
    public function resetPassword(Request $request, User $user)
    {
        abort_if(
            $user->role !== 'user',
            404
        );

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->back()
            ->with(
                'success',
                "Kata sandi untuk pengguna {$user->name} berhasil direset. Beritahukan kata sandi baru kepada pelanggan."
            );
    }

    /**
     * Menghapus user.
     */
    public function destroyUser(
        User $user
    ) {

        abort_if(
            $user->role !== 'user',
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Jangan menghapus akun admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->id ===
            Auth::id()
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda tidak dapat menghapus akun yang sedang digunakan.'
                );
        }

        $user->delete();

        return redirect()
            ->route(
                'dashboard.admin.kelola-user.index'
            )
            ->with(
                'success',
                'User berhasil dihapus.'
            );
    }

    /**
     * =========================================================
     * KELOLA STAFF (Profil Cabang/Loket, Hak Akses, Status Akun)
     * =========================================================
     */

    /**
     * Menampilkan daftar staf.
     */
    public function staffIndex(Request $request)
    {
        $user = Auth::user();

        $query = User::query();

        // Ambil akun dengan role staf
        $query->whereIn('role', ['staff', 'admin_staff', 'staf']);

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('branch', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('access_code', 'like', "%{$search}%");
            });
        }

        // Filter Status Kepegawaian (active = Aktif Bekerja, inactive = Berhenti Bekerja)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Penempatan Cabang / Loket
        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }

        $staffMembers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik Staf
        $totalStaff = User::whereIn('role', ['staff', 'admin_staff', 'staf'])->count();
        $activeStaff = User::whereIn('role', ['staff', 'admin_staff', 'staf'])->where('status', 'active')->count();
        $inactiveStaff = User::whereIn('role', ['staff', 'admin_staff', 'staf'])->where('status', 'inactive')->count();
        $availableBranches = User::defaultBranches();
        $uniqueBranchesCount = User::whereIn('role', ['staff', 'admin_staff', 'staf'])->whereNotNull('branch')->distinct('branch')->count('branch');

        return view(
            'dashboard.admin.kelola-staff.index',
            compact(
                'user',
                'staffMembers',
                'totalStaff',
                'activeStaff',
                'inactiveStaff',
                'availableBranches',
                'uniqueBranchesCount'
            )
        );
    }

    /**
     * Menampilkan form tambah staf baru.
     */
    public function createStaff()
    {
        $user = Auth::user();
        $availableBranches = User::defaultBranches();
        $availablePermissions = User::availablePermissions();

        return view(
            'dashboard.admin.kelola-staff.create',
            compact('user', 'availableBranches', 'availablePermissions')
        );
    }

    /**
     * Menyimpan data staf baru.
     */
    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'branch' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'access_code' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar pada sistem.',
            'phone.required' => 'Nomor kontak / WhatsApp wajib diisi.',
            'branch.required' => 'Penempatan cabang / loket wajib dipilih.',
            'password.required' => 'Kata sandi akun wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Default permission staf loket jika tidak dipilih
        $permissions = $request->input('permissions', [
            'create_booking',
            'view_routes',
            'view_passengers',
            'checkin_ticket',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'status' => 'active',
            'branch' => $validated['branch'],
            'position' => $validated['position'] ?? 'Staf Loket & Kasir',
            'access_code' => $validated['access_code'] ?? null,
            'permissions' => $permissions,
        ]);

        return redirect()
            ->route('dashboard.admin.kelola-staff.index')
            ->with('success', "Staf baru {$validated['name']} berhasil ditambahkan dan siap bertugas di {$validated['branch']}.");
    }

    /**
     * Menampilkan profil lengkap & wewenang staf.
     */
    public function showStaff(User $staff)
    {
        abort_if(! $staff->isStaff(), 404);

        $admin = Auth::user();
        $availablePermissions = User::availablePermissions();

        return view(
            'dashboard.admin.kelola-staff.show',
            compact('staff', 'admin', 'availablePermissions')
        );
    }

    /**
     * Menampilkan form edit profil & wewenang staf.
     */
    public function editStaff(User $staff)
    {
        abort_if(! $staff->isStaff(), 404);

        $admin = Auth::user();
        $availableBranches = User::defaultBranches();
        $availablePermissions = User::availablePermissions();

        return view(
            'dashboard.admin.kelola-staff.edit',
            compact('staff', 'admin', 'availableBranches', 'availablePermissions')
        );
    }

    /**
     * Memperbarui profil staf & hak akses wewenang.
     */
    public function updateStaff(Request $request, User $staff)
    {
        abort_if(! $staff->isStaff(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone' => ['required', 'string', 'max:20'],
            'branch' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'access_code' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'phone.required' => 'Nomor kontak / WhatsApp wajib diisi.',
            'branch.required' => 'Penempatan cabang / loket wajib dipilih.',
            'status.required' => 'Status kepegawaian wajib ditentukan.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->phone = $validated['phone'];
        $staff->branch = $validated['branch'];
        $staff->position = $validated['position'] ?? $staff->position;
        $staff->access_code = $validated['access_code'] ?? $staff->access_code;
        $staff->status = $validated['status'];
        $staff->permissions = $request->input('permissions', []);

        if (! empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        return redirect()
            ->route('dashboard.admin.kelola-staff.index')
            ->with('success', "Data dan wewenang staf {$staff->name} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan / menonaktifkan akun staf yang berhenti bekerja.
     */
    public function toggleStaffStatus(User $staff)
    {
        abort_if(! $staff->isStaff(), 404);

        if ($staff->status === 'active') {
            $staff->status = 'inactive';
            $message = "Akun staf {$staff->name} berhasil dinonaktifkan (Berhenti Bekerja). Staf ini tidak dapat lagi mengakses sistem.";
        } else {
            $staff->status = 'active';
            $message = "Akun staf {$staff->name} berhasil diaktifkan kembali dan dapat bertugas.";
        }

        $staff->save();

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Reset kata sandi staf secara manual oleh admin utama.
     */
    public function resetStaffPassword(Request $request, User $staff)
    {
        abort_if(! $staff->isStaff(), 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $staff->password = Hash::make($validated['password']);
        $staff->save();

        return redirect()
            ->back()
            ->with('success', "Kata sandi untuk staf {$staff->name} berhasil direset.");
    }

    /**
     * Menghapus staf.
     */
    public function destroyStaff(User $staff)
    {
        abort_if(! $staff->isStaff(), 404);

        if ($staff->id === Auth::id()) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $staff->name;
        $staff->delete();

        return redirect()
            ->route('dashboard.admin.kelola-staff.index')
            ->with('success', "Data staf {$name} berhasil dihapus dari sistem.");
    }

    /**
     * =========================================================
     * KELOLA DRIVER (Profil Legalitas, Status Kerja, Riwayat)
     * =========================================================
     */

    /**
     * Menampilkan daftar driver.
     */
    public function driverIndex(Request $request)
    {
        $user = Auth::user();
        $query = Driver::with('user');

        // Pencarian (nama user, phone, nomor SIM, emergency contact, email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%")
                    ->orWhere('emergency_contact_name', 'like', "%{$search}%")
                    ->orWhere('emergency_contact_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status kerja: standby, on_duty, on_leave
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            if ($statusFilter === 'standby') {
                $query->whereIn('status', ['standby', 'Available', 'available']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        // Filter legalitas SIM
        if ($request->filled('license_status')) {
            $licStatus = $request->license_status;
            $today = now()->startOfDay();
            if ($licStatus === 'expired') {
                $query->where('license_expired_at', '<', $today);
            } elseif ($licStatus === 'expiring') {
                $query->whereBetween('license_expired_at', [$today, $today->copy()->addDays(30)]);
            } elseif ($licStatus === 'valid') {
                $query->where('license_expired_at', '>', $today->copy()->addDays(30));
            }
        }

        $drivers = $query->latest()->paginate(10)->withQueryString();

        // Statistik
        $totalDrivers = Driver::count();
        $standbyDrivers = Driver::whereIn('status', ['standby', 'Available', 'available'])->count();
        $onDutyDrivers = Driver::where('status', 'on_duty')->count();
        $onLeaveDrivers = Driver::whereIn('status', ['on_leave', 'leave'])->count();
        $expiringLicenseDrivers = Driver::whereBetween('license_expired_at', [now()->startOfDay(), now()->startOfDay()->addDays(30)])->count();
        $expiredLicenseDrivers = Driver::where('license_expired_at', '<', now()->startOfDay())->count();

        return view('dashboard.admin.kelola-driver.index', compact(
            'user',
            'drivers',
            'totalDrivers',
            'standbyDrivers',
            'onDutyDrivers',
            'onLeaveDrivers',
            'expiringLicenseDrivers',
            'expiredLicenseDrivers'
        ));
    }

    /**
     * Menampilkan formulir pendaftaran driver baru.
     */
    public function createDriver()
    {
        $user = Auth::user();

        return view('dashboard.admin.kelola-driver.create', compact('user'));
    }

    /**
     * Menyimpan data driver baru beserta akun login dan foto.
     */
    public function storeDriver(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'license_number' => ['required', 'string', 'max:50', 'unique:drivers,license_number'],
            'license_type' => ['required', 'string', 'max:50'],
            'license_expired_at' => ['required', 'date'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['standby', 'on_duty', 'on_leave'])],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap pengemudi wajib diisi.',
            'email.required' => 'Alamat email login wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'phone.required' => 'Nomor WhatsApp pengemudi wajib diisi.',
            'password.required' => 'Kata sandi login wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'license_number.required' => 'Nomor SIM wajib diisi.',
            'license_number.unique' => 'Nomor SIM ini sudah terdaftar pada sistem.',
            'license_type.required' => 'Jenis SIM wajib dipilih.',
            'license_expired_at.required' => 'Masa berlaku SIM wajib diisi.',
            'emergency_contact_name.required' => 'Nama kontak darurat keluarga wajib diisi.',
            'emergency_contact_phone.required' => 'Nomor telepon kontak darurat wajib diisi.',
            'emergency_contact_relation.required' => 'Hubungan kontak darurat keluarga wajib diisi.',
            'status.required' => 'Status kerja pengemudi wajib ditentukan.',
            'photo.image' => 'File foto harus berupa gambar valid.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('drivers', 'public');
        }

        $userDriver = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
            'status' => 'active',
        ]);

        Driver::create([
            'user_id' => $userDriver->id,
            'phone' => $validated['phone'],
            'photo' => $photoPath,
            'license_number' => $validated['license_number'],
            'license_type' => $validated['license_type'],
            'license_expired_at' => $validated['license_expired_at'],
            'emergency_contact_name' => $validated['emergency_contact_name'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
            'emergency_contact_relation' => $validated['emergency_contact_relation'],
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('dashboard.admin.kelola-driver.index')
            ->with('success', "Driver baru {$validated['name']} berhasil ditambahkan dengan nomor SIM {$validated['license_number']}.");
    }

    /**
     * Menampilkan detail profil, legalitas SIM, dan riwayat penugasan armada minibus.
     */
    public function showDriver(Driver $driver)
    {
        $admin = Auth::user();
        $driver->load(['user', 'schedules.vehicle', 'schedules.route.origin', 'schedules.route.destination']);

        // Riwayat penugasan (rute dan armada minibus)
        $schedules = $driver->schedules()
            ->with(['vehicle', 'route.origin', 'route.destination', 'travelRoute'])
            ->latest()
            ->paginate(8);

        return view('dashboard.admin.kelola-driver.show', compact('driver', 'admin', 'schedules'));
    }

    /**
     * Menampilkan formulir edit driver.
     */
    public function editDriver(Driver $driver)
    {
        $admin = Auth::user();
        $driver->load('user');

        return view('dashboard.admin.kelola-driver.edit', compact('driver', 'admin'));
    }

    /**
     * Memperbarui profil & legalitas driver.
     */
    public function updateDriver(Request $request, Driver $driver)
    {
        $driver->load('user');
        $userId = $driver->user_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'license_number' => ['required', 'string', 'max:50', Rule::unique('drivers', 'license_number')->ignore($driver->id)],
            'license_type' => ['required', 'string', 'max:50'],
            'license_expired_at' => ['required', 'date'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['standby', 'on_duty', 'on_leave'])],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap pengemudi wajib diisi.',
            'email.required' => 'Alamat email login wajib diisi.',
            'phone.required' => 'Nomor WhatsApp pengemudi wajib diisi.',
            'license_number.required' => 'Nomor SIM wajib diisi.',
            'license_type.required' => 'Jenis SIM wajib dipilih.',
            'license_expired_at.required' => 'Masa berlaku SIM wajib diisi.',
            'emergency_contact_name.required' => 'Nama kontak darurat keluarga wajib diisi.',
            'emergency_contact_phone.required' => 'Nomor telepon kontak darurat wajib diisi.',
            'emergency_contact_relation.required' => 'Hubungan kontak darurat keluarga wajib diisi.',
            'status.required' => 'Status kerja pengemudi wajib ditentukan.',
            'photo.image' => 'File foto harus berupa gambar valid.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($driver->user) {
            $driver->user->name = $validated['name'];
            $driver->user->email = $validated['email'];
            $driver->user->phone = $validated['phone'];
            if (! empty($validated['password'])) {
                $driver->user->password = Hash::make($validated['password']);
            }
            $driver->user->save();
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('drivers', 'public');
            $driver->photo = $photoPath;
        }

        $driver->phone = $validated['phone'];
        $driver->license_number = $validated['license_number'];
        $driver->license_type = $validated['license_type'];
        $driver->license_expired_at = $validated['license_expired_at'];
        $driver->emergency_contact_name = $validated['emergency_contact_name'];
        $driver->emergency_contact_phone = $validated['emergency_contact_phone'];
        $driver->emergency_contact_relation = $validated['emergency_contact_relation'];
        $driver->address = $validated['address'] ?? null;
        $driver->notes = $validated['notes'] ?? null;
        $driver->status = $validated['status'];
        $driver->save();

        return redirect()
            ->route('dashboard.admin.kelola-driver.show', $driver->id)
            ->with('success', "Data dan legalitas pengemudi {$driver->user?->name} berhasil diperbarui.");
    }

    /**
     * Memperbarui status kerja driver secara cepat.
     */
    public function updateDriverStatus(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['standby', 'on_duty', 'on_leave'])],
        ]);

        $driver->status = $validated['status'];
        $driver->save();

        $statusName = $driver->statusLabel();

        return redirect()
            ->back()
            ->with('success', "Status kerja driver {$driver->user?->name} berhasil diubah menjadi: {$statusName}.");
    }

    /**
     * Menghapus data driver.
     */
    public function destroyDriver(Driver $driver)
    {
        $name = $driver->user?->name ?? "Driver #{$driver->id}";
        $user = $driver->user;

        $driver->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()
            ->route('dashboard.admin.kelola-driver.index')
            ->with('success', "Data pengemudi {$name} berhasil dihapus dari sistem.");
    }

    /**
     * =========================================================
     * MEMANTAU KENDARAAN (FLEET MONITORING)
     * =========================================================
     */

    /**
     * Menampilkan daftar armada kendaraan dan pemantauan kelaikan.
     */
    public function vehicleIndex(Request $request)
    {
        $user = Auth::user();

        // 1. Statistik ringkas armada
        $totalVehicles = Vehicle::count();
        $readyVehicles = Vehicle::whereIn('status', ['siap_jalan', 'Available', 'ready', 'available'])->count();
        $operatingVehicles = Vehicle::whereIn('status', ['beroperasi', 'On Duty', 'operating', 'in_trip', 'departed'])->count();
        $maintenanceVehicles = Vehicle::whereIn('status', ['maintenance', 'Maintenance', 'Perbaikan', 'perbaikan', 'bengkel'])->count();

        // Dokumen mendekati jatuh tempo (<= 30 hari) atau sudah kedaluwarsa
        $warningDate = now()->addDays(30);
        $legalityAlertCount = Vehicle::where(function ($q) use ($warningDate) {
            $q->whereNotNull('kir_expiry_date')->where('kir_expiry_date', '<=', $warningDate);
        })->orWhere(function ($q) use ($warningDate) {
            $q->whereNotNull('stnk_tax_due_date')->where('stnk_tax_due_date', '<=', $warningDate);
        })->count();

        // 2. Query data kendaraan dengan filter
        $query = Vehicle::query()->withCount(['schedules', 'seats']);

        // Filter Pencarian (Plat Nomor atau Nama Kendaraan)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Filter Status Operasional
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'siap_jalan') {
                $query->whereIn('status', ['siap_jalan', 'Available', 'ready', 'available']);
            } elseif ($status === 'beroperasi') {
                $query->whereIn('status', ['beroperasi', 'On Duty', 'operating', 'in_trip', 'departed']);
            } elseif ($status === 'maintenance') {
                $query->whereIn('status', ['maintenance', 'Maintenance', 'Perbaikan', 'perbaikan', 'bengkel']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter Tipe Kendaraan
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter Legalitas & Kelaikan
        if ($request->filled('legality') && $request->legality !== 'all') {
            $legality = $request->legality;
            $today = now()->startOfDay();
            if ($legality === 'kir_expiring') {
                $query->whereNotNull('kir_expiry_date')
                    ->whereBetween('kir_expiry_date', [$today, $today->copy()->addDays(30)]);
            } elseif ($legality === 'stnk_expiring') {
                $query->whereNotNull('stnk_tax_due_date')
                    ->whereBetween('stnk_tax_due_date', [$today, $today->copy()->addDays(30)]);
            } elseif ($legality === 'expired') {
                $query->where(function ($q) use ($today) {
                    $q->where(function ($sub) use ($today) {
                        $sub->whereNotNull('kir_expiry_date')->where('kir_expiry_date', '<', $today);
                    })->orWhere(function ($sub) use ($today) {
                        $sub->whereNotNull('stnk_tax_due_date')->where('stnk_tax_due_date', '<', $today);
                    });
                });
            }
        }

        $vehicles = $query->orderBy('plate_number')->paginate(10)->withQueryString();

        $types = Vehicle::availableTypes();
        $statuses = Vehicle::availableStatuses();

        return view('dashboard.admin.memantau-kendaraan.index', compact(
            'user',
            'vehicles',
            'totalVehicles',
            'readyVehicles',
            'operatingVehicles',
            'maintenanceVehicles',
            'legalityAlertCount',
            'types',
            'statuses'
        ));
    }

    /**
     * Menampilkan formulir pendaftaran unit armada baru.
     */
    public function createVehicle()
    {
        $user = Auth::user();
        $types = Vehicle::availableTypes();
        $statuses = Vehicle::availableStatuses();
        $availableFacilities = Vehicle::availableFacilities();

        return view('dashboard.admin.memantau-kendaraan.create', compact(
            'user',
            'types',
            'statuses',
            'availableFacilities'
        ));
    }

    /**
     * Menyimpan armada baru ke database.
     */
    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:4', 'max:60'],
            'manufacture_year' => ['nullable', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'chassis_number' => ['nullable', 'string', 'max:100'],
            'engine_number' => ['nullable', 'string', 'max:100'],
            'stnk_tax_due_date' => ['nullable', 'date'],
            'kir_expiry_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['siap_jalan', 'beroperasi', 'maintenance', 'Available', 'Maintenance'])],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('vehicles', 'public');
        }

        $vehicle = Vehicle::create([
            'plate_number' => strtoupper(trim($validated['plate_number'])),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'capacity' => $validated['capacity'],
            'manufacture_year' => $validated['manufacture_year'] ?? null,
            'chassis_number' => $validated['chassis_number'] ?? null,
            'engine_number' => $validated['engine_number'] ?? null,
            'stnk_tax_due_date' => $validated['stnk_tax_due_date'] ?? null,
            'kir_expiry_date' => $validated['kir_expiry_date'] ?? null,
            'status' => $validated['status'],
            'facilities' => $validated['facilities'] ?? [],
            'photo' => $photoPath,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('dashboard.admin.memantau-kendaraan.show', $vehicle->id)
            ->with('success', "Unit armada {$vehicle->plate_number} ({$vehicle->name}) berhasil ditambahkan ke sistem.");
    }

    /**
     * Menampilkan detail informasi dan kelaikan unit kendaraan.
     */
    public function showVehicle(Vehicle $vehicle)
    {
        $user = Auth::user();

        $vehicle->load([
            'seats',
            'schedules' => function ($q) {
                $q->with(['route.origin', 'route.destination', 'travelRoute', 'driver.user', 'bookings'])
                    ->orderByDesc('departure_date')
                    ->orderByDesc('departure_time')
                    ->take(10);
            },
        ]);

        $kirStatus = $vehicle->kirStatus();
        $stnkStatus = $vehicle->stnkStatus();
        $statuses = Vehicle::availableStatuses();

        return view('dashboard.admin.memantau-kendaraan.show', compact(
            'user',
            'vehicle',
            'kirStatus',
            'stnkStatus',
            'statuses'
        ));
    }

    /**
     * Menampilkan formulir edit unit armada.
     */
    public function editVehicle(Vehicle $vehicle)
    {
        $user = Auth::user();
        $types = Vehicle::availableTypes();
        $statuses = Vehicle::availableStatuses();
        $availableFacilities = Vehicle::availableFacilities();

        return view('dashboard.admin.memantau-kendaraan.edit', compact(
            'user',
            'vehicle',
            'types',
            'statuses',
            'availableFacilities'
        ));
    }

    /**
     * Memperbarui data unit armada.
     */
    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:4', 'max:60'],
            'manufacture_year' => ['nullable', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'chassis_number' => ['nullable', 'string', 'max:100'],
            'engine_number' => ['nullable', 'string', 'max:100'],
            'stnk_tax_due_date' => ['nullable', 'date'],
            'kir_expiry_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['siap_jalan', 'beroperasi', 'maintenance', 'Available', 'Maintenance'])],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('photo')) {
            if ($vehicle->photo && Storage::disk('public')->exists($vehicle->photo)) {
                Storage::disk('public')->delete($vehicle->photo);
            }
            $vehicle->photo = $request->file('photo')->store('vehicles', 'public');
        }

        $vehicle->plate_number = strtoupper(trim($validated['plate_number']));
        $vehicle->name = $validated['name'];
        $vehicle->type = $validated['type'];
        $vehicle->capacity = $validated['capacity'];
        $vehicle->manufacture_year = $validated['manufacture_year'] ?? null;
        $vehicle->chassis_number = $validated['chassis_number'] ?? null;
        $vehicle->engine_number = $validated['engine_number'] ?? null;
        $vehicle->stnk_tax_due_date = $validated['stnk_tax_due_date'] ?? null;
        $vehicle->kir_expiry_date = $validated['kir_expiry_date'] ?? null;
        $vehicle->status = $validated['status'];
        $vehicle->facilities = $validated['facilities'] ?? [];
        $vehicle->notes = $validated['notes'] ?? null;
        $vehicle->save();

        return redirect()
            ->route('dashboard.admin.memantau-kendaraan.show', $vehicle->id)
            ->with('success', "Data dan kelaikan armada {$vehicle->plate_number} berhasil diperbarui.");
    }

    /**
     * Memperbarui status unit armada secara cepat (Quick Status Update).
     */
    public function updateVehicleStatus(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['siap_jalan', 'beroperasi', 'maintenance', 'Available', 'Maintenance'])],
        ]);

        $vehicle->status = $validated['status'];
        $vehicle->save();

        $statusLabel = $vehicle->statusLabel();

        return redirect()
            ->back()
            ->with('success', "Status operasional unit {$vehicle->plate_number} berhasil diubah menjadi: {$statusLabel}.");
    }

    /**
     * Menghapus data unit armada kendaraan.
     */
    public function destroyVehicle(Vehicle $vehicle)
    {
        $plate = $vehicle->plate_number;

        // Cek apakah masih memiliki jadwal perjalanan yang belum selesai
        $hasActiveSchedules = Schedule::where('vehicle_id', $vehicle->id)
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['Completed', 'completed', 'Cancelled', 'cancelled'])
            ->exists();

        if ($hasActiveSchedules) {
            return redirect()
                ->back()
                ->with('error', "Unit {$plate} tidak dapat dihapus karena masih memiliki jadwal keberangkatan aktif atau mendatang.");
        }

        if ($vehicle->photo && Storage::disk('public')->exists($vehicle->photo)) {
            Storage::disk('public')->delete($vehicle->photo);
        }

        // Hapus relasi kursi
        $vehicle->seats()->delete();
        $vehicle->delete();

        return redirect()
            ->route('dashboard.admin.memantau-kendaraan.index')
            ->with('success', "Unit armada {$plate} berhasil dihapus dari sistem.");
    }

    /**
     * =========================================================
     * PEMESANAN TIKET & OPERASIONAL (BOOKING & TICKETING)
     * =========================================================
     */

    /**
     * Menampilkan daftar semua pemesanan tiket dengan filter dan statistik.
     */
    public function bookingIndex(Request $request)
    {
        $user = Auth::user();

        // Metrik Ringkas Pemesanan
        $today = now()->startOfDay();
        $totalBookings = Booking::count();
        $todayPaidBookings = Booking::whereDate('created_at', $today)
            ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success', 'lunas'])
            ->count();
        $pendingBookings = Booking::whereIn('status', ['Pending', 'pending'])->count();
        $walkInBookings = Booking::whereIn('source', ['walk_in', 'whatsapp', 'phone', 'loket'])->count();

        $query = Booking::with([
            'user',
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.travelRoute',
            'schedule.vehicle',
            'passengers.seat',
            'payment',
            'ticket',
        ]);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('passengers', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Status Pembayaran
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'paid') {
                $query->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success', 'lunas']);
            } elseif ($status === 'pending') {
                $query->whereIn('status', ['Pending', 'pending']);
            } elseif ($status === 'cancelled') {
                $query->whereIn('status', ['Cancelled', 'cancelled']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter Sumber Pemesanan
        if ($request->filled('source') && $request->source !== 'all') {
            $source = $request->source;
            if ($source === 'walk_in') {
                $query->whereIn('source', ['walk_in', 'loket']);
            } elseif ($source === 'whatsapp') {
                $query->whereIn('source', ['whatsapp', 'wa']);
            } else {
                $query->where('source', $source);
            }
        }

        // Filter Jadwal Tertentu
        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.admin.pemesanan.index', compact(
            'user',
            'bookings',
            'totalBookings',
            'todayPaidBookings',
            'pendingBookings',
            'walkInBookings'
        ));
    }

    /**
     * Menampilkan formulir pendaftaran tiket manual di loket / via telepon/WhatsApp (Walk-in).
     */
    public function createManualBooking(Request $request)
    {
        $user = Auth::user();

        // Mengambil jadwal keberangkatan mendatang yang aktif
        $schedules = Schedule::with([
            'route.origin',
            'route.destination',
            'travelRoute',
            'vehicle',
            'driver.user',
        ])
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['Completed', 'completed', 'Cancelled', 'cancelled'])
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(50)
            ->get();

        $selectedSchedule = null;
        $occupancyMap = [];

        if ($request->filled('schedule_id')) {
            $selectedSchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'travelRoute',
                'vehicle',
                'driver.user',
            ])->find($request->schedule_id);

            if ($selectedSchedule) {
                $occupancyMap = $selectedSchedule->getSeatOccupancyMap();
            }
        }

        return view('dashboard.admin.pemesanan.create-manual', compact(
            'user',
            'schedules',
            'selectedSchedule',
            'occupancyMap'
        ));
    }

    /**
     * Menyimpan pemesanan tiket manual loket / WhatsApp ke database.
     */
    public function storeManualBooking(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'selected_seats' => ['required', 'array', 'min:1'],
            'selected_seats.*' => ['integer', 'exists:seats,id'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'source' => ['required', Rule::in(['walk_in', 'whatsapp', 'phone'])],
            'payment_method' => ['required', Rule::in(['Cash', 'Transfer Bank', 'QRIS'])],
            'payment_status' => ['required', Rule::in(['paid', 'pending'])],
            'notes' => ['nullable', 'string', 'max:500'],
            'passengers' => ['nullable', 'array'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Pessimistic lock pada jadwal dan baris kursi terpilih
            $schedule = Schedule::where('id', $validated['schedule_id'])
                ->with(['route', 'vehicle'])
                ->lockForUpdate()
                ->firstOrFail();

            Seat::whereIn('id', $validated['selected_seats'])->lockForUpdate()->get();

            // Validasi tabrakan kursi: pastikan kursi belum terisi di jadwal ini
            $alreadyBooked = BookingPassenger::whereHas('booking', function ($q) use ($schedule) {
                $q->where('schedule_id', $schedule->id)
                    ->whereNotIn('status', ['Cancelled', 'cancelled']);
            })->whereIn('seat_id', $validated['selected_seats'])->lockForUpdate()->exists();

            if ($alreadyBooked) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Salah satu atau lebih kursi yang Anda pilih telah dipesan oleh penumpang lain. Silakan pilih kursi kosong.');
            }

            // Cari atau buat akun user pelanggan
            $phone = trim($validated['customer_phone']);
            $customerUser = User::where('phone', $phone)->first();
            if (! $customerUser && ! empty($validated['customer_email'])) {
                $customerUser = User::where('email', $validated['customer_email'])->first();
            }

            if (! $customerUser) {
                $email = ! empty($validated['customer_email'])
                    ? $validated['customer_email']
                    : ('guest_'.time().'_'.rand(100, 999).'@travel.test');

                $customerUser = User::create([
                    'name' => $validated['customer_name'],
                    'phone' => $phone,
                    'email' => $email,
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'user',
                    'status' => 'active',
                ]);
            }

            // Hitung tarif tiket
            $routePrice = $schedule->route?->price ?? 100000;
            $passengerCount = count($validated['selected_seats']);
            $totalPrice = $routePrice * $passengerCount;

            // Generate kode booking unik
            $bookingCode = 'TRV-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999);

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $customerUser->id,
                'schedule_id' => $schedule->id,
                'total_passenger' => $passengerCount,
                'subtotal' => $totalPrice,
                'discount' => 0,
                'total_price' => $totalPrice,
                'status' => $validated['payment_status'] === 'paid' ? 'Paid' : 'Pending',
                'source' => $validated['source'],
                'notes' => $validated['notes'] ?? null,
                'expired_at' => now()->addHours(24),
            ]);

            // Buat data penumpang per kursi
            foreach ($validated['selected_seats'] as $idx => $seatId) {
                $seat = Seat::find($seatId);
                $pName = $request->input("passengers.{$seatId}.name")
                    ?: $request->input("passengers.{$idx}.name")
                    ?: ($idx === 0 ? $validated['customer_name'] : ('Penumpang '.($seat?->seat_number ?? ($idx + 1))));

                $pPhone = $request->input("passengers.{$seatId}.phone")
                    ?: $request->input("passengers.{$idx}.phone")
                    ?: $phone;

                $pSpecial = $request->input("passengers.{$seatId}.special_request")
                    ?: $request->input("passengers.{$idx}.special_request")
                    ?: ($validated['notes'] ?? null);

                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seatId,
                    'name' => $pName,
                    'phone' => $pPhone,
                    'special_request' => $pSpecial,
                ]);
            }

            // Catat Pembayaran
            Payment::create([
                'booking_id' => $booking->id,
                'order_id' => 'ORD-'.$booking->booking_code,
                'payment_method' => $validated['payment_method'],
                'gross_amount' => $totalPrice,
                'status' => $validated['payment_status'] === 'paid' ? 'Success' : 'Pending',
                'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
            ]);

            // Terbitkan Tiket jika sudah lunas
            if ($validated['payment_status'] === 'paid') {
                Ticket::create([
                    'booking_id' => $booking->id,
                    'ticket_number' => 'TKT-'.strtoupper(Str::random(8)),
                    'qr_code' => 'TICKET-'.$booking->booking_code,
                    'status' => 'Issued',
                ]);
            }

            // Perbarui sisa kursi jadwal
            $schedule->recalculateAvailableSeats();

            return redirect()
                ->route('dashboard.admin.pemesanan.show', $booking->id)
                ->with('success', "Pemesanan manual {$booking->booking_code} untuk {$passengerCount} penumpang berhasil dibuat.");
        });
    }

    /**
     * Menampilkan detail pemesanan dan e-ticket penumpang.
     */
    public function showBooking(Booking $booking)
    {
        $user = Auth::user();

        $booking->load([
            'user',
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.travelRoute',
            'schedule.vehicle',
            'schedule.driver.user',
            'passengers.seat',
            'payment',
            'ticket',
        ]);

        return view('dashboard.admin.pemesanan.show', compact('user', 'booking'));
    }

    /**
     * Menampilkan denah kursi visual (Seat Map) interaktif untuk jadwal perjalanan tertentu.
     */
    public function bookingSeatMap(Schedule $schedule)
    {
        $user = Auth::user();

        $schedule->load([
            'route.origin',
            'route.destination',
            'travelRoute',
            'vehicle',
            'driver.user',
        ]);

        $occupancyMap = $schedule->getSeatOccupancyMap();

        // Hitung ringkasan kursi
        $totalSeats = $schedule->vehicle ? $schedule->vehicle->capacity : 10;
        $occupiedCount = 0;
        $pendingCount = 0;
        $availableCount = 0;

        foreach ($occupancyMap as $rowSeats) {
            foreach ($rowSeats as $seatData) {
                if ($seatData['occupancy']['status'] === 'paid') {
                    $occupiedCount++;
                } elseif ($seatData['occupancy']['status'] === 'pending') {
                    $pendingCount++;
                } else {
                    $availableCount++;
                }
            }
        }

        return view('dashboard.admin.pemesanan.seat-map', compact(
            'user',
            'schedule',
            'occupancyMap',
            'totalSeats',
            'occupiedCount',
            'pendingCount',
            'availableCount'
        ));
    }

    /**
     * Menampilkan manifes perjalanan lengkap yang siap dicetak dan dikirim via WhatsApp ke sopir.
     */
    public function tripManifest(Schedule $schedule)
    {
        $user = Auth::user();

        $schedule->load([
            'route.origin',
            'route.destination',
            'travelRoute',
            'vehicle',
            'driver.user',
            'bookings' => function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled'])
                    ->with(['user', 'passengers.seat', 'payment', 'ticket']);
            },
        ]);

        // Ekstraksi seluruh penumpang pada jadwal ini diurutkan berdasarkan nomor kursi
        $passengers = collect();
        foreach ($schedule->bookings as $b) {
            foreach ($b->passengers as $p) {
                $passengers->push([
                    'seat_number' => $p->seat?->seat_number ?? '-',
                    'passenger_name' => $p->name,
                    'phone' => $p->phone ?: ($b->user?->phone ?? '-'),
                    'booking_code' => $b->booking_code,
                    'booking_id' => $b->id,
                    'payment_status' => $b->statusLabel(),
                    'is_paid' => in_array(strtolower($b->status), ['paid', 'confirmed', 'success', 'lunas']),
                    'special_request' => $p->special_request ?: ($b->notes ?: '-'),
                    'ticket_number' => $b->ticket?->ticket_number ?? '-',
                    'booking_source' => $b->sourceLabel(),
                ]);
            }
        }

        $sortedPassengers = $passengers->sortBy('seat_number')->values();

        return view('dashboard.admin.pemesanan.manifes', compact(
            'user',
            'schedule',
            'sortedPassengers'
        ));
    }

    /**
     * Menampilkan formulir ubah jadwal (Reschedule) dan geser kursi.
     */
    public function rescheduleBookingForm(Booking $booking)
    {
        $user = Auth::user();

        if (strtolower($booking->status) === 'cancelled') {
            return redirect()
                ->route('dashboard.admin.pemesanan.show', $booking->id)
                ->with('error', 'Pemesanan yang telah dibatalkan tidak dapat diubah jadwalnya (reschedule).');
        }

        $booking->load([
            'user',
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.travelRoute',
            'schedule.vehicle',
            'passengers.seat',
        ]);

        // Cari jadwal alternatif dengan rute yang sama atau tanggal mendatang
        $originId = $booking->schedule?->route?->origin_id;
        $destId = $booking->schedule?->route?->destination_id;

        $availableSchedules = Schedule::with([
            'route.origin',
            'route.destination',
            'travelRoute',
            'vehicle',
            'driver.user',
        ])
            ->where('id', '!=', $booking->schedule_id)
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['Completed', 'completed', 'Cancelled', 'cancelled'])
            ->where('available_seats', '>=', $booking->total_passenger)
            ->when($originId && $destId, function ($q) use ($originId, $destId) {
                $q->whereHas('route', function ($r) use ($originId, $destId) {
                    $r->where('origin_id', $originId)->where('destination_id', $destId);
                });
            })
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        return view('dashboard.admin.pemesanan.reschedule', compact(
            'user',
            'booking',
            'availableSchedules'
        ));
    }

    /**
     * Memproses perubahan jadwal (Reschedule) dan pembaruan alokasi kursi.
     */
    public function updateRescheduleBooking(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'new_schedule_id' => ['required', 'exists:schedules,id'],
            'new_seats' => ['required', 'array'],
            'new_seats.*' => ['integer', 'exists:seats,id'],
            'reschedule_reason' => ['required', 'string', 'max:255'],
        ]);

        if (count($validated['new_seats']) !== $booking->total_passenger) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Jumlah kursi baru harus sama dengan jumlah penumpang ({$booking->total_passenger} kursi).");
        }

        $newSchedule = Schedule::with(['route', 'vehicle'])->findOrFail($validated['new_schedule_id']);
        $oldSchedule = $booking->schedule;

        return DB::transaction(function () use ($validated, $booking, $newSchedule, $oldSchedule) {
            // Pastikan kursi baru belum terisi di jadwal baru
            $alreadyBooked = BookingPassenger::whereHas('booking', function ($q) use ($newSchedule) {
                $q->where('schedule_id', $newSchedule->id)
                    ->whereNotIn('status', ['Cancelled', 'cancelled']);
            })->whereIn('seat_id', $validated['new_seats'])->exists();

            if ($alreadyBooked) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Salah satu kursi yang dipilih pada jadwal baru sudah terisi. Silakan pilih kursi lain.');
            }

            // Catat log reschedule
            $oldInfo = ($oldSchedule->route?->origin?->name ?? '').' → '.($oldSchedule->route?->destination?->name ?? '').', '.($oldSchedule->departure_date ? $oldSchedule->departure_date->format('d/m/Y') : '').' '.$oldSchedule->departure_time;
            $newInfo = ($newSchedule->route?->origin?->name ?? '').' → '.($newSchedule->route?->destination?->name ?? '').', '.($newSchedule->departure_date ? $newSchedule->departure_date->format('d/m/Y') : '').' '.$newSchedule->departure_time;

            $notesLog = trim(($booking->notes ? $booking->notes."\n" : '').'[Reschedule '.now()->format('d/m/Y H:i')."] Dari: {$oldInfo} Menjadi: {$newInfo}. Alasan: {$validated['reschedule_reason']}");

            // Update kursi penumpang ke kursi baru
            foreach ($booking->passengers as $idx => $passenger) {
                if (isset($validated['new_seats'][$idx])) {
                    $passenger->seat_id = $validated['new_seats'][$idx];
                    $passenger->save();
                }
            }

            // Pindahkan jadwal booking
            $booking->schedule_id = $newSchedule->id;
            $booking->rescheduled_at = now();
            $booking->reschedule_reason = $validated['reschedule_reason'];
            $booking->notes = $notesLog;
            $booking->save();

            // Hitung ulang kuota kursi kedua jadwal
            if ($oldSchedule) {
                $oldSchedule->recalculateAvailableSeats();
            }
            $newSchedule->recalculateAvailableSeats();

            return redirect()
                ->route('dashboard.admin.pemesanan.show', $booking->id)
                ->with('success', "Jadwal keberangkatan berhasil diubah ke {$newInfo}. Kursi penumpang telah diperbarui.");
        });
    }

    /**
     * Membatalkan pemesanan dan melepaskan kursi.
     */
    public function cancelBooking(Request $request, Booking $booking)
    {
        if (strtolower($booking->status) === 'cancelled') {
            return redirect()->back()->with('error', 'Pemesanan ini sudah dalam status dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'Cancelled';
            $booking->save();

            if ($booking->ticket) {
                $booking->ticket->status = 'Cancelled';
                $booking->ticket->save();
            }

            if ($booking->schedule) {
                $booking->schedule->recalculateAvailableSeats();
            }
        });

        return redirect()
            ->back()
            ->with('success', "Pemesanan {$booking->booking_code} berhasil dibatalkan dan kursi telah dikembalikan ke status kosong.");
    }

    /*
    |--------------------------------------------------------------------------
    | Modul Pembayaran (Invoicing, Verifikasi Manual Bukti Transfer, Gateway Log)
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan daftar transaksi pembayaran, metrik omzet, dan filter pencarian.
     */
    public function paymentIndex(Request $request)
    {
        $user = Auth::user();

        // 4 Kartu Metrik Keuangan
        $totalTransactions = Payment::count();
        $totalRevenue = Payment::whereIn('status', ['Success', 'Paid', 'paid', 'success', 'lunas'])->sum('gross_amount');
        $pendingVerificationCount = Payment::whereIn('status', ['Pending', 'pending', 'menunggu'])
            ->where(function ($q) {
                $q->whereNotNull('proof_image')
                    ->orWhere('payment_method', 'like', '%transfer%')
                    ->orWhere('payment_method', 'like', '%bank%');
            })->count();
        $failedOrExpiredCount = Payment::whereIn('status', ['Failed', 'failed', 'gagal', 'Expired', 'expired'])->count();

        // Query Utama
        $query = Payment::with([
            'booking.user',
            'booking.passengers.seat',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.schedule.vehicle',
            'verifiedBy',
        ]);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($b) use ($search) {
                        $b->where('booking_code', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($u) use ($search) {
                                $u->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orWhereHas('passengers', function ($p) use ($search) {
                                $p->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter Status Pembayaran
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'paid') {
                $query->whereIn('status', ['Success', 'Paid', 'paid', 'success', 'lunas']);
            } elseif ($status === 'pending') {
                $query->whereIn('status', ['Pending', 'pending', 'menunggu']);
            } elseif ($status === 'expired') {
                $query->whereIn('status', ['Expired', 'expired']);
            } elseif ($status === 'failed') {
                $query->whereIn('status', ['Failed', 'failed', 'gagal', 'ditolak']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter Metode Pembayaran
        if ($request->filled('method') && $request->method !== 'all') {
            $method = $request->method;
            if ($method === 'transfer') {
                $query->where(function ($m) {
                    $m->where('payment_method', 'like', '%transfer%')
                        ->orWhere('payment_method', 'like', '%bank%')
                        ->orWhere('payment_method', 'like', '%rekening%');
                });
            } elseif ($method === 'qris') {
                $query->where('payment_method', 'like', '%qris%');
            } elseif ($method === 'cash') {
                $query->where(function ($m) {
                    $m->where('payment_method', 'like', '%cash%')
                        ->orWhere('payment_method', 'like', '%tunai%');
                });
            } elseif ($method === 'gateway') {
                $query->where(function ($m) {
                    $m->whereNotNull('gateway_provider')
                        ->orWhere('payment_method', 'like', '%midtrans%')
                        ->orWhere('payment_method', 'like', '%xendit%');
                });
            }
        }

        // Filter Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.admin.pembayaran.index', compact(
            'user',
            'payments',
            'totalTransactions',
            'totalRevenue',
            'pendingVerificationCount',
            'failedOrExpiredCount'
        ));
    }

    /**
     * Menampilkan detail faktur / invoice pembayaran dan status log payment gateway.
     */
    public function showPayment(Payment $payment)
    {
        $user = Auth::user();

        $payment->load([
            'booking.user',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.schedule.vehicle',
            'booking.schedule.driver.user',
            'booking.passengers.seat',
            'booking.ticket',
            'verifiedBy',
        ]);

        return view('dashboard.admin.pembayaran.show', compact('user', 'payment'));
    }

    /**
     * Verifikasi manual bukti transfer bank (Setujui / Tolak).
     */
    public function verifyPayment(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($validated, $payment) {
            $admin = Auth::user();

            if ($validated['action'] === 'approve') {
                $payment->status = 'Success';
                $payment->paid_at = now();
                $payment->verified_by = $admin ? $admin->id : null;
                $payment->verified_at = now();
                if (! empty($validated['notes'])) {
                    $payment->notes = $validated['notes'];
                }
                $payment->save();

                // Update status booking
                if ($payment->booking) {
                    $payment->booking->status = 'Paid';
                    $payment->booking->save();

                    // Terbitkan Tiket jika belum ada
                    if (! $payment->booking->ticket) {
                        Ticket::create([
                            'booking_id' => $payment->booking->id,
                            'ticket_number' => 'TKT-'.strtoupper(Str::random(8)),
                            'qr_code' => 'TICKET-'.$payment->booking->booking_code,
                            'status' => 'Issued',
                            'issued_at' => now(),
                        ]);
                    } else {
                        $payment->booking->ticket->status = 'Issued';
                        $payment->booking->ticket->save();
                    }

                    // Sinkronkan sisa kuota kursi
                    if ($payment->booking->schedule) {
                        $payment->booking->schedule->recalculateAvailableSeats();
                    }
                }

                return redirect()->back()->with('success', "Pembayaran untuk tagihan {$payment->order_id} berhasil diverifikasi (Lunas). E-Ticket perjalanan telah resmi diterbitkan.");
            } else {
                // Reject
                $reason = $validated['rejection_reason'] ?: ($validated['notes'] ?: 'Bukti transfer ditolak oleh admin karena tidak valid.');
                $payment->status = 'Failed';
                $payment->verified_by = $admin ? $admin->id : null;
                $payment->verified_at = now();
                $payment->notes = $reason;
                $payment->save();

                // Batalkan booking & lepaskan kursi jika diminta
                if ($payment->booking) {
                    $payment->booking->status = 'Cancelled';
                    $payment->booking->notes = trim(($payment->booking->notes ? $payment->booking->notes."\n" : '').'[Pembayaran Ditolak '.now()->format('d/m/Y H:i')."] {$reason}");
                    $payment->booking->save();

                    if ($payment->booking->ticket) {
                        $payment->booking->ticket->status = 'Cancelled';
                        $payment->booking->ticket->save();
                    }

                    if ($payment->booking->schedule) {
                        $payment->booking->schedule->recalculateAvailableSeats();
                    }
                }

                return redirect()->back()->with('error', "Pembayaran tagihan {$payment->order_id} telah ditolak. Alasan: {$reason}. Kursi telah dikembalikan ke status kosong.");
            }
        });
    }

    /**
     * Mengunggah atau memperbarui lampiran bukti transfer manual.
     */
    public function uploadPaymentProof(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'proof_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'proof_image_url' => ['nullable', 'string', 'max:500'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('payment-proofs', 'public');
            $payment->proof_image = $path;
        } elseif (! empty($validated['proof_image_url'])) {
            $payment->proof_image = $validated['proof_image_url'];
        }

        if (! empty($validated['bank_name'])) {
            $payment->bank_name = $validated['bank_name'];
        }
        if (! empty($validated['account_name'])) {
            $payment->account_name = $validated['account_name'];
        }
        if (! empty($validated['account_number'])) {
            $payment->account_number = $validated['account_number'];
        }
        if (! empty($validated['notes'])) {
            $payment->notes = $validated['notes'];
        }

        $payment->save();

        return redirect()->back()->with('success', "Bukti transfer dan data rekening untuk tagihan {$payment->order_id} berhasil disimpan.");
    }

    /**
     * Simulasi atau sinkronisasi status otomatis payment gateway (Midtrans / Xendit).
     */
    public function syncGatewayStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'gateway_provider' => ['nullable', 'string', 'in:midtrans,xendit,manual'],
            'gateway_status' => ['nullable', 'string', 'in:settlement,capture,pending,expire,deny,cancel'],
        ]);

        $provider = $validated['gateway_provider'] ?? ($payment->gateway_provider ?: 'midtrans');
        $status = $validated['gateway_status'] ?? 'settlement';

        $txId = $payment->transaction_id ?: ('TX-'.strtoupper(Str::random(10)));
        $currentTime = now()->toIso8601String();

        // Buat mock callback response sesuai format gateway
        $mockResponse = [
            'transaction_time' => $currentTime,
            'transaction_status' => $status,
            'transaction_id' => $txId,
            'status_message' => "Payment notification from {$provider}: status {$status}",
            'status_code' => in_array($status, ['settlement', 'capture']) ? '200' : (in_array($status, ['pending']) ? '201' : '407'),
            'signature_key' => hash('sha512', $payment->order_id.'200'.$payment->gross_amount.'secret_server_key'),
            'payment_type' => $payment->payment_method ?: 'bank_transfer',
            'order_id' => $payment->order_id,
            'merchant_id' => 'G'.rand(100000, 999999),
            'gross_amount' => (string) $payment->gross_amount,
            'currency' => 'IDR',
            'channel_response_message' => 'Approved by acquirer',
            'simulated_at' => $currentTime,
            'callback_received_at' => now()->format('Y-m-d H:i:s'),
        ];

        return DB::transaction(function () use ($payment, $provider, $status, $txId, $mockResponse) {
            $payment->gateway_provider = $provider;
            $payment->gateway_status = $status;
            $payment->transaction_id = $txId;
            $payment->raw_response = $mockResponse;

            if (in_array($status, ['settlement', 'capture'])) {
                $payment->status = 'Success';
                $payment->paid_at = now();

                if ($payment->booking) {
                    $payment->booking->status = 'Paid';
                    $payment->booking->save();

                    if (! $payment->booking->ticket) {
                        Ticket::create([
                            'booking_id' => $payment->booking->id,
                            'ticket_number' => 'TKT-'.strtoupper(Str::random(8)),
                            'qr_code' => 'TICKET-'.$payment->booking->booking_code,
                            'status' => 'Issued',
                            'issued_at' => now(),
                        ]);
                    }

                    if ($payment->booking->schedule) {
                        $payment->booking->schedule->recalculateAvailableSeats();
                    }
                }
            } elseif ($status === 'expire') {
                $payment->status = 'Expired';
                if ($payment->booking) {
                    $payment->booking->status = 'Expired';
                    $payment->booking->save();
                    if ($payment->booking->schedule) {
                        $payment->booking->schedule->recalculateAvailableSeats();
                    }
                }
            } elseif (in_array($status, ['deny', 'cancel'])) {
                $payment->status = 'Failed';
                if ($payment->booking) {
                    $payment->booking->status = 'Cancelled';
                    $payment->booking->save();
                    if ($payment->booking->schedule) {
                        $payment->booking->schedule->recalculateAvailableSeats();
                    }
                }
            }

            $payment->save();

            return redirect()->back()->with('success', "Status Payment Gateway ({$provider}) berhasil disinkronkan: {$status}. Log raw response telah diperbarui.");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Modul Rute Perjalanan & Jadwal Keberangkatan
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan daftar rute perjalanan, tarif tiket dasar/weekend, dan statistik rute.
     */
    public function routeIndex(Request $request)
    {
        $user = Auth::user();

        // 4 Kartu Metrik
        $totalRoutes = Route::count();
        $activeRoutesCount = Route::where('is_active', true)->count();
        $totalLocations = Location::count();
        $activeSchedulesCount = Schedule::whereDate('departure_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['Cancelled', 'cancelled', 'Completed', 'completed'])
            ->count();

        // Master Data untuk Modal
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'siap_jalan')->orderBy('name')->get();
        $drivers = Driver::with('user')->where('status', 'standby')->get();

        // Query Rute
        $query = Route::with([
            'origin',
            'destination',
            'schedules' => function ($q) {
                $q->whereDate('departure_date', '>=', now()->toDateString())
                    ->whereNotIn('status', ['Cancelled', 'cancelled']);
            },
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('origin', function ($o) use ($search) {
                    $o->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('destination', function ($d) use ($search) {
                    $d->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $routes = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.admin.rute.index', compact(
            'user',
            'routes',
            'locations',
            'vehicles',
            'drivers',
            'totalRoutes',
            'activeRoutesCount',
            'totalLocations',
            'activeSchedulesCount'
        ));
    }

    /**
     * Menyimpan rute perjalanan baru ke database.
     */
    public function storeRoute(Request $request)
    {
        $validated = $request->validate([
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'price' => ['required', 'numeric', 'min:10000'],
            'weekend_price' => ['nullable', 'numeric', 'min:10000'],
            'duration' => ['required', 'string', 'max:50'],
            'distance_km' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'origin_id.different' => 'Titik awal keberangkatan dan titik tujuan akhir tidak boleh sama.',
            'price.required' => 'Tarif dasar tiket wajib diisi.',
            'duration.required' => 'Estimasi durasi perjalanan wajib diisi.',
        ]);

        // Cek duplikasi rute
        $exists = Route::where('origin_id', $validated['origin_id'])
            ->where('destination_id', $validated['destination_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Rute perjalanan dengan titik asal dan titik tujuan tersebut sudah terdaftar.');
        }

        Route::create([
            'origin_id' => $validated['origin_id'],
            'destination_id' => $validated['destination_id'],
            'price' => $validated['price'],
            'weekend_price' => $validated['weekend_price'] ?? null,
            'duration' => $validated['duration'],
            'distance_km' => $validated['distance_km'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.admin.rute.index')->with('success', 'Rute perjalanan baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data rute perjalanan dan penyesuaian tarif.
     */
    public function updateRoute(Request $request, Route $route)
    {
        $validated = $request->validate([
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'price' => ['required', 'numeric', 'min:10000'],
            'weekend_price' => ['nullable', 'numeric', 'min:10000'],
            'duration' => ['required', 'string', 'max:50'],
            'distance_km' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Cek duplikasi dengan rute lain
        $duplicate = Route::where('origin_id', $validated['origin_id'])
            ->where('destination_id', $validated['destination_id'])
            ->where('id', '!=', $route->id)
            ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kombinasi titik asal dan tujuan sudah digunakan pada rute lain.');
        }

        $route->update([
            'origin_id' => $validated['origin_id'],
            'destination_id' => $validated['destination_id'],
            'price' => $validated['price'],
            'weekend_price' => $validated['weekend_price'] ?? null,
            'duration' => $validated['duration'],
            'distance_km' => $validated['distance_km'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $route->is_active,
        ]);

        return redirect()->route('dashboard.admin.rute.index')->with('success', "Data rute {$route->routeTitle()} berhasil diperbarui.");
    }

    /**
     * Mengubah status aktif / nonaktif rute perjalanan.
     */
    public function toggleRouteStatus(Route $route)
    {
        $route->is_active = ! $route->is_active;
        $route->save();

        $statusText = $route->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('dashboard.admin.rute.index')->with('success', "Rute {$route->routeTitle()} berhasil {$statusText}.");
    }

    /**
     * Menghapus rute perjalanan jika tidak memiliki riwayat tiket aktif.
     */
    public function destroyRoute(Route $route)
    {
        $hasActiveBookings = Booking::whereHas('schedule', function ($q) use ($route) {
            $q->where('route_id', $route->id)
                ->whereNotIn('status', ['Cancelled', 'cancelled']);
        })->exists();

        if ($hasActiveBookings) {
            return redirect()->route('dashboard.admin.rute.index')->with('error', "Rute {$route->routeTitle()} tidak dapat dihapus karena masih memiliki riwayat tiket aktif. Silakan nonaktifkan status rute sebagai gantinya.");
        }

        $title = $route->routeTitle();
        $route->delete();

        return redirect()->route('dashboard.admin.rute.index')->with('success', "Rute {$title} berhasil dihapus dari sistem.");
    }

    /**
     * Menyimpan master titik pool / shuttle / drop point baru.
     */
    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:10', 'unique:locations,code'],
            'city' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'in:pool,shuttle,drop_point'],
        ]);

        Location::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'type' => $validated['type'],
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.admin.rute.index')->with('success', "Titik lokasi/pool {$validated['name']} berhasil didaftarkan.");
    }

    /**
     * Menampilkan daftar dan kalender jadwal harian keberangkatan.
     */
    public function scheduleIndex(Request $request)
    {
        $user = Auth::user();

        // Master Data untuk Filter & Modal
        $routes = Route::with(['origin', 'destination'])->where('is_active', true)->get();
        $vehicles = Vehicle::orderBy('name')->get();
        $drivers = Driver::with('user')->get();

        // Query Jadwal
        $query = Schedule::with([
            'route.origin',
            'route.destination',
            'vehicle',
            'driver.user',
            'bookings',
        ]);

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('departure_date', $request->date);
        } else {
            // Default tampilkan hari ini ke depan
            $query->whereDate('departure_date', '>=', now()->toDateString());
        }

        // Filter Rute
        if ($request->filled('route_id') && $request->route_id !== 'all') {
            $query->where('route_id', $request->route_id);
        }

        // Filter Armada
        if ($request->filled('vehicle_id') && $request->vehicle_id !== 'all') {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter Driver
        if ($request->filled('driver_id') && $request->driver_id !== 'all') {
            $query->where('driver_id', $request->driver_id);
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('departure_date')
            ->orderBy('departure_time')
            ->paginate(15)
            ->withQueryString();

        // Metrik Ringkas
        $totalSchedulesCount = Schedule::whereDate('departure_date', '>=', now()->toDateString())->count();
        $activeTripsCount = Schedule::where('status', 'Active')->count();
        $scheduledCount = Schedule::where('status', 'Scheduled')->whereDate('departure_date', '>=', now()->toDateString())->count();

        return view('dashboard.admin.rute.jadwal', compact(
            'user',
            'schedules',
            'routes',
            'vehicles',
            'drivers',
            'totalSchedulesCount',
            'activeTripsCount',
            'scheduledCount'
        ));
    }

    /**
     * Membuat satu slot jadwal keberangkatan tunggal.
     */
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'route_id' => ['required', 'exists:routes,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'price' => ['nullable', 'numeric', 'min:10000'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $vehicle->ensureSeatsExist();

        // Cek bentrok jam jalan armada
        $vehicleClash = Schedule::where('vehicle_id', $vehicle->id)
            ->whereDate('departure_date', $validated['departure_date'])
            ->where('departure_time', $validated['departure_time'])
            ->whereNotIn('status', ['Cancelled', 'cancelled'])
            ->exists();

        if ($vehicleClash) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Armada {$vehicle->name} [{$vehicle->plate_number}] sudah memiliki jadwal keberangkatan lain pada tanggal dan jam tersebut.");
        }

        // Cek bentrok sopir jika dipilih
        if (! empty($validated['driver_id'])) {
            $driverClash = Schedule::where('driver_id', $validated['driver_id'])
                ->whereDate('departure_date', $validated['departure_date'])
                ->where('departure_time', $validated['departure_time'])
                ->whereNotIn('status', ['Cancelled', 'cancelled'])
                ->exists();

            if ($driverClash) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sopir yang dipilih sudah ditugaskan pada jadwal lain di tanggal dan jam yang sama.');
            }
        }

        Schedule::create([
            'route_id' => $validated['route_id'],
            'vehicle_id' => $vehicle->id,
            'driver_id' => $validated['driver_id'] ?? null,
            'departure_date' => $validated['departure_date'],
            'departure_time' => $validated['departure_time'],
            'available_seats' => $vehicle->capacity,
            'price' => $validated['price'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'Scheduled',
        ]);

        return redirect()->route('dashboard.admin.rute.jadwal')->with('success', 'Jadwal keberangkatan baru berhasil ditambahkan.');
    }

    /**
     * Batch generator jadwal keberangkatan massal harian.
     */
    public function batchGenerateSchedules(Request $request)
    {
        $validated = $request->validate([
            'route_id' => ['required', 'exists:routes,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'departure_times' => ['required', 'array', 'min:1'],
            'departure_times.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'price' => ['nullable', 'numeric', 'min:10000'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $vehicle->ensureSeatsExist();

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();
        $times = $validated['departure_times'];

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($validated, $vehicle, $start, $end, $times, &$createdCount, &$skippedCount) {
            $current = $start->copy();
            while ($current->lte($end)) {
                $dateStr = $current->toDateString();

                foreach ($times as $time) {
                    $exists = Schedule::where('route_id', $validated['route_id'])
                        ->where('vehicle_id', $vehicle->id)
                        ->whereDate('departure_date', $dateStr)
                        ->where('departure_time', $time)
                        ->whereNotIn('status', ['Cancelled', 'cancelled'])
                        ->exists();

                    if (! $exists) {
                        Schedule::create([
                            'route_id' => $validated['route_id'],
                            'vehicle_id' => $vehicle->id,
                            'driver_id' => $validated['driver_id'] ?? null,
                            'departure_date' => $dateStr,
                            'departure_time' => $time,
                            'available_seats' => $vehicle->capacity,
                            'price' => $validated['price'] ?? null,
                            'notes' => $validated['notes'] ?? null,
                            'status' => 'Scheduled',
                        ]);
                        $createdCount++;
                    } else {
                        $skippedCount++;
                    }
                }

                $current->addDay();
            }
        });

        $msg = "Berhasil membuat {$createdCount} slot jadwal keberangkatan harian.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} jadwal dilewati karena sudah ada sebelumnya).";
        }

        return redirect()->route('dashboard.admin.rute.jadwal')->with('success', $msg);
    }

    /**
     * Memasangkan atau mengganti armada minibus dan pengemudi pada suatu jadwal.
     */
    public function assignScheduleVehicleDriver(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
        ]);

        $oldVehicleId = $schedule->vehicle_id;
        $newVehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $newVehicle->ensureSeatsExist();

        $schedule->vehicle_id = $newVehicle->id;
        $schedule->driver_id = $validated['driver_id'] ?? null;
        $schedule->save();

        // Jika armada berganti, hitung ulang sisa kursi
        if ($oldVehicleId !== $newVehicle->id) {
            $schedule->recalculateAvailableSeats();
        }

        $driverName = $schedule->driver?->user?->name ?? 'Belum ada sopir';

        return redirect()->route('dashboard.admin.rute.jadwal')->with('success', "Penetapan jadwal berhasil diperbarui: Armada {$newVehicle->name} [{$newVehicle->plate_number}] & Sopir: {$driverName}.");
    }

    /**
     * Mengubah status operasional jadwal keberangkatan.
     */
    public function updateScheduleStatus(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Scheduled,Active,Completed,Cancelled'],
        ]);

        $schedule->status = $validated['status'];
        $schedule->save();

        return redirect()->route('dashboard.admin.rute.jadwal')->with('success', "Status jadwal keberangkatan berhasil diubah menjadi {$schedule->status}.");
    }

    /**
     * Menghapus jadwal keberangkatan jika belum memiliki pesanan aktif.
     */
    public function destroySchedule(Schedule $schedule)
    {
        $hasActiveBookings = $schedule->bookings()
            ->whereNotIn('status', ['Cancelled', 'cancelled'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->route('dashboard.admin.rute.jadwal')->with('error', 'Jadwal tidak dapat dihapus karena sudah ada pemesanan tiket dari penumpang.');
        }

        $schedule->delete();

        return redirect()->route('dashboard.admin.rute.jadwal')->with('success', 'Jadwal keberangkatan berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | Modul Kelola Refund (Cancellation & Transparent Cash Outflow Management)
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan daftar permohonan pembatalan, ringkasan arus keluar uang, dan filter.
     */
    public function refundIndex(Request $request)
    {
        $user = Auth::user();

        // 4 Kartu Metrik Ringkasan Finansial Arus Keluar
        $totalRefundRequests = Refund::count();
        $pendingRefundCount = Refund::whereIn('status', ['Menunggu', 'menunggu', 'Pending', 'pending'])->count();
        $totalRefundedAmount = Refund::whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved'])->sum('amount');
        $totalFeesRetained = Refund::whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved'])->sum('cancellation_fee');

        // Master Data Pemesanan Aktif untuk Modal "Ajukan Refund Baru"
        $activeBookings = Booking::with([
            'user',
            'schedule.route.origin',
            'schedule.route.destination',
        ])
            ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
            ->whereDoesntHave('refund', function ($q) {
                $q->whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved', 'Menunggu', 'menunggu', 'Pending', 'pending']);
            })
            ->latest()
            ->take(50)
            ->get();

        // Query Utama Permohonan Refund
        $query = Refund::with([
            'booking.user',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.passengers',
            'user',
            'processor',
        ]);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('account_holder', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($b) use ($search) {
                        $b->where('booking_code', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($u) use ($search) {
                                $u->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $st = strtolower($request->status);
            if ($st === 'pending') {
                $query->whereIn('status', ['Menunggu', 'menunggu', 'Pending', 'pending']);
            } elseif ($st === 'approved') {
                $query->whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved']);
            } elseif ($st === 'rejected') {
                $query->whereIn('status', ['Ditolak', 'ditolak', 'Rejected', 'rejected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $refunds = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.admin.refund.index', compact(
            'user',
            'refunds',
            'activeBookings',
            'totalRefundRequests',
            'pendingRefundCount',
            'totalRefundedAmount',
            'totalFeesRetained'
        ));
    }

    /**
     * Membuat pengajuan permohonan pembatalan & pengembalian dana baru.
     */
    public function storeRefund(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'reason' => ['required', 'string', 'max:500'],
            'cancellation_fee_type' => ['required', 'in:percentage,nominal'],
            'fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cancellation_fee' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:100'],
        ], [
            'booking_id.required' => 'Pemesanan yang ingin dibatalkan wajib dipilih.',
            'reason.required' => 'Alasan pembatalan tiket wajib diisi.',
            'bank_name.required' => 'Nama bank tujuan transfer balik wajib diisi.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_holder.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        // Cek jika sudah memiliki pengajuan refund aktif
        $existing = Refund::where('booking_id', $booking->id)
            ->whereIn('status', ['Menunggu', 'menunggu', 'Pending', 'pending', 'Disetujui', 'disetujui', 'Approved', 'approved'])
            ->exists();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Pemesanan {$booking->booking_code} sudah memiliki permohonan refund yang sedang aktif atau sudah disetujui sebelumnya.");
        }

        $originalAmount = (float) $booking->total_price;

        // Hitung potongan biaya pembatalan & nominal bersih
        if ($validated['cancellation_fee_type'] === 'percentage') {
            $pct = (float) ($validated['fee_percentage'] ?? 0);
            $fee = round($originalAmount * ($pct / 100), 2);
        } else {
            $fee = (float) ($validated['cancellation_fee'] ?? 0);
        }

        $fee = min($originalAmount, max(0, $fee));
        $netAmount = max(0, $originalAmount - $fee);
        $refundPercentage = $originalAmount > 0 ? round(($netAmount / $originalAmount) * 100, 2) : 0;

        Refund::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'original_amount' => $originalAmount,
            'amount' => $netAmount,
            'cancellation_fee' => $fee,
            'refund_percentage' => $refundPercentage,
            'reason' => $validated['reason'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_holder' => $validated['account_holder'],
            'status' => 'Menunggu',
        ]);

        return redirect()->route('dashboard.admin.refund.index')->with('success', "Permohonan refund untuk pemesanan {$booking->booking_code} berhasil diajukan dengan estimasi dana bersih Rp ".number_format($netAmount, 0, ',', '.').'.');
    }

    /**
     * Menyetujui permohonan refund, membatalkan pesanan, dan mengosongkan kembali kursi.
     */
    public function approveRefund(Request $request, Refund $refund)
    {
        if (in_array(strtolower($refund->status), ['disetujui', 'approved'])) {
            return redirect()->back()->with('error', 'Permohonan refund ini sudah disetujui sebelumnya.');
        }

        $validated = $request->validate([
            'cancellation_fee' => ['nullable', 'numeric', 'min:0'],
            'admin_note' => ['nullable', 'string', 'max:500'],
            'transfer_proof' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $originalAmount = (float) ($refund->original_amount ?? ($refund->booking ? $refund->booking->total_price : $refund->amount));
        $fee = isset($validated['cancellation_fee']) ? (float) $validated['cancellation_fee'] : (float) $refund->cancellation_fee;
        $fee = min($originalAmount, max(0, $fee));
        $netAmount = max(0, $originalAmount - $fee);
        $refundPercentage = $originalAmount > 0 ? round(($netAmount / $originalAmount) * 100, 2) : 0;

        // Upload bukti transfer kantor jika dilampirkan
        $proofPath = $refund->transfer_proof;
        if ($request->hasFile('transfer_proof')) {
            $file = $request->file('transfer_proof');
            $filename = 'refund_'.$refund->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $proofPath = $file->storeAs('refund-proofs', $filename, 'public');
        }

        DB::transaction(function () use ($refund, $originalAmount, $fee, $netAmount, $refundPercentage, $proofPath, $validated) {
            // 1. Update status Refund
            $refund->original_amount = $originalAmount;
            $refund->amount = $netAmount;
            $refund->cancellation_fee = $fee;
            $refund->refund_percentage = $refundPercentage;
            $refund->transfer_proof = $proofPath;
            $refund->admin_note = $validated['admin_note'] ?? $refund->admin_note;
            $refund->status = 'Disetujui';
            $refund->processed_at = now();
            $refund->processed_by = Auth::id();
            $refund->save();

            // 2. Batalkan Pemesanan & Bebaskan Kursi
            $booking = $refund->booking;
            if ($booking) {
                $booking->status = 'Cancelled';
                $formattedNet = 'Rp '.number_format($netAmount, 0, ',', '.');
                $formattedFee = 'Rp '.number_format($fee, 0, ',', '.');
                $logText = '[Refund Disetujui '.now()->format('d/m/Y H:i')."] Dana ditransfer balik: {$formattedNet} (Potongan Admin: {$formattedFee}). Tujuan: {$refund->bank_name} {$refund->account_number} a.n {$refund->account_holder}.";
                $booking->notes = trim(($booking->notes ? $booking->notes."\n" : '').$logText);
                $booking->save();

                // 3. Batalkan Tiket
                if ($booking->ticket) {
                    $booking->ticket->status = 'Cancelled';
                    $booking->ticket->save();
                }

                // 4. Update Pembayaran
                if ($booking->payment) {
                    $booking->payment->status = 'Refunded';
                    $booking->payment->save();
                }

                // 5. KOSONGKAN KEMBALI KURSI JADWAL KEBERANGKATAN
                if ($booking->schedule) {
                    $booking->schedule->recalculateAvailableSeats();
                }
            }
        });

        $bookingCode = $refund->booking?->booking_code ?? '#'.$refund->booking_id;

        return redirect()->route('dashboard.admin.refund.index')
            ->with('success', "Permohonan refund {$bookingCode} berhasil disetujui. Dana bersih Rp ".number_format($netAmount, 0, ',', '.').' telah dicatat dan kursi penumpang telah dikosongkan kembali untuk dapat dipesan ulang.');
    }

    /**
     * Menolak permohonan pembatalan & refund.
     */
    public function rejectRefund(Request $request, Refund $refund)
    {
        if (in_array(strtolower($refund->status), ['disetujui', 'approved'])) {
            return redirect()->back()->with('error', 'Permohonan refund yang sudah disetujui tidak dapat ditolak.');
        }

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'admin_note.required' => 'Alasan penolakan refund wajib diisi.',
            'admin_note.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $refund->status = 'Ditolak';
        $refund->admin_note = $validated['admin_note'];
        $refund->processed_at = now();
        $refund->processed_by = Auth::id();
        $refund->save();

        $bookingCode = $refund->booking?->booking_code ?? '#'.$refund->booking_id;

        return redirect()->route('dashboard.admin.refund.index')
            ->with('success', "Permohonan refund {$bookingCode} telah ditolak dengan alasan: \"{$validated['admin_note']}\". Status tiket dan kursi penumpang tetap aktif.");
    }

    /*
    |--------------------------------------------------------------------------
    | Modul Laporan (Rekonsiliasi Keuangan, Keterisian Kursi, Manifes, & Refund)
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan dashboard laporan terpadu dengan 4 tab rekonsiliasi dan filter.
     */
    public function reportIndex(Request $request)
    {
        $user = Auth::user();

        // 1. Parameter Filter & Tab
        $tab = $request->input('tab', 'financial');
        if (! in_array($tab, ['financial', 'occupancy', 'manifest', 'cancellation'])) {
            $tab = 'financial';
        }

        // Preset Waktu Cepat
        $preset = $request->input('preset');
        if ($preset === 'today') {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        } elseif ($preset === '7days') {
            $startDate = now()->subDays(6)->toDateString();
            $endDate = now()->toDateString();
        } elseif ($preset === 'this_month') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->toDateString();
        } elseif ($preset === 'this_year') {
            $startDate = now()->startOfYear()->toDateString();
            $endDate = now()->toDateString();
        } else {
            $startDate = $request->input('date_from', now()->startOfMonth()->toDateString());
            $endDate = $request->input('date_to', now()->toDateString());
        }

        $routeId = $request->filled('route_id') && $request->route_id !== 'all' ? $request->route_id : null;

        // Master Rute untuk Dropdown Filter
        $routes = Route::with(['origin', 'destination'])->orderBy('id')->get();

        // 2. Tab 1: Rekonsiliasi Keuangan (Financial)
        $paidBookingsQuery = Booking::with([
            'user',
            'payment',
            'schedule.route.origin',
            'schedule.route.destination',
        ])
            ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($routeId) {
            $paidBookingsQuery->whereHas('schedule', function ($q) use ($routeId) {
                $q->where('route_id', $routeId);
            });
        }

        $paidBookings = $paidBookingsQuery->latest()->get();

        // Data Refund yang Disetujui
        $approvedRefundsQuery = Refund::with(['booking.user', 'processor'])
            ->whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved'])
            ->whereDate('processed_at', '>=', $startDate)
            ->whereDate('processed_at', '<=', $endDate);

        if ($routeId) {
            $approvedRefundsQuery->whereHas('booking.schedule', function ($q) use ($routeId) {
                $q->where('route_id', $routeId);
            });
        }

        $approvedRefunds = $approvedRefundsQuery->get();

        // Kalkulasi Finansial
        $grossRevenue = (float) $paidBookings->sum('total_price');
        $totalRefundOutflow = (float) $approvedRefunds->sum('amount');
        $totalFeesRetained = (float) $approvedRefunds->sum('cancellation_fee');
        $netRevenue = $grossRevenue - $totalRefundOutflow;
        $totalTicketsSold = (int) $paidBookings->sum('total_passenger');

        // Breakdown Metode Pembayaran
        $breakdownBank = 0;
        $breakdownQris = 0;
        $breakdownCash = 0;
        $breakdownGateway = 0;

        foreach ($paidBookings as $b) {
            $method = strtolower($b->payment?->payment_method ?? '');
            $amt = (float) $b->total_price;
            if (str_contains($method, 'transfer') || str_contains($method, 'bank') || str_contains($method, 'bca') || str_contains($method, 'mandiri') || str_contains($method, 'bri') || str_contains($method, 'bni')) {
                $breakdownBank += $amt;
            } elseif (str_contains($method, 'qris')) {
                $breakdownQris += $amt;
            } elseif (str_contains($method, 'cash') || str_contains($method, 'tunai') || str_contains($method, 'loket') || $b->source === 'walk_in' || $b->source === 'loket') {
                $breakdownCash += $amt;
            } else {
                $breakdownGateway += $amt;
            }
        }

        // 3. Tab 2: Keterisian Kursi (Occupancy & Load Factor)
        $schedulesQuery = Schedule::with([
            'route.origin',
            'route.destination',
            'vehicle',
            'driver.user',
            'bookings' => function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled']);
            },
            'bookings.passengers',
        ])
            ->whereDate('departure_date', '>=', $startDate)
            ->whereDate('departure_date', '<=', $endDate);

        if ($routeId) {
            $schedulesQuery->where('route_id', $routeId);
        }

        $schedules = $schedulesQuery->orderBy('departure_date')->orderBy('departure_time')->get();

        $totalCapacityAll = 0;
        $totalOccupiedAll = 0;

        foreach ($schedules as $sch) {
            $cap = $sch->vehicle?->capacity ?? 10;
            $occ = 0;
            foreach ($sch->bookings as $sb) {
                $occ += $sb->passengers->count() > 0 ? $sb->passengers->count() : ($sb->total_passenger ?? 1);
            }
            $totalCapacityAll += $cap;
            $totalOccupiedAll += $occ;
        }

        $totalEmptySeatsAll = max(0, $totalCapacityAll - $totalOccupiedAll);
        $overallLoadFactor = $totalCapacityAll > 0 ? round(($totalOccupiedAll / $totalCapacityAll) * 100, 1) : 0;

        // 4. Tab 3: Manifes Penumpang Terpadu (Manifest)
        $manifestQuery = BookingPassenger::with([
            'seat',
            'booking.user',
            'booking.ticket',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.schedule.vehicle',
            'booking.schedule.driver.user',
        ])
            ->whereHas('booking', function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled']);
            })
            ->whereHas('booking.schedule', function ($q) use ($startDate, $endDate, $routeId) {
                $q->whereDate('departure_date', '>=', $startDate)
                    ->whereDate('departure_date', '<=', $endDate);
                if ($routeId) {
                    $q->where('route_id', $routeId);
                }
            });

        $manifestPassengers = $manifestQuery->latest()->get();

        // 5. Tab 4: Rekap Pembatalan & Refund (Cancellation)
        $cancellationsQuery = Refund::with([
            'booking.user',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'processor',
        ])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($routeId) {
            $cancellationsQuery->whereHas('booking.schedule', function ($q) use ($routeId) {
                $q->where('route_id', $routeId);
            });
        }

        $cancellations = $cancellationsQuery->latest()->get();

        return view('dashboard.admin.laporan.index', compact(
            'user',
            'tab',
            'preset',
            'startDate',
            'endDate',
            'routeId',
            'routes',
            'paidBookings',
            'approvedRefunds',
            'grossRevenue',
            'totalRefundOutflow',
            'totalFeesRetained',
            'netRevenue',
            'totalTicketsSold',
            'breakdownBank',
            'breakdownQris',
            'breakdownCash',
            'breakdownGateway',
            'schedules',
            'totalCapacityAll',
            'totalOccupiedAll',
            'totalEmptySeatsAll',
            'overallLoadFactor',
            'manifestPassengers',
            'cancellations'
        ));
    }

    /**
     * Mengekspor laporan ke format Excel (CSV terstruktur dengan UTF-8 BOM).
     */
    public function exportReportExcel(Request $request)
    {
        $tab = $request->input('tab', 'financial');
        $startDate = $request->input('date_from', now()->startOfMonth()->toDateString());
        $endDate = $request->input('date_to', now()->toDateString());
        $routeId = $request->filled('route_id') && $request->route_id !== 'all' ? $request->route_id : null;

        $filename = 'Laporan_'.ucfirst($tab).'_'.$startDate.'_sd_'.$endDate.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($tab, $startDate, $endDate, $routeId) {
            $handle = fopen('php://output', 'w');
            // Tambahkan UTF-8 BOM agar Microsoft Excel menampilkan aksen/karakter dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($tab === 'financial') {
                fputcsv($handle, ['LAPORAN REKONSILIASI KEUANGAN TRAVEL']);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, ['Diekspor Pada:', now()->format('d/m/Y H:i:s')]);
                fputcsv($handle, []);

                fputcsv($handle, ['No', 'Kode Booking', 'No Invoice', 'Tanggal', 'Nama Pemesan', 'Kontak', 'Rute Perjalanan', 'Metode Bayar', 'Status', 'Nominal (Rp)']);

                $query = Booking::with(['user', 'payment', 'schedule.route.origin', 'schedule.route.destination'])
                    ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);

                if ($routeId) {
                    $query->whereHas('schedule', fn ($q) => $q->where('route_id', $routeId));
                }

                $bookings = $query->latest()->get();
                $totalGross = 0;

                foreach ($bookings as $idx => $b) {
                    $totalGross += (float) $b->total_price;
                    $routeStr = ($b->schedule?->route?->origin?->city ?? 'Origin').' - '.($b->schedule?->route?->destination?->city ?? 'Destination');
                    fputcsv($handle, [
                        $idx + 1,
                        $b->booking_code,
                        $b->payment?->order_id ?? '-',
                        $b->created_at ? $b->created_at->format('d/m/Y H:i') : '-',
                        $b->user?->name ?? 'Pelanggan',
                        $b->user?->phone ?? '-',
                        $routeStr,
                        $b->payment?->payment_method ?? 'Online',
                        $b->status,
                        (float) $b->total_price,
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['', '', '', '', '', '', '', '', 'TOTAL PENDAPATAN KOTOR', $totalGross]);

                // Hitung refund
                $refunds = Refund::whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved'])
                    ->whereDate('processed_at', '>=', $startDate)
                    ->whereDate('processed_at', '<=', $endDate)
                    ->get();

                $totalRefund = (float) $refunds->sum('amount');
                $totalNet = $totalGross - $totalRefund;

                fputcsv($handle, ['', '', '', '', '', '', '', '', 'TOTAL PENGEMBALIAN REFUND', -$totalRefund]);
                fputcsv($handle, ['', '', '', '', '', '', '', '', 'TOTAL PENDAPATAN BERSIH', $totalNet]);
            } elseif ($tab === 'occupancy') {
                fputcsv($handle, ['LAPORAN KETERISIAN KURSI & LOAD FACTOR']);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, []);

                fputcsv($handle, ['No', 'Tanggal', 'Jam', 'Rute Perjalanan', 'Armada', 'No Polisi', 'Sopir', 'Kapasitas', 'Kursi Terisi', 'Kursi Kosong', 'Load Factor (%)', 'Status']);

                $query = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings.passengers'])
                    ->whereDate('departure_date', '>=', $startDate)
                    ->whereDate('departure_date', '<=', $endDate);

                if ($routeId) {
                    $query->where('route_id', $routeId);
                }

                $schedules = $query->orderBy('departure_date')->orderBy('departure_time')->get();

                $sumCap = 0;
                $sumOcc = 0;

                foreach ($schedules as $idx => $s) {
                    $cap = $s->vehicle?->capacity ?? 10;
                    $occ = 0;
                    foreach ($s->bookings as $sb) {
                        if (! in_array(strtolower($sb->status), ['cancelled', 'batal'])) {
                            $occ += $sb->passengers->count() > 0 ? $sb->passengers->count() : ($sb->total_passenger ?? 1);
                        }
                    }
                    $empty = max(0, $cap - $occ);
                    $lf = $cap > 0 ? round(($occ / $cap) * 100, 1) : 0;

                    $sumCap += $cap;
                    $sumOcc += $occ;

                    $routeStr = ($s->route?->origin?->city ?? 'Origin').' - '.($s->route?->destination?->city ?? 'Destination');

                    fputcsv($handle, [
                        $idx + 1,
                        $s->departure_date ? $s->departure_date->format('d/m/Y') : '-',
                        $s->departure_time,
                        $routeStr,
                        $s->vehicle?->name ?? 'Minibus',
                        $s->vehicle?->plate_number ?? '-',
                        $s->driver?->user?->name ?? 'Belum Ditugaskan',
                        $cap,
                        $occ,
                        $empty,
                        $lf.'%',
                        $s->status,
                    ]);
                }

                $avgLf = $sumCap > 0 ? round(($sumOcc / $sumCap) * 100, 1) : 0;
                fputcsv($handle, []);
                fputcsv($handle, ['', '', '', '', '', '', 'TOTAL', $sumCap, $sumOcc, max(0, $sumCap - $sumOcc), $avgLf.'%', '']);
            } elseif ($tab === 'manifest') {
                fputcsv($handle, ['MANIFES PENUMPANG TRAVEL']);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, []);

                fputcsv($handle, ['No', 'Tanggal Jalan', 'Jam', 'Nomor Tiket', 'Kode Booking', 'Nama Penumpang', 'No Kursi', 'Telepon / WA', 'Rute', 'Titik Asal (Pool)', 'Titik Turun (Drop Point)', 'Armada', 'Sopir']);

                $query = BookingPassenger::with([
                    'seat',
                    'booking.ticket',
                    'booking.schedule.route.origin',
                    'booking.schedule.route.destination',
                    'booking.schedule.vehicle',
                    'booking.schedule.driver.user',
                ])
                    ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['Cancelled', 'cancelled']))
                    ->whereHas('booking.schedule', function ($q) use ($startDate, $endDate, $routeId) {
                        $q->whereDate('departure_date', '>=', $startDate)
                            ->whereDate('departure_date', '<=', $endDate);
                        if ($routeId) {
                            $q->where('route_id', $routeId);
                        }
                    });

                $passengers = $query->get();

                foreach ($passengers as $idx => $p) {
                    $sch = $p->booking?->schedule;
                    fputcsv($handle, [
                        $idx + 1,
                        $sch?->departure_date ? $sch->departure_date->format('d/m/Y') : '-',
                        $sch?->departure_time ?? '-',
                        $p->booking?->ticket?->ticket_number ?? '-',
                        $p->booking?->booking_code ?? '-',
                        $p->name,
                        $p->seat?->seat_number ?? '-',
                        $p->phone ?? $p->booking?->user?->phone ?? '-',
                        ($sch?->route?->origin?->city ?? '').' - '.($sch?->route?->destination?->city ?? ''),
                        $sch?->route?->origin?->name ?? '-',
                        $sch?->route?->destination?->name ?? '-',
                        $sch?->vehicle?->plate_number ?? '-',
                        $sch?->driver?->user?->name ?? '-',
                    ]);
                }
            } else {
                fputcsv($handle, ['REKAP PEMBATALAN TIKET & REFUND']);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, []);

                fputcsv($handle, ['No', 'Kode Booking', 'Tanggal Pengajuan', 'Nama Pemesan', 'Kontak', 'Alasan Pembatalan', 'Nominal Asli (Rp)', 'Potongan Admin (Rp)', 'Dana Bersih Ditransfer (Rp)', 'Rekening Tujuan', 'Status', 'Diverifikasi Oleh']);

                $query = Refund::with(['booking.user', 'processor'])
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);

                if ($routeId) {
                    $query->whereHas('booking.schedule', fn ($q) => $q->where('route_id', $routeId));
                }

                $refunds = $query->latest()->get();
                $sumOrig = 0;
                $sumFee = 0;
                $sumNet = 0;

                foreach ($refunds as $idx => $rf) {
                    $orig = (float) ($rf->original_amount ?? ($rf->booking ? $rf->booking->total_price : $rf->amount));
                    $fee = (float) $rf->cancellation_fee;
                    $net = (float) $rf->amount;

                    $sumOrig += $orig;
                    $sumFee += $fee;
                    $sumNet += $net;

                    $bankInfo = ($rf->bank_name ?? 'BANK').' '.($rf->account_number ?? '-').' a.n '.($rf->account_holder ?? '-');

                    fputcsv($handle, [
                        $idx + 1,
                        $rf->booking?->booking_code ?? '#'.$rf->booking_id,
                        $rf->created_at ? $rf->created_at->format('d/m/Y H:i') : '-',
                        $rf->booking?->user?->name ?? $rf->user?->name ?? 'Pelanggan',
                        $rf->booking?->user?->phone ?? $rf->user?->phone ?? '-',
                        $rf->reason ?? '-',
                        $orig,
                        $fee,
                        $net,
                        $bankInfo,
                        $rf->status,
                        $rf->processor?->name ?? '-',
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['', '', '', '', '', 'TOTAL', $sumOrig, $sumFee, $sumNet, '', '', '']);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Menampilkan dokumen resmi laporan siap cetak / simpan ke PDF.
     */
    public function exportReportPdf(Request $request)
    {
        $user = Auth::user();

        $tab = $request->input('tab', 'financial');
        if (! in_array($tab, ['financial', 'occupancy', 'manifest', 'cancellation'])) {
            $tab = 'financial';
        }

        $startDate = $request->input('date_from', now()->startOfMonth()->toDateString());
        $endDate = $request->input('date_to', now()->toDateString());
        $routeId = $request->filled('route_id') && $request->route_id !== 'all' ? $request->route_id : null;

        $selectedRoute = $routeId ? Route::with(['origin', 'destination'])->find($routeId) : null;

        // Data berdasarkan Tab
        if ($tab === 'financial') {
            $query = Booking::with(['user', 'payment', 'schedule.route.origin', 'schedule.route.destination'])
                ->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);

            if ($routeId) {
                $query->whereHas('schedule', fn ($q) => $q->where('route_id', $routeId));
            }

            $bookings = $query->latest()->get();
            $refunds = Refund::whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved'])
                ->whereDate('processed_at', '>=', $startDate)
                ->whereDate('processed_at', '<=', $endDate)
                ->get();

            $grossRevenue = (float) $bookings->sum('total_price');
            $totalRefund = (float) $refunds->sum('amount');
            $totalFee = (float) $refunds->sum('cancellation_fee');
            $netRevenue = $grossRevenue - $totalRefund;

            return view('dashboard.admin.laporan.print', compact(
                'user',
                'tab',
                'startDate',
                'endDate',
                'selectedRoute',
                'bookings',
                'refunds',
                'grossRevenue',
                'totalRefund',
                'totalFee',
                'netRevenue'
            ));
        } elseif ($tab === 'occupancy') {
            $query = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings.passengers'])
                ->whereDate('departure_date', '>=', $startDate)
                ->whereDate('departure_date', '<=', $endDate);

            if ($routeId) {
                $query->where('route_id', $routeId);
            }

            $schedules = $query->orderBy('departure_date')->orderBy('departure_time')->get();

            return view('dashboard.admin.laporan.print', compact(
                'user',
                'tab',
                'startDate',
                'endDate',
                'selectedRoute',
                'schedules'
            ));
        } elseif ($tab === 'manifest') {
            $query = BookingPassenger::with([
                'seat',
                'booking.ticket',
                'booking.schedule.route.origin',
                'booking.schedule.route.destination',
                'booking.schedule.vehicle',
                'booking.schedule.driver.user',
            ])
                ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['Cancelled', 'cancelled']))
                ->whereHas('booking.schedule', function ($q) use ($startDate, $endDate, $routeId) {
                    $q->whereDate('departure_date', '>=', $startDate)
                        ->whereDate('departure_date', '<=', $endDate);
                    if ($routeId) {
                        $q->where('route_id', $routeId);
                    }
                });

            $passengers = $query->get();

            return view('dashboard.admin.laporan.print', compact(
                'user',
                'tab',
                'startDate',
                'endDate',
                'selectedRoute',
                'passengers'
            ));
        } else {
            $query = Refund::with(['booking.user', 'booking.schedule.route.origin', 'booking.schedule.route.destination', 'processor'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);

            if ($routeId) {
                $query->whereHas('booking.schedule', fn ($q) => $q->where('route_id', $routeId));
            }

            $cancellations = $query->latest()->get();

            return view('dashboard.admin.laporan.print', compact(
                'user',
                'tab',
                'startDate',
                'endDate',
                'selectedRoute',
                'cancellations'
            ));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Modul Pengaturan (Profil, Kebijakan, Pembayaran, Lokasi, Keamanan)
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan halaman pengaturan dengan semua data setting dan lokasi operasional.
     */
    public function settingIndex(): View
    {
        $user = Auth::user();

        // Load semua setting dari key-value store
        $settings = AppSetting::all()->pluck('value', 'key');

        // Load semua titik operasional
        $locations = OperationalLocation::latest()->get();

        // Load riwayat sesi login untuk user ini (maks. 20 entri terbaru)
        $loginSessions = LoginSession::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('dashboard.admin.pengaturan.index', compact(
            'user',
            'settings',
            'locations',
            'loginSessions',
        ));
    }

    /**
     * Simpan/perbarui profil perusahaan.
     */
    public function settingUpdateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_whatsapp' => ['nullable', 'string', 'max:30'],
            'company_email' => ['nullable', 'email', 'max:150'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $fields = ['company_name', 'company_phone', 'company_whatsapp', 'company_email', 'company_address'];

        foreach ($fields as $field) {
            AppSetting::set($field, $validated[$field] ?? null);
        }

        if ($request->hasFile('company_logo')) {
            $oldLogo = AppSetting::get('company_logo');

            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('company_logo')->store('company', 'public');
            AppSetting::set('company_logo', $path);
        }

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'profil'])
            ->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Simpan kebijakan tiket & perjalanan.
     */
    public function settingUpdatePolicy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_timeout_qris' => ['required', 'integer', 'min:1', 'max:1440'],
            'payment_timeout_transfer' => ['required', 'integer', 'min:1', 'max:1440'],
            'refund_tiers' => ['nullable', 'string'],
            'reschedule_cutoff_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'reschedule_fee' => ['required', 'numeric', 'min:0'],
            'baggage_free_kg' => ['required', 'numeric', 'min:0'],
            'baggage_excess_fee_per_kg' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'kebijakan'])
            ->with('success', 'Kebijakan tiket & perjalanan berhasil disimpan.');
    }

    /**
     * Simpan integrasi pembayaran (rekening bank + gateway credentials).
     */
    public function settingUpdatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
            'gateway_provider' => ['nullable', 'string', 'in:midtrans,xendit,none'],
            'gateway_server_key' => ['nullable', 'string', 'max:255'],
            'gateway_client_key' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'pembayaran'])
            ->with('success', 'Konfigurasi integrasi pembayaran berhasil disimpan.');
    }

    /**
     * Tambah titik operasional baru.
     */
    public function settingStoreLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'maps_url' => ['nullable', 'url', 'max:500'],
            'coordinates' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        OperationalLocation::create($validated);

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'lokasi'])
            ->with('success', 'Titik operasional berhasil ditambahkan.');
    }

    /**
     * Perbarui titik operasional yang sudah ada.
     */
    public function settingUpdateLocation(Request $request, int $id): RedirectResponse
    {
        $location = OperationalLocation::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'maps_url' => ['nullable', 'url', 'max:500'],
            'coordinates' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $location->update($validated);

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'lokasi'])
            ->with('success', 'Titik operasional berhasil diperbarui.');
    }

    /**
     * Hapus titik operasional.
     */
    public function settingDestroyLocation(int $id): RedirectResponse
    {
        OperationalLocation::findOrFail($id)->delete();

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'lokasi'])
            ->with('success', 'Titik operasional berhasil dihapus.');
    }

    /**
     * Perbarui keamanan akun (password + 2FA).
     */
    public function settingUpdateSecurity(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'two_factor_enabled' => ['nullable', 'boolean'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.'])->with('tab', 'keamanan');
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        AppSetting::set('two_factor_enabled', $request->boolean('two_factor_enabled') ? '1' : '0');

        // Catat sesi login saat ini
        LoginSession::create([
            'user_id' => $user->id,
            'device' => $request->header('User-Agent', 'Unknown'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'last_active_at' => now(),
        ]);

        return redirect()->route('dashboard.admin.pengaturan.index', ['tab' => 'keamanan'])
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
