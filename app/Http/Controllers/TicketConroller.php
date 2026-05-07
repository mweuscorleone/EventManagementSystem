<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EventTicketType;

class TicketConroller extends Controller
{
    //CREATE TICKET TYPE
    public function createTicketTypes(Request $request){

        $userId = auth()->id();

        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_types,name',
            'max_guest' => 'sometimes|numeric|min:1',
            'is_active' => 'sometimes|boolean'
        ],
        [
            'name.unique' => 'ticket type name already exist please try again!'
        ]);

        DB::table('ticket_types')->insert([
            'name' => $request->name,
            'max_guest' => $request->max_guest ?? 1,
            'is_active' => $request->is_active ?? true,
            'created_by' => $userId,
            'created_at' => now()
        ]);

        return  response()->json([
            'status' => 'success',
            'message' => 'ticket type created successfull!'
        ],201);
    

    }
    //UPDATE TICKET TYPE
    public function updateTicketType(Request $request, $ticketTypeId){
        $userId = auth()->id();
        $ticketType = DB::table('ticket_types')->where('id', $ticketTypeId)->first();

        if(!$ticketType){
            return response()->json([
                'message' => 'no matching ticket type found, please try again'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'max_guest' => 'sometimes|numeric|min:1',
            'is_acive' => 'sometimes|boolean'
                     
        ]);

        DB::table('ticket_types')->where('id', $ticketTypeId)->update([
            'name' => $request->name ?? $ticketType->name,
            'max_guest' => $request->max_guest ?? $ticketType->max_guest,
            'is_active' => $request->is_active ?? $ticketType->is_active,
            'updated_by' => $userId,
            'updated_at' => now()
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'ticket type updated succesfully!'
        ], 200);

    }
    //DELETE TICKET TYPE

    public function removeTickeType($ticketTypeId){
        $ticketType = DB::table('ticket_types')->where('id', $ticketTypeId)->first();

        if(!$ticketType){
            return response()->json([
                'message' => 'ticket type not found please try again'
            ]);
        }

        DB::table('ticket_types')->where('id', $ticketTypeId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ticket type deleted successfully!'
        ], 200);
    }

    //DISPLAYING ALL TICKET TYPES 

    public function showTickets(){
        $tickets = DB::table('ticket_types')->join('users', 'ticket_types.created_by', 'users.id')->where('is_active', true)->select(
            'ticket_types.name as Ticket_name',
            'ticket_types.max_guest as Max_guest',
            'users.name as Created_by',
            'ticket_types.created_at as create_at'
            
        )->get();

      return response()->json($tickets);
    }
    //ASSIGN TICKET TO EVENT
    public function assignTicket(Request $request, $ticketTypeId){
        $userId = auth()->id();
        $ticket_type = DB::table('ticket_types')->where('id', $ticketTypeId)->first();

        if(!$ticket_type){
            return response()->json([
                'message' => 'ticket type id not found!'
            ], 404);
        }

        $request->validate([
            'event_id' => 'required|numeric|exists:events,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            
        ],
        [
            'event_id.exists' => 'event not exist please try again'
        ]);

        DB::table('event_tickets')->updateOrInsert([
            'event_id' => $request->event_id,
            'ticket_type_id' => $ticketTypeId
        ],
        [
            'price' => $request->price,
            'quantity' => $request->quantity,
            'sold' => $request->sold ?? 0,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => now(),
            'updated_at' => now()
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'ticket assigned successfully!'
        ], 200);
    }

    //SHOW AVAILABLE EVENTS WITH TICKET INFORMATION

    public function showAssignedEventTickets(){
        $eventTickets = DB::table('event_tickets')
                        ->join('ticket_types', 'event_tickets.ticket_type_id', 'ticket_types.id')
                        ->join('events', 'event_tickets.event_id', '=', 'events.id')
                        ->join('event_types', 'events.event_type_id', '=', 'event_types.id')
                        ->join('venues', 'events.venue_id', '=', 'venues.id')
                        ->join('users', 'events.organizer_id', '=', 'users.id')
                       
                        ->select(
                            'events.id as EventNO',
                            'events.title as Event_name',
                            'event_types.name as Event_type',
                            'events.event_date as Event_date',
                            'events.end_datetime as Event_end_date',
                            'events.status as Status',
                            'events.guest_limit as Total_guests',
                            'venues.name as Venue',
                            'venues.venue_type as Venue_type',
                            'venues.city as Venue_city',
                            'venues.location as Venue_location',
                            'venues.capacity as Venue_capacity',
                            'ticket_types.name as Ticket_availabl',
                            'event_tickets.price as Price',
                            'users.name as Event_organizer'

                            

                        )->get();
        

        return response()->json($eventTickets);
    }

    //SHOW REMAINING TICKETS 

    public function getRemainingTickets(){

        $tickets = DB::table('event_tickets')->join('events', 'event_tickets.event_id', '=', 'events.id')
                    ->join('ticket_types', 'event_tickets.ticket_type_id', 'ticket_types.id')
                    ->select(
                        'events.title as Event',
                        'ticket_types.name as Ticket',
                        'event_tickets.quantity as Ticket_quantiy',
                        'event_tickets.sold as Sold_tickets',
                        DB::raw('(event_tickets.quantity - event_tickets.sold) as Remaining_tickets')
                    )->get();


        return response()->json($tickets);
    }
    //REMOVE TICKET TO EVENT 
    public function removeTicketAssignment($eventId, $ticketTypeId){
        $assignedTicket = DB::table('event_tickets')->where('event_id', $eventId)->where('ticket_type_id', $ticketTypeId)->first();

        if(!$assignedTicket){
            return response()->json([
                'message' => 'event has no ticket assigned matching to your choices please try again later'

            ],404);
        }

        DB::table('event_tickets')->where('event_id', $eventId)->where('ticket_type_id', $ticketTypeId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ticket assigment removed successfully'
        ], 200);
    }

}

