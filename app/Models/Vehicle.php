<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'hull_number',
        'name',
        'type',
        'capacity',
        'photo',
        'facilities',
        'status',
        'stnk_tax_due_date',
        'kir_expiry_date',
        'manufacture_year',
        'chassis_number',
        'engine_number',
        'notes',
        'parking_location',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'capacity' => 'integer',
            'manufacture_year' => 'integer',
            'stnk_tax_due_date' => 'date',
            'kir_expiry_date' => 'date',
        ];
    }

    /**
     * Daftar pilihan status unit operasional.
     *
     * @return array<string, string>
     */
    public static function availableStatuses(): array
    {
        return [
            'siap_jalan' => 'Siap Jalan',
            'beroperasi' => 'Sedang Beroperasi',
            'maintenance' => 'Masuk Bengkel',
        ];
    }

    /**
     * Daftar rekomendasi tipe minibus / kendaraan travel.
     *
     * @return array<int, string>
     */
    public static function availableTypes(): array
    {
        return [
            'Toyota HiAce Premio',
            'Toyota HiAce Commuter',
            'Mercedes-Benz Sprinter',
        ];
    }

    /**
     * Daftar pilihan fasilitas standar minibus.
     *
     * @return array<int, string>
     */
    public static function availableFacilities(): array
    {
        return [
            'AC Dingin',
            'Reclining Seat (Kursi Rebah)',
            'Port USB Charger',
            'Audio & Musik',
            'Android TV / Hiburan',
            'Wi-Fi Cepat',
            'Air Mineral Gratis',
            'Bagasi Luas',
            'Lampu Baca Pribadi',
        ];
    }

    /**
     * Label ramah tampilan status operasional.
     */
    public function statusLabel(): string
    {
        $status = strtolower($this->status ?? 'siap_jalan');

        return match ($status) {
            'beroperasi', 'on duty', 'in_trip', 'departed', 'operating' => 'Sedang Beroperasi',
            'maintenance', 'perbaikan', 'bengkel' => 'Masuk Bengkel',
            default => 'Siap Jalan',
        };
    }

    /**
     * Status CSS class untuk badge status unit.
     */
    public function statusBadgeClass(): string
    {
        $status = strtolower($this->status ?? 'siap_jalan');

        return match ($status) {
            'beroperasi', 'on duty', 'in_trip', 'departed', 'operating' => 'badge-operating',
            'maintenance', 'perbaikan', 'bengkel' => 'badge-maintenance',
            default => 'badge-ready',
        };
    }

    /**
     * Nomor lambung armada fisik (misal: HCE-08, ELF-02).
     */
    public function hullNumber(): string
    {
        if (! empty($this->hull_number)) {
            return $this->hull_number;
        }

        $typePrefix = 'HCE';
        if (stripos($this->type ?? '', 'elf') !== false || stripos($this->name ?? '', 'elf') !== false) {
            $typePrefix = 'ELF';
        } elseif (stripos($this->type ?? '', 'sprinter') !== false) {
            $typePrefix = 'SPT';
        }

        return $typePrefix.'-'.str_pad((string) ($this->id ?: 1), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Lokasi fisik unit kendaraan diparkir di garasi/pool.
     */
    public function parkingLocation(): string
    {
        if (! empty($this->parking_location)) {
            return $this->parking_location;
        }

        return 'Belum dicatat';
    }

    /**
     * Nomor Surat Perintah Jalan (SPJ) resmi.
     */
    public function spjNumber(?Schedule $schedule = null): string
    {
        $dateStr = Carbon::today()->format('Ymd');
        $code = $this->hullNumber();

        return "SPJ/{$dateStr}/{$code}";
    }

    /**
     * Evaluasi status masa berlaku uji KIR.
     *
     * @return array{status: string, label: string, badge_class: string, days_left: ?int}
     */
    public function kirStatus(): array
    {
        if (! $this->kir_expiry_date) {
            return [
                'status' => 'unknown',
                'label' => 'Belum Diatur',
                'badge_class' => 'badge-legal-unknown',
                'days_left' => null,
            ];
        }

        $today = Carbon::today();
        $kirDate = Carbon::parse($this->kir_expiry_date)->startOfDay();
        $daysDiff = $today->diffInDays($kirDate, false);

        if ($daysDiff < 0) {
            $daysAgo = abs($daysDiff);

            return [
                'status' => 'expired',
                'label' => "Kedaluwarsa ({$daysAgo} hari lalu)",
                'badge_class' => 'badge-legal-expired',
                'days_left' => $daysDiff,
            ];
        }

        if ($daysDiff <= 30) {
            return [
                'status' => 'expiring_soon',
                'label' => "Sisa {$daysDiff} hari",
                'badge_class' => 'badge-legal-warning',
                'days_left' => $daysDiff,
            ];
        }

        return [
            'status' => 'valid',
            'label' => 'Berlaku Baik',
            'badge_class' => 'badge-legal-valid',
            'days_left' => $daysDiff,
        ];
    }

    /**
     * Evaluasi status masa berlaku / jatuh tempo pajak STNK.
     *
     * @return array{status: string, label: string, badge_class: string, days_left: ?int}
     */
    public function stnkStatus(): array
    {
        if (! $this->stnk_tax_due_date) {
            return [
                'status' => 'unknown',
                'label' => 'Belum Diatur',
                'badge_class' => 'badge-legal-unknown',
                'days_left' => null,
            ];
        }

        $today = Carbon::today();
        $taxDate = Carbon::parse($this->stnk_tax_due_date)->startOfDay();
        $daysDiff = $today->diffInDays($taxDate, false);

        if ($daysDiff < 0) {
            $daysAgo = abs($daysDiff);

            return [
                'status' => 'expired',
                'label' => "Jatuh Tempo ({$daysAgo} hari lalu)",
                'badge_class' => 'badge-legal-expired',
                'days_left' => $daysDiff,
            ];
        }

        if ($daysDiff <= 30) {
            return [
                'status' => 'expiring_soon',
                'label' => "Sisa {$daysDiff} hari",
                'badge_class' => 'badge-legal-warning',
                'days_left' => $daysDiff,
            ];
        }

        return [
            'status' => 'valid',
            'label' => 'Berlaku Baik',
            'badge_class' => 'badge-legal-valid',
            'days_left' => $daysDiff,
        ];
    }

    /**
     * Menghitung total perjalanan yang ditugaskan kepada unit armada ini.
     */
    public function totalTripsCount(): int
    {
        return $this->schedules()->count();
    }

    /**
     * Relasi kursi armada.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Relasi jadwal perjalanan yang menggunakan kendaraan ini.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Memastikan data kursi (Seat) untuk unit armada ini telah terdaftar di database.
     * Jika belum ada, sistem akan membuat denah kursi realistis sesuai kapasitas unit.
     */
    public function ensureSeatsExist(bool $forceRecreate = false): void
    {
        $capacity = (int) ($this->capacity ?? 10);

        $existingCount = $this->seats()->count();
        if (! $forceRecreate && $existingCount === $capacity && $existingCount > 0) {
            return;
        }

        $this->seats()->delete();

        if ($capacity === 14) {
            // HiAce Commuter 14 Seats (Sesuai Foto Denah Asli)
            // Row 1: Kursi 1 (kiri), Driver di kanan
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '1', 'seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']);

            // Row 2: Kursi 4 (kiri), Kursi 3 (tengah), Kursi 2 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '4', 'seat_row' => '2', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '3', 'seat_row' => '2', 'seat_column' => 2, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '2', 'seat_row' => '2', 'seat_column' => 4, 'status' => 'Available']);

            // Row 3: Kursi 7 (kiri), Lorong, Kursi 6 (tengah), Kursi 5 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '7', 'seat_row' => '3', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '6', 'seat_row' => '3', 'seat_column' => 3, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '5', 'seat_row' => '3', 'seat_column' => 4, 'status' => 'Available']);

            // Row 4: Kursi 10 (kiri), Lorong, Kursi 9 (tengah), Kursi 8 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '10', 'seat_row' => '4', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '9', 'seat_row' => '4', 'seat_column' => 3, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '8', 'seat_row' => '4', 'seat_column' => 4, 'status' => 'Available']);

            // Row 5: Kursi 14 (kiri), Kursi 13, Kursi 12, Kursi 11 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '14', 'seat_row' => '5', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '13', 'seat_row' => '5', 'seat_column' => 2, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '12', 'seat_row' => '5', 'seat_column' => 3, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '11', 'seat_row' => '5', 'seat_column' => 4, 'status' => 'Available']);
        } elseif ($capacity === 10) {
            // HiAce Premio 10 Seats Full Bagasi (Sesuai Foto Denah Asli)
            // Row 1: Kursi 1 (kiri), Driver di kanan
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '1', 'seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']);

            // Row 2: Kursi 4 (kiri), Kursi 3 (tengah), Kursi 2 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '4', 'seat_row' => '2', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '3', 'seat_row' => '2', 'seat_column' => 2, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '2', 'seat_row' => '2', 'seat_column' => 4, 'status' => 'Available']);

            // Row 3: Kursi 7 (kiri), Lorong, Kursi 6 (tengah), Kursi 5 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '7', 'seat_row' => '3', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '6', 'seat_row' => '3', 'seat_column' => 3, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '5', 'seat_row' => '3', 'seat_column' => 4, 'status' => 'Available']);

            // Row 4: Kursi 10 (kiri), Lorong, Kursi 9 (tengah), Kursi 8 (kanan)
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '10', 'seat_row' => '4', 'seat_column' => 1, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '9', 'seat_row' => '4', 'seat_column' => 3, 'status' => 'Available']);
            Seat::create(['vehicle_id' => $this->id, 'seat_number' => '8', 'seat_row' => '4', 'seat_column' => 4, 'status' => 'Available']);
        } else {
            Seat::create([
                'vehicle_id' => $this->id,
                'seat_number' => '1',
                'seat_row' => '1',
                'seat_column' => 1,
                'status' => 'Available',
            ]);

            $seatNumber = 2;
            $row = 2;

            while ($seatNumber <= $capacity) {
                foreach ([1, 3, 4] as $column) {
                    if ($seatNumber > $capacity) {
                        break;
                    }

                    Seat::create([
                        'vehicle_id' => $this->id,
                        'seat_number' => (string) $seatNumber,
                        'seat_row' => (string) $row,
                        'seat_column' => $column,
                        'status' => 'Available',
                    ]);

                    $seatNumber++;
                }

                $row++;
            }
        }
    }

    /**
     * Mendapatkan daftar kursi yang dikelompokkan per baris (row).
     */
    public function seatMapRows()
    {
        $this->ensureSeatsExist();

        return $this->seats()->orderBy('seat_row')->orderBy('seat_column')->get()->groupBy('seat_row');
    }
}
