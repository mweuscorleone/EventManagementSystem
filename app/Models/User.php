<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organizeEvents(){
        return $this->hasMany(Event::class, 'organizer_id');
    }
    public function bookings(){
       return $this->hasMany(Booking::class, 'guest_id');
    }
    public function createdEvents(){
        return $this->hasMany(Event::class, 'created_by');

    }
    public function updatedEvents(){
        return $this->hasMany(Event::class, 'updated_by');
    }
      public function createdVenues(){
        return $this->hasMany(Venue::class, 'created_by');
    }
    public function updatedVenues(){
        return $this->hasMany(Venue::class, 'updated_by');
    }
     public function create_ticket_types(){
        return $this->hasMany(TicketType::class, 'created_by');
    }
    public function update_ticket_types(){
        return $this->hasMany(TicketType::class, 'updated_by');
    }
     public function create_event_tickets(){
        return $this->hasMany(EventTicket::class, 'created_by');
    }
    public function update_event_tickets(){
        return $this->hasMany(EventTicket::class, 'updated_by');

    }

    public function scan_qrCodes(){
        return $this->hasMany(QrCode::class, 'scanned_by');
    }
     
}
