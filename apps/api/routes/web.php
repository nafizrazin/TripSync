<?php
use App\Http\Controllers\Api\V1\{AdminController,AuthController,BookingController,MetaController,PaymentController,SeatHoldController,TripController}; use Illuminate\Support\Facades\Route;
Route::get('/',fn()=>response()->json(['service'=>'TripSync API']));
Route::prefix('api/v1')->group(function (): void {
    Route::get('auth/csrf',[AuthController::class,'csrf']); Route::post('auth/register',[AuthController::class,'register'])->middleware('throttle:register'); Route::post('auth/login',[AuthController::class,'login'])->middleware('throttle:login');
    Route::get('locations',[MetaController::class,'locations']); Route::get('discovery/overview',[MetaController::class,'discoveryOverview']); Route::get('trips/search',[TripController::class,'search']); Route::get('trips/fare-calendar',[TripController::class,'fareCalendar']); Route::get('trips/{trip}/seats',[TripController::class,'seats']);
    Route::middleware('auth')->group(function (): void {
        Route::get('auth/me',[AuthController::class,'me']); Route::post('auth/logout',[AuthController::class,'logout']);
        Route::post('seat-holds',[SeatHoldController::class,'store']); Route::get('seat-holds/{seatHold}',[SeatHoldController::class,'show']); Route::delete('seat-holds/{seatHold}',[SeatHoldController::class,'destroy']);
        Route::get('bookings',[BookingController::class,'index']); Route::post('bookings',[BookingController::class,'store']); Route::get('bookings/{booking}',[BookingController::class,'show']); Route::post('bookings/{booking}/cancel',[BookingController::class,'cancel']);
        Route::post('bookings/{booking}/payments',[PaymentController::class,'store']); Route::post('payments/{payment}/simulate',[PaymentController::class,'simulate']);
        Route::prefix('admin')->middleware('role:operations_admin,finance_admin,super_admin')->group(function (): void {
            Route::get('overview',[AdminController::class,'overview']); Route::get('bookings',[AdminController::class,'bookings']); Route::get('audit-logs',[AdminController::class,'auditLogs']);
            Route::get('trips',[AdminController::class,'trips'])->middleware('role:operations_admin,super_admin');
            Route::get('payments',[AdminController::class,'payments'])->middleware('role:finance_admin,super_admin');
        });
    });
});
