<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;
use Illuminate\Support\Facades\Log;

class QRcodeController extends Controller
{
    public function generateQrCode($bookingId){
        $booking = DB::table('bookings')->where('id', $bookingId)->first();

        if(!$booking){
            return response()->json([
                'message' => 'no booking found, matching to your choice'
            ],404);
        };

        if($booking->status !== 'confirmed'){
            return response()->json([
                'message' => 'payment is not completed, please make payment first'
            ], 400);
        }


        $existingQrcode = DB::table('qr_codes')->where('booking_id', $bookingId)->first();

        if($existingQrcode){
            return response()->json([
                'message' => 'QR code already generated'
            ], 400);
        }

        DB::beginTransaction();

        try{
            $qrCode = Str::uuid();




            DB::table('qr_codes')->insert([
            'booking_id' => $booking->id,
            'token' => $qrCode,
            'is_used' =>  false,
            'created_at' => now(),
            'updated_at' => now()
            ]);

    

        //generate Qr image

         $qrImage = QrCode::size(300)->generate($qrCode);

       
         return response()->json([
            'status' => 'success',
          'message' => 'qrcode generated successfully!',
             'token' => $qrCode,
            'qr_image' => $qrImage
         ], 200);

         DB::commit();
        }

        catch(Exception $e){
            DB::rollBack();

            Log::error('qr-generation-error' . $e->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
        
        
    }

    public function scanQrcode(Request $request){
        $userId = auth()->id();
        $request->validate([
            'token' => 'required|string|max:255'
        ]);

        $qrcode = DB::table('qr_codes')->where('token', $request->token)->first();

        if(!$qrcode){
            return response()->json([
                'message' => 'token not found please try again'
            ], 404);
        }
        if($qrcode->is_used){
            return response()->json([
                'message' => 'qr code already used, please try again later'
            ], 400);
        }
        DB::beginTransaction();
    
        try{
            $booking = DB::table('bookings')->where('id', $qrcode->booking_id)->first();
            $ticket = DB::table('event_tickets')->where('id', $booking->event_ticket_id)->first();
            $event = DB::table('events')->where('id', $ticket->event_id)->first();
            $payment = DB::table('payments')->where('booking_id', $booking->id)->first();
            $venue = DB::table('venues')->where('id', $event->venue_id)->first();
            $guest = DB::table('users')->where('id', $booking->guest_id)->
                select('name')->first();

            DB::table('qr_codes')->where('token', $request->token)
            ->update([
            'is_used' => true,
            'used_at' => now(),
            'scanned_by' => $userId,
            'created_at'=> now(),
            'updated_at' => now()
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'qrcode scanned succefully!',
            'guest' => $guest,
            'event' => $event->title,
            'venue' => $venue->name,
            'status' => $payment->status,
            'booking status' => $booking->status,
            'paid_amount' => $booking->total_amount,
            'paid date' => $payment->created_at
        ], 200);

            
        }

        catch (Exception $e) {
            DB::rollBack();

            Log::error('scan-error'. $e->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
       
        
    }
}
