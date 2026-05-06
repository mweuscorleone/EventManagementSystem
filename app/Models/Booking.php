<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{   use HasFactory;
    protected $fillable = [
        'guest_id',
        'event_ticket_id',
        'quantity',
        'total_amount',
        'status'
    ];


  
    public function guests(){
       return $this->belongsTo(User::class, 'guest_id');
    }
    public function event_tickets(){
        return $this->belongsTo(EventTicket::class, 'event_ticket_id');
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
    public function qrcodes(){
        return $this->hasMany(QrCode::class);
        
    }
}
