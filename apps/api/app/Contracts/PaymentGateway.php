<?php
namespace App\Contracts;
use App\Models\{Booking,Payment,Refund};
interface PaymentGateway { public function initiate(Booking $booking,string $idempotencyKey): Payment; public function refund(Payment $payment,int $amountMinor,string $reason): Refund; }
