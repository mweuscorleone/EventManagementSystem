<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function book(Request $request){
        $guestId = auth()->id();

        $request->validate([
            'event_ticket_id' => 'required|numeric|exists:event_tickets,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        $ticket = DB::table('event_tickets')->where('id', $request->event_ticket_id)->first();

        if(!$ticket){
            return response()->json([
                'message' => 'no ticket available matching your choice please try again'
            ], 404);
        }

        if($ticket->quantity !== null && $ticket->sold >= $ticket->quantity){
            return response()->json([
                'message' => 'ticket sold out'
            ], 404);
        }

        $totalAmount = $ticket->price * $request->quantity;

        $bookingId = DB::table('bookings')->insertGetId([
            'guest_id' => $guestId,
            'event_ticket_id' => $request->event_ticket_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'total_amount' => $totalAmount,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'event booked successfully',
            'bookId' => $bookingId,
            'total_amount' => $totalAmount
        ], 200);


    }
}
