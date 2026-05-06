<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
   //ADD OR CREATE AN EVENT 

    public function addEvent(Request $request){
        $userId = auth()->id();

        $request->validate([
            'organizer_id' => 'sometimes|numeric|exists:users,id',
            'title' => 'required|string|max:255|unique:events,title',
            'event_type_id' => 'required|numeric|exists:event_types,id',
            'event_date' => 'required|date',
            'end_datetime' => 'nullable|date|after:event_date',
            'venue_id' => 'required|numeric|exists:venues,id',
            'is_public' => 'boolean',
            'guest_limit' => 'required|numeric'
        ],
        [
            'title.unique' => 'event titile already exists please choose another title',
            'event_type_id.exists' => 'event type is not found please try again' ,
            'venue_id.exists' => 'venue not found please try again',
             'organizer_id.exitst' => 'user not found'
        ]);

         $venue =  DB::table('venues')->where('id', $request->venue_id)->first();

         if($request->guest_limit > $venue->capacity){
            return response()->json([
                'error' => 'Guest limit cannot exceed venue capacity'
            ], 400);

            
         }


        $id = DB::table('events')->insertGetId([
            'organizer_id' => $request->organizer_id ?? $userId,
            'title' => $request->title,
            'event_type_id' => $request->event_type_id,
            'event_date' => $request->event_date,
            'end_datetime' => $request->end_date,
            'venue_id' => $request->venue_id,
            'is_public' => $request->is_public ?? true,
            'guest_limit' => $request->guest_limit,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => $userId

        ],
        );

      


        $event = DB::table('events')->where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'event created successfully!',
            'event' => $event
        ], 201);
}

//UPDATING EVENT DETAILS

public function updateEvent(Request $request, $eventId){
    $userId = auth()->id();
    $event = DB::table('events')->where('id', $eventId)->first();

    if(!$event){
        return response()->json([
            'status' => 'failed',
            'message' => 'event not found'
        ], 404);
    }
     $request->validate([
            'organizer_id' => 'sometimes|numeric|exists:users,id',
            'title' => 'sometimes|string|max:255|unique:events,title',
            'event_type_id' => 'sometimes|numeric|exists:event_types,id',
            'event_date' => 'sometimes|date',
            'end_datetime' => 'nullable|date|after:event_date',
            'venue_id' => 'sometimes|numeric|exists:venues,id',
            'is_public' => 'sometimes|boolean',
            'guest_limit' => 'sometimes|numeric'
        ],
        [
            'title.unique' => 'event titile already exists please choose another title',
            'event_type_id.exists' => 'event type is not found please try again' ,
            'venue_id.exists' => 'venue not found please try again',
            'organizer_id.exitst' => 'user not found'
        ]);

        //check if venue_id or guest_limit is provided

        if($request->has('venue_id') || $request->has('guest_limit')){
            $venueId = $request->venue_id ?? $event->venue_id;

            $venue = DB::table('venues')->where('id', $venueId)->first();

            $guestLimit = $request->guest_limit ?? $event->guest_limit;

            if($guestLimit > $venue->capacity){
                return response()->json([
                    'message' => 'number of  guest exceed the venue capacity limit'
                ], 400);
            }
        }

      

        DB::table('events')->where('id', $eventId)->update([
                'title' => $request->title ?? $event->title,
                'organizer_id' => $request->organizer_id ?? $event->organizer_id,
                'event_type_id' => $request->event_type_id ?? $event->event_type_id,
                'event_date' => $reqeuest->event_date ?? $event->event_date,
                'end_datetime' => $request->end_datetime ?? $event->end_datetime,
                'venue_id'  => $request->venue->id ?? $event->venue_id,
                 'is_public' => $request->is_public ?? $event->is_public,
                'guest_limit' => $request->guest_limit ?? $event->guest_limit,
                'updated_by' => $userId,
                'updated_at' => now() 

        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'event updated successfully!'
        ], 200);
    }


    //REMOVE EVENT 

    public function removeEvent($eventId){
        $event = DB::table('events')->where('id', $eventId)->first();

        if(!$event){
            return response()->json([
                'message' => 'selected event is not found'
            ], 200);
        }

        DB::table('events')->where('id', $eventId)->delete();


        return response()->json([
            'status' => 'success',
            'message' => 'event removed successfully!'
        ], 200);
    }


    //DISPLAYING EVENTS 

    public function showEvents(){

        //
    }
    


    public function addEventType(Request $request){
        $userId = auth()->id();
        $request->validate([
            'name' => 'required|string|unique:event_types,name',
            'description' => 'required|string',
           
        ],
        [
            'name.unique' => 'event type already exists please try again'
        ]);

        $id = DB::table('event_types')->insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $event_type = DB::table('event_types')->where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'event type created successful!',
            'event_type' => $event_type

        ], 201);
    }


}
