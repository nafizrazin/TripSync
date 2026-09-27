<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Booking\CancellationPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CancellationPolicyTest extends TestCase
{
    #[DataProvider('refundCases')]
    public function test_refund_policy_is_deterministic(int $minutesUntilDeparture, int $expectedPercent): void
    {
        $policy = new CancellationPolicy();

        self::assertSame($expectedPercent, $policy->refundPercent($minutesUntilDeparture));
    }

    public static function refundCases(): array
    {
        return [
            '24 hours or more' => [1440, 100],
            '12 to 24 hours' => [720, 80],
            '6 to 12 hours' => [360, 50],
            'under 6 hours' => [359, 0],
        ];
    }
}
