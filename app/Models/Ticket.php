<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'ticket_number',
        'qr_code',
        'status',
        'boarded_at',
    ];

    protected function casts(): array
    {
        return [
            'boarded_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
