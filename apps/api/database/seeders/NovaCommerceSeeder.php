<?php
namespace Database\Seeders;

use App\Models\{Booking,BookingPassenger,Payment,Refund,Role,Ticket,Trip,TripSeat,User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB,Hash};
use Illuminate\Support\Str;

final class NovaCommerceSeeder extends Seeder {
    private string $demoPassword;
    private const DEMO_CUSTOMER_TARGET = 30;
    private const BOOKING_TARGET = 650;

    public function run(): void {
        $this->demoPassword = $this->resolveDemoPassword();

        $customers = $this->seedDemoCustomers();
        if (Booking::query()->where('public_id','like','NOVA-BKG-%')->exists()) {
            return;
        }
        $trips = $this->candidateTrips();
        if ($trips->isEmpty()) {
            return;
        }
        for ($index = 0; $index < self::BOOKING_TARGET; $index++) {
            $trip = $trips[$index % $trips->count()];
            $customer = $customers[$index % $customers->count()];
            $this->createBookingScenario($index, $trip, $customer);
        }
        $this->seedAuditActivity();
    }


    private function resolveDemoPassword(): string {
        $password = env('DEMO_USER_PASSWORD');
        if (! is_string($password) || $password === '' || $password === 'tripsync_demo_change_me') {
            throw new \RuntimeException('DEMO_USER_PASSWORD must be set before seeding demo users.');
        }
        return $password;
    }

    private function seedDemoCustomers() {
        $role = Role::query()->where('name','customer')->firstOrFail();
        $customers = collect();
        $customers->push(User::query()->where('email','customer@tripsync.local')->firstOrFail());
        for ($index = 1; $index <= self::DEMO_CUSTOMER_TARGET; $index++) {
            $user = User::query()->updateOrCreate(
                ['email'=>sprintf('traveller%02d@tripsync.local',$index)],
                [
                    'name'=>sprintf('Demo Traveller %02d',$index),
                    'phone'=>'018'.str_pad((string)$index,8,'0',STR_PAD_LEFT),
                    'password'=>Hash::make($this->demoPassword),'email_verified_at'=>now(),'status'=>'active',
                ]
            );
            $user->roles()->syncWithoutDetaching([$role->id]);
            $customers->push($user);
        }
        return $customers;
    }

    private function candidateTrips() {
        $past = Trip::query()->where('departure_at','<',now())->latest('departure_at')->limit(50)->get();
        $future = Trip::query()->whereBetween('departure_at',[now(),now()->addDays(4)])->where('trip_status','scheduled')->orderBy('departure_at')->limit(70)->get();
        return $past->concat($future)->values();
    }

