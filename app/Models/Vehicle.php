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
            'Isuzu Elf Short',
            'Isuzu Elf Long',
            'Mercedes-Benz Sprinter',
            'Mitsubishi Colt L300',
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
    public function ensureSeatsExist(): void
    {
        $existingCount = $this->seats()->count();
        if ($existingCount >= ($this->capacity ?? 10) && $existingCount > 0) {
            return;
        }

        $capacity = max(4, $this->capacity ?? 10);
        $this->seats()->delete();

        // Row 1: Kursi 1A di samping driver
        Seat::create([
            'vehicle_id' => $this->id,
            'seat_number' => '1A',
            'seat_row' => '1',
            'seat_column' => 3,
            'status' => 'Available',
        ]);

        $created = 1;
        $currentRow = 2;
        $letters = ['A', 'B', 'C', 'D'];

        while ($created < $capacity) {
            $seatsInThisRow = 2;
            if ($capacity > 10 && $capacity <= 15) {
                $seatsInThisRow = 3;
            } elseif ($capacity > 15) {
                $seatsInThisRow = 4;
            }

            $remaining = $capacity - $created;
            if ($remaining <= 4) {
                $seatsInThisRow = $remaining;
            }

            for ($col = 0; $col < $seatsInThisRow && $created < $capacity; $col++) {
                $seatNumber = $currentRow.$letters[$col];
                Seat::create([
                    'vehicle_id' => $this->id,
                    'seat_number' => $seatNumber,
                    'seat_row' => (string) $currentRow,
                    'seat_column' => $col + 1,
                    'status' => 'Available',
                ]);
                $created++;
            }
            $currentRow++;
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
