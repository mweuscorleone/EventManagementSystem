<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    public function makePayment(Request $request, $bookingId){
       $booking = DB::table('bookings')->where('id', $bookingId)->first();

       if(!$booking){
        return response()->json([
            'message' => 'you have not yet book for an event'
        ], 200);
       }
       if($booking->status === 'confirmed'){
        return response()->json([
            'message' => 'booking already paid'
        ], 400);
       }

       $request->validate([
        'payment_method' => 'sometimes|string|in:direct_cash,credit_card,mobile_payment'
       ]);
       $eventTicket = DB::table('event_tickets')->where('id', $booking->event_ticket_id)->first();

       if($eventTicket->quantity !== null && $eventTicket->sold + $booking->quantity > $eventTicket->quantity){
        return response()->json([
            'status' => 'failed',
            'message' => 'not enough tickets, please try again later'
        ], 400);
       }

       DB::beginTransaction();

       try{
            DB::table('payments')->insert([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'payment_method' => $request->payment_method ?? 'direct_cash',
            'status' => 'paid',
            'transaction_id' => Str::uuid(),
            'created_at' => now(),
            'updated_at' => now()

      ]);

      DB::table('bookings')->join('event_tickets', 'bookings.event_ticket_id', '=', 'bookings.id')
            ->where('bookings.id', $booking->id)
            ->increment('event_tickets.sold', $booking->quantity);

     DB::table('bookings')->where('id', $booking->id)->update([
        'status' => 'confirmed',
        'updated_at' => now()
     ]);
     

     DB::commit();

    return response()->json([
        'status' => 'success',
        'message' => 'payment made successfully!'
    ], 200);


       }

    catch (Exception $e){

         DB::rollBack();

        Log::error('payment-error' . $e->getMessage());

       

        return response()->json([
            'status' => 'failed',
            'message' => 'something went wrong',
            'error' => $e->getMessage()
        ], 500);

        
    }
      

    }
}
