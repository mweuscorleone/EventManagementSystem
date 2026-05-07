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

    public function updateVenue(Request $request, $venueId){
        $userId = auth()->id();
        $venue = DB::table('venues')->where('id', $venueId)->first();

        if(!$venue){
            return response()->json([
                'message' => 'no venue found matches to your choice please try again!'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:venues,name',
            'venue_type' => 'sometimes|string|in:hotel,hall,conference_center,outdoor',
            'city' => 'sometimes|string',
            'location' => 'sometimes|string',
            'capacity' => 'sometimes|numeric|min:1',
            'description' => 'sometimes|string|max:255'
        ]);

        DB::table('venues')->where('id', $venue->id)->update([
            'name' => $request->name ?? $venue->name,
            'venue_type' => $request->venue_type ?? $venue->venue_type,
            'city' => $request->city ?? $venue->city,
            'location' => $request->location ?? $venue->location,
            'capacity' => $request->capacity ?? $venue->capacity,
            'description' => $request->description ??  $venue->description,
            'is_active' => $request->is_active ?? $venue->is_active,
            'updated_by' => $userId,
            'updated_at' => now()
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'venue updated successfully!'
        ], 200);
    
    }


    public function removeVenue($venueId){
        $venue = DB::table('venues')->where('id', $venueId)->first();

        if(!$venue){
            return response()->json([
                'message' => 'no venue found matching to your choice please try again'
            ],404);
        }

        DB::table('venues')->where('id', $venue->id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'venue deleted successfully'
        ], 200);
    }

    public function getVenues(){
        $venues = DB::table('venues')->get();


        return response()->json($venues);
    }
}
