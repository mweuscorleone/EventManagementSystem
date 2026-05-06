<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'titile',
        'event_type_id',
        'event_date',
        'end_datetime',
        'status',
        'venue_id',
        'guest_limit',
        'is_public',
        'created_by',
        'updated_by'
    ];

    public function event_organizers(){
       return $this->belongsTo(User::class, 'organizer_id');
    }
    public function event_types(){
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
    public function venues(){
        return $this->belongsTo(Venue::class);
    }
    public function ticket_types(){
       return $this->hasMany(TicketType::class, 'event_id');
    }
    public function bookings(){
       return $this->hasMany(Booking::class);
    }
    public function invitations(){
        return $this->hasMany(Invitation::class);
    }
    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updator(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
