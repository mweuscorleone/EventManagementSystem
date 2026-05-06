<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'token',
        'is_used',
        'used_at'
    ];

    public function bookings(){
        return $this->belongsTo(Booking::class);
    }
}
