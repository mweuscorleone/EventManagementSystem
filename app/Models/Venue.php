<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Venue extends Model
{   use HasFactory;

    protected $fillable = [
        'name',
        'venue_type',
        'city',
        'location',
        'capacity',
        "description",
        'is_active',
        'created_by',
        'updated_by'
    ];
    

    public function events(){
       return $this->hasMany(Event::class);
    }
    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updator(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
