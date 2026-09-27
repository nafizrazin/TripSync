<?php
namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class NovaSeederTest extends TestCase {
    public function test_nova_seed_dataset_exceeds_portfolio_demo_thresholds(): void {
        $this->artisan('migrate:fresh');
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(12, DB::table('operators')->count());
        $this->assertGreaterThanOrEqual(36, DB::table('buses')->count());
        $this->assertGreaterThanOrEqual(24, DB::table('routes')->count());
        $this->assertGreaterThanOrEqual(600, DB::table('trips')->count());
        $this->assertGreaterThanOrEqual(650, DB::table('bookings')->count());
        $this->assertGreaterThanOrEqual(500, DB::table('payments')->where('status','succeeded')->count());
        $this->assertGreaterThanOrEqual(500, DB::table('tickets')->count());
        $this->assertSame(0, DB::table('trip_seats')->where('status','sold')->whereNull('booking_id')->count());
        $this->assertSame(0, DB::table('bookings')->whereIn('status',['confirmed','completed'])->whereNotExists(function ($query): void {
            $query->selectRaw('1')->from('payments')->whereColumn('payments.booking_id','bookings.id')->where('payments.status','succeeded');
        })->count());
    }
}
