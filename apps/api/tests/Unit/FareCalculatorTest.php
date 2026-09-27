<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Booking\FareCalculator;
use PHPUnit\Framework\TestCase;

final class FareCalculatorTest extends TestCase
{
    public function test_it_calculates_service_fee_and_total_in_minor_units(): void
    {
        $calculator = new FareCalculator();

        $subtotal = $calculator->decimalToMinor('1200.00');
        $fee = $calculator->serviceFeeMinor($subtotal);

        self::assertSame(120000, $subtotal);
        self::assertSame(3000, $fee);
        self::assertSame(123000, $calculator->totalMinor($subtotal, $fee));
        self::assertSame('1230.00', $calculator->minorToDecimal(123000));
    }

    public function test_discount_never_produces_a_negative_total(): void
    {
        $calculator = new FareCalculator();

        self::assertSame(0, $calculator->totalMinor(10000, 250, 20000));
    }
}
