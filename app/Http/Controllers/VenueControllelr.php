<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venue;

class VenueControllelr extends Controller
{
    public function addVenue(Request $request){
        $userId = auth()->id();
        $request->validate([
            'name' => 'required|string|max:255|unique:venues,name',
            'venue_type' => 'required|string|in:hotel,conference_center,outdoor,hall',
            'city' => 'required|string',
            'location' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:1',
            'description' => 'nullable|string'
            
        ],
        [
            'name.unique' => 'venue name already exist please try again',
            'venue_type.in' => 'please select venue type in [hotel,conference_center,outdoor,hall]',
        ]);


        $id = DB::table('venues')->insertGetId([
            'name' => $request->name,
            'venue_type' => $request->venue_type,
            'city' => $request->city,
            'location' => $request->location,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $venue = DB::table('venues')->where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'venue created successfully!',
            'venue' => $venue
        ], 201);

    
    }
    
}
