<?php
namespace App\Domain\Booking;
final class CancellationPolicy {
    public function refundPercent(int $minutesUntilDeparture): int { return match(true){ $minutesUntilDeparture>=1440=>100,$minutesUntilDeparture>=720=>80,$minutesUntilDeparture>=360=>50,default=>0 }; }
    public function refundMinor(int $paidMinor,int $minutesUntilDeparture): int { return intdiv($paidMinor*$this->refundPercent($minutesUntilDeparture),100); }
}
