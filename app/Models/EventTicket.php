<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'price',
        'quantity',
        'sold',
        'created_by',
        'updated_by'
    ];

    public function events(){
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function ticket_types(){
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }
    public function bookings(){
        return $this->hasMany(Booking::class, 'event_ticket_id');
    }
    public function creator(){
        return $this-> belongsTo(User::class, 'created_by');
    }
    public function updator(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
