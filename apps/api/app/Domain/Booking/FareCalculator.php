<?php
namespace App\Domain\Booking;
final class FareCalculator {
    public function decimalToMinor(string|int|float $amount): int { return (int) round(((float)$amount)*100); }
    public function minorToDecimal(int $minor): string { return number_format($minor/100,2,'.',''); }
    public function serviceFeeMinor(int $subtotalMinor): int { return (int) round($subtotalMinor*0.025); }
    public function totalMinor(int $subtotalMinor,int $serviceFeeMinor,int $discountMinor=0): int { return max(0,$subtotalMinor+$serviceFeeMinor-$discountMinor); }
}
