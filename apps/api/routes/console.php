<?php

use App\Application\Booking\ReleaseSeatHoldAction;
use App\Models\SeatHold;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function (): void {
    $action = app(ReleaseSeatHoldAction::class);

    SeatHold::query()
        ->where('status', 'active')
        ->where('expires_at', '<=', now())
        ->orderBy('expires_at')
        ->chunkById(100, function ($holds) use ($action): void {
            $holds->each(
                fn (SeatHold $hold) => $action->execute($hold, 'expired')
            );
        });
})
    ->name('seat-holds.release-expired')
    ->everyMinute()
    ->withoutOverlapping();
