<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VenueControllelr;
use App\Http\Controllers\TicketConroller;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QRcodeController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//THESE ROUTES CAN ONLY BE ACCESSED BY ADMIN
Route::middleware(['auth:sanctum', 'role:admin'])->group(function(){
    Route::post('/create/user', [UserManagementController::class, 'createUser']);
    Route::put('update/user/{userId}', [UserManagementController::class, 'updateUser']);
    Route::delete('/delete/user/{userId}', [UserManagementController::class, 'removeUser']);
    Route::post('add/event/types', [EventController::class, 'addEventType']);
    Route::post('/add/venue', [VenueControllelr::class, 'addVenue']);
    Route::put('update/venue/{venueId}', [VenueControllelr::class, 'updateVenue']);
    Route::delete('/remove/venue/{venueId}', [VenueControllelr::class, 'removeVenue']);
    Route::get('get/venues' , [VenueControllelr::class, 'getVenues']);
});
Route::middleware(['auth:sanctum', 'role:admin,organizer'])->group(function(){
    Route::post('/add/event', [EventController::class, 'addEvent']);
    Route::put('/update/event/{eventId}', [EventController::class, 'updateEvent']);
    Route::delete('/remove/event/{eventId}', [EventController::class, 'removeEvent']);
    Route::post('/create/ticket/type', [TicketConroller::class, 'createTicketTypes']);
    Route::put('/update/ticket/type/{ticketType}', [TicketConroller::class, 'updateTicketType']);
    Route::get('/ticket/types', [TicketConroller::class, 'showTickets']);
    Route::delete('/remove/ticket/types/{ticketTypeId}', [TicketConroller::class, 'removeTickeType']);
    Route::post('assign/ticket/to/event/{ticketTypeId}', [TicketConroller::class, 'assignTicket']);
    Route::delete('remove/ticket/to/event/{eventId}/{ticketTypeId}', [TicketConroller::class, 'removeTicketAssignment']);
    Route::get('events/list/with/assigned/tickets', [TicketConroller::class, 'showAssignedEventTickets']);
    Route::get('remaining/ticket/event', [TicketConroller::class, 'getRemainingTickets']);
});

Route::middleware(['auth:sanctum', 'role:guest,admin'])->group(function (){
    Route::post('/book/event', [BookingController::class, 'book']);
    Route::post('/make/payment/{bookingId}', [PaymentController::class, 'makePayment']);
    Route::post('generate/qr-code/{bookId}', [QRcodeController::class, 'generateQrCode']);
});

Route::middleware(['auth:sanctum', 'role:scanner,admin'])->group(function(){
    Route::post('/scan/qr-code', [QRcodeController::class, 'scanQrcode']);
});

Route::post('user/login', [AuthController::class, 'login']);
Route::post('/reset/password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/user/logout', [AuthController::class, 'logout']);
});

        