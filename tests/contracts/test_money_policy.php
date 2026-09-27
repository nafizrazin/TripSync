<?php
require __DIR__.'/../../apps/api/app/Domain/Booking/FareCalculator.php';
require __DIR__.'/../../apps/api/app/Domain/Booking/CancellationPolicy.php';
use App\Domain\Booking\FareCalculator;
use App\Domain\Booking\CancellationPolicy;
$f=new FareCalculator();
assert($f->serviceFeeMinor(240000)===6000);
assert($f->totalMinor(240000,6000,0)===246000);
$p=new CancellationPolicy();
assert($p->refundPercent(25*60)===100);
assert($p->refundPercent(18*60)===80);
assert($p->refundPercent(8*60)===50);
assert($p->refundPercent(5*60)===0);
echo "ok\n";
