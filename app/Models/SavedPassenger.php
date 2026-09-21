<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nik',
        'phone',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tampilkan NIK dengan format sensor untuk keamanan data.
     */
    public function maskedNik(): string
    {
        if (! $this->nik) {
            return '-';
        }

        $len = strlen($this->nik);
        if ($len <= 6) {
            return $this->nik;
        }

        return substr($this->nik, 0, 4).str_repeat('•', max(0, $len - 8)).substr($this->nik, -4);
    }
}