    private function createBookingScenario(int $index, Trip $trip, User $customer): void {
        DB::transaction(function () use ($index,$trip,$customer): void {
            $seat = TripSeat::query()->where('trip_id',$trip->id)->where('status','available')->orderBy('seat_number')->lockForUpdate()->first();
            if (!$seat) {
                return;
            }
            $isCancelled = $index % 11 === 0;
            $isPast = $trip->departure_at->lt(now());
            $status = $isCancelled ? 'cancelled' : ($isPast ? 'completed' : 'confirmed');
            $bookedAt = $trip->departure_at->copy()->subDays(($index % 8) + 1)->subMinutes($index % 55);
            $subtotal = (float)$seat->fare;
            $serviceFee = round($subtotal * 0.03, 2);
            $total = $subtotal + $serviceFee;

            $booking = Booking::query()->create([
                'public_id'=>'NOVA-BKG-'.str_pad((string)($index+1),6,'0',STR_PAD_LEFT),
                'booking_reference'=>'NV-'.str_pad((string)($index+1),7,'0',STR_PAD_LEFT),
                'user_id'=>$customer->id,'trip_id'=>$trip->id,'seat_hold_id'=>null,'status'=>$status,
                'subtotal'=>number_format($subtotal,2,'.',''),'discount_amount'=>'0.00','service_fee'=>number_format($serviceFee,2,'.',''),
                'total_amount'=>number_format($total,2,'.',''),'currency'=>'BDT','expires_at'=>null,
                'confirmed_at'=>$bookedAt->copy()->addMinutes(3),'cancelled_at'=>$isCancelled?$bookedAt->copy()->addHours(2):null,
            ]);
            $booking->timestamps = false;
            $booking->created_at = $bookedAt;
            $booking->updated_at = $isCancelled ? $bookedAt->copy()->addHours(2) : $bookedAt->copy()->addMinutes(3);
            $booking->saveQuietly();

            $passenger = BookingPassenger::query()->create([
                'booking_id'=>$booking->id,'full_name'=>$customer->name,'phone'=>$customer->phone ?: '01700009999','email'=>$customer->email,
            ]);
            DB::table('booking_seats')->insert([
                'id'=>(string)Str::ulid(),'booking_id'=>$booking->id,'passenger_id'=>$passenger->id,'trip_seat_id'=>$seat->id,'fare'=>$seat->fare,
            ]);

            $payment = Payment::query()->create([
                'public_id'=>'NOVA-PAY-'.str_pad((string)($index+1),6,'0',STR_PAD_LEFT),'booking_id'=>$booking->id,'provider'=>'simulation',
                'provider_transaction_id'=>'SIM-NOVA-'.str_pad((string)($index+1),8,'0',STR_PAD_LEFT),'idempotency_key'=>'seed-nova-'.($index+1),
                'amount'=>number_format($total,2,'.',''),'currency'=>'BDT','status'=>'succeeded','initiated_at'=>$bookedAt,'paid_at'=>$bookedAt->copy()->addMinutes(3),
                'metadata'=>['seeded'=>true,'scenario'=>$status],
            ]);
            DB::table('payment_events')->insert([
                'id'=>(string)Str::ulid(),'payment_id'=>$payment->id,'event_type'=>'payment.succeeded','provider_event_id'=>'NOVA-EVT-'.str_pad((string)($index+1),8,'0',STR_PAD_LEFT),
                'payload'=>json_encode(['source'=>'nova-seeder']),'occurred_at'=>$bookedAt->copy()->addMinutes(3),'created_at'=>$bookedAt,'updated_at'=>$bookedAt,
            ]);

            $ticket = Ticket::query()->create([
                'public_id'=>'NOVA-TKT-'.str_pad((string)($index+1),6,'0',STR_PAD_LEFT),'ticket_number'=>'TS-NV-'.str_pad((string)($index+1),7,'0',STR_PAD_LEFT),
                'booking_id'=>$booking->id,'passenger_id'=>$passenger->id,'trip_seat_id'=>$seat->id,'status'=>$isCancelled?'void':'issued',
                'qr_payload'=>'TRIPSYNC:NOVA:'.hash('sha256',$booking->booking_reference.':'.$passenger->id),'issued_at'=>$bookedAt->copy()->addMinutes(3),
            ]);
            unset($ticket);

            if ($isCancelled) {
                $seat->update(['status' => 'available','booking_id' => null,'hold_expires_at' => null,'version' => $seat->version + 1]);
                Refund::query()->create([
                    'payment_id'=>$payment->id,'booking_id'=>$booking->id,'amount'=>number_format($total*0.80,2,'.',''),'currency'=>'BDT',
                    'status'=>'succeeded','provider_refund_id'=>'SIM-REF-'.str_pad((string)($index+1),7,'0',STR_PAD_LEFT),'reason'=>'Seeded flexible cancellation','processed_at'=>$bookedAt->copy()->addHours(2),
                ]);
            } else {
                $seat->update(['status' => 'sold','booking_id' => $booking->id,'hold_expires_at' => null,'version' => $seat->version + 1]);
            }
        });
    }

    private function seedAuditActivity(): void {
        $actors = User::query()->whereIn('email',['operations@tripsync.local','finance@tripsync.local','admin@tripsync.local'])->get();
        $bookings = Booking::query()->where('public_id','like','NOVA-BKG-%')->latest()->limit(90)->get();
        $rows = [];
        foreach ($bookings as $index => $booking) {
            $actor = $actors[$index % max(1,$actors->count())];
            $rows[] = [
                'actor_id'=>$actor?->id,'action'=>['booking.reviewed','trip.manifest.checked','payment.reconciled'][$index%3],
                'entity_type'=>'booking','entity_id'=>$booking->id,'before_data'=>null,
                'after_data'=>json_encode(['seeded'=>true,'status'=>$booking->status]),'ip_address'=>'127.0.0.1','user_agent'=>'TripSync Nova Seeder',
                'request_id'=>'seed-nova-'.($index+1),'created_at'=>now()->subMinutes($index*7),
            ];
        }
        if ($rows) {
            DB::table('audit_logs')->insert($rows);
        }
    }
}
