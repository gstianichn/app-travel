<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'transaction_id',
        'payment_method',
        'gross_amount',
        'status',
        'paid_at',
        'expired_at',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'raw_response' => 'array',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
