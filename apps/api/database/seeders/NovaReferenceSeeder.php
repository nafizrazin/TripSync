<?php
namespace Database\Seeders;

use App\Models\{Bus,Location,Operator,Role,Schedule,SeatLayout,TransportRoute,Trip,TripSeat,User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB,Hash};
use Illuminate\Support\Str;

final class NovaReferenceSeeder extends Seeder {
    private string $demoPassword;
    private const LOCATION_COUNT_TARGET = 20;
    private const OPERATOR_COUNT_TARGET = 12;
    private const BUS_COUNT_TARGET = 36;
    private const ROUTE_COUNT_TARGET = 24;
    private const TRIP_DAY_SPAN = 28;

    public function run(): void {
        $this->demoPassword = $this->resolveDemoPassword();

        DB::transaction(function (): void {
            $roles = $this->seedRolesAndFixedUsers();
            $locations = $this->seedLocations();
            $operators = $this->seedOperators();
            $layouts = $this->seedSeatLayouts();
            $buses = $this->seedBuses($operators, $layouts);
            $routes = $this->seedRoutes($locations);
            $this->seedSchedulesTripsAndSeats($routes, $buses);
            unset($roles);
        });
    }


    private function resolveDemoPassword(): string {
        $password = env('DEMO_USER_PASSWORD');
        if (! is_string($password) || $password === '' || $password === 'tripsync_demo_change_me') {
            throw new \RuntimeException('DEMO_USER_PASSWORD must be set before seeding demo users.');
        }
        return $password;
    }

    private function seedRolesAndFixedUsers(): array {
        $roleDefs = [
            'customer' => 'Customer',
            'operations_admin' => 'Operations Admin',
            'finance_admin' => 'Finance Admin',
            'super_admin' => 'Super Admin',
        ];
        $roles = [];
        foreach ($roleDefs as $name => $label) {
            $roles[$name] = Role::query()->updateOrCreate(['name' => $name], ['label' => $label]);
        }

        $users = [
            ['Demo Customer','customer@tripsync.local','01700000001','customer'],
            ['Operations Admin','operations@tripsync.local','01700000002','operations_admin'],
            ['Finance Admin','finance@tripsync.local','01700000003','finance_admin'],
            ['Super Admin','admin@tripsync.local','01700000004','super_admin'],
        ];
        foreach ($users as [$name,$email,$phone,$role]) {
            $user = User::query()->updateOrCreate(
                ['email' => $email],
                ['name' => $name,'phone' => $phone,'password' => Hash::make($this->demoPassword),'email_verified_at' => now(),'status' => 'active']
            );
            $user->roles()->syncWithoutDetaching([$roles[$role]->id]);
        }
        return $roles;
    }

    private function seedLocations(): array {
        $names = [
            'Dhaka','Chattogram','Coxs Bazar','Sylhet','Rajshahi','Khulna','Rangpur','Mymensingh','Barishal','Cumilla',
            'Bogura','Jessore','Kushtia','Noakhali','Feni','Gazipur','Narayanganj','Tangail','Dinajpur','Bandarban',
        ];
        $result = [];
        foreach ($names as $name) {
            $result[$name] = Location::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name,'district' => $name,'is_active' => true]
            );
        }
        return $result;
    }

    private function seedOperators(): array {
        $defs = [
            ['Green Line','GL','Premium intercity comfort','#22d3ee',4.86,4821,96.4],
            ['Shohagh Paribahan','SH','Trusted journeys since 1973','#8b5cf6',4.71,3910,93.8],
            ['Hanif Enterprise','HF','Nationwide routes, dependable service','#10b981',4.62,5422,91.9],
            ['Ena Transport','ENA','Fast connections across Bangladesh','#f59e0b',4.55,3104,90.6],
            ['Saintmartin Hyundai','SM','Premium coaches for long routes','#38bdf8',4.79,2490,95.1],
            ['Desh Travels','DT','Comfortable everyday travel','#f97316',4.48,2818,89.7],
            ['Shyamoli NR Travels','SN','Extensive nationwide network','#14b8a6',4.58,3674,92.2],
            ['Royal Coach','RC','Business-class road travel','#a78bfa',4.73,1980,94.3],
            ['Nabil Paribahan','NB','Northern routes, modern coaches','#60a5fa',4.51,2244,90.9],
            ['Soudia Coach','SC','Coastal connections with comfort','#34d399',4.46,1710,89.4],
            ['Unique Service','US','Frequent departures, flexible travel','#fb7185',4.39,1555,88.8],
            ['Tuba Line','TL','Reliable regional connectivity','#facc15',4.32,1180,87.9],
        ];
        $result = [];
        foreach ($defs as [$name,$code,$tagline,$color,$rating,$reviews,$punctuality]) {
            $result[$name] = Operator::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,'short_code' => $code,'phone' => '09612'.str_pad((string)(count($result)+1),5,'0',STR_PAD_LEFT),
                    'email' => Str::slug($name).'@tripsync.local','tagline' => $tagline,'brand_color' => $color,
                    'rating' => $rating,'review_count' => $reviews,'punctuality_percent' => $punctuality,'status' => 'active',
                ]
            );
        }
        return $result;
    }

    private function seedSeatLayouts(): array {
        $definitions = [
            'Executive 40' => $this->layoutDefinition(10, [1,2,4,5], 'standard'),
            'Business 32' => $this->layoutDefinition(8, [1,2,4,5], 'business'),
            'Premium 28' => $this->premiumLayoutDefinition(),
        ];
        $result = [];
        foreach ($definitions as $name => $seats) {
            $layout = SeatLayout::query()->updateOrCreate(
                ['name' => $name],
                ['seat_count' => count($seats),'columns' => 5,'status' => 'active']
            );
            if ($layout->seats()->count() !== count($seats)) {
                $layout->seats()->delete();
                $now = now();
                DB::table('seat_layout_seats')->insert(array_map(fn(array $seat): array => $seat + [
                    'id' => (string) Str::ulid(),'seat_layout_id' => $layout->id,
                ], $seats));
            }
            $result[$name] = $layout->refresh();
        }
        return $result;
    }

    private function layoutDefinition(int $rows, array $columns, string $seatType): array {
        $letters = [1 => 'A', 2 => 'B', 4 => 'C', 5 => 'D'];
        $seats = [];
        for ($row = 1; $row <= $rows; $row++) {
            foreach ($columns as $column) {
                $seats[] = [
                    'seat_number' => $row.$letters[$column],'row_number' => $row,'column_number' => $column,'deck' => 1,
                    'seat_type' => $seatType,'is_window' => in_array($column,[1,5],true),'is_aisle' => in_array($column,[2,4],true),'is_active' => true,
                ];
            }
        }
        return $seats;
    }

    private function premiumLayoutDefinition(): array {
        $letters = [1 => 'A', 2 => 'B', 4 => 'C', 5 => 'D'];
        $seats = [];
        for ($row = 1; $row <= 8; $row++) {
            foreach ([1,2,5] as $column) {
                $seats[] = ['seat_number'=>$row.$letters[$column],'row_number'=>$row,'column_number'=>$column,'deck'=>1,'seat_type'=>'premium','is_window'=>in_array($column,[1,5],true),'is_aisle'=>$column===2,'is_active'=>true];
            }
        }
        foreach ([1,2,4,5] as $column) {
            $seats[] = ['seat_number'=>'9'.$letters[$column],'row_number'=>9,'column_number'=>$column,'deck'=>1,'seat_type'=>'premium','is_window'=>in_array($column,[1,5],true),'is_aisle'=>in_array($column,[2,4],true),'is_active'=>true];
        }
        return $seats;
    }

    private function seedBuses(array $operators, array $layouts): array {
        $amenitySets = [
            ['WiFi','USB','Water','Recliner','AC'],
            ['USB','Water','Recliner','AC'],
            ['WiFi','USB','Blanket','Water','Recliner','AC'],
            ['USB','AC','Charging Port'],
        ];
        $comforts = ['executive','business','premium'];
        $operatorList = array_values($operators);
        $layoutList = array_values($layouts);
        $buses = [];
        for ($index = 0; $index < self::BUS_COUNT_TARGET; $index++) {
            $operator = $operatorList[$index % count($operatorList)];
            $layout = $layoutList[$index % count($layoutList)];
            $sequence = $index + 1;
            $buses[] = Bus::query()->updateOrCreate(
                ['registration_number' => 'DHAKA-NOVA-'.str_pad((string)$sequence,3,'0',STR_PAD_LEFT)],
                [
                    'operator_id' => $operator->id,'seat_layout_id' => $layout->id,
                    'label' => ($operator->short_code ?: 'TS').'-'.str_pad((string)$sequence,3,'0',STR_PAD_LEFT),
                    'model' => ['Hyundai Universe','Hino RM2','Scania K410','Volvo B11R'][$index % 4],
                    'bus_type' => $index % 5 === 0 ? 'non_ac' : 'ac',
                    'comfort_class' => $comforts[$index % 3],
                    'amenities' => $amenitySets[$index % count($amenitySets)],
                    'status' => 'active',
                ]
            );
        }
        return $buses;
    }

    private function seedRoutes(array $locations): array {
        $defs = [
            ['Dhaka','Chattogram',360,1180,264],['Chattogram','Dhaka',360,1160,264],
            ['Dhaka','Coxs Bazar',600,1680,390],['Coxs Bazar','Dhaka',600,1650,390],
            ['Dhaka','Sylhet',300,980,240],['Sylhet','Dhaka',300,960,240],
            ['Dhaka','Rajshahi',330,1120,245],['Rajshahi','Dhaka',330,1100,245],
            ['Dhaka','Khulna',390,1180,271],['Khulna','Dhaka',390,1160,271],
            ['Dhaka','Rangpur',420,1250,304],['Rangpur','Dhaka',420,1230,304],
            ['Dhaka','Barishal',300,900,169],['Barishal','Dhaka',300,890,169],
            ['Dhaka','Mymensingh',180,620,121],['Mymensingh','Dhaka',180,610,121],
            ['Chattogram','Coxs Bazar',240,780,150],['Coxs Bazar','Chattogram',240,760,150],
            ['Dhaka','Bogura',300,980,197],['Bogura','Dhaka',300,960,197],
            ['Dhaka','Jessore',360,1080,211],['Jessore','Dhaka',360,1060,211],
            ['Dhaka','Cumilla',180,650,100],['Cumilla','Dhaka',180,640,100],
        ];
        $routes = [];
        foreach ($defs as $index => [$from,$to,$duration,$fare,$distance]) {
            $code = 'R'.str_pad((string)($index+1),2,'0',STR_PAD_LEFT).'-'.strtoupper(substr(Str::slug($from,''),0,2).substr(Str::slug($to,''),0,2));
            $routes[] = [
                'model' => TransportRoute::query()->updateOrCreate(
                    ['code' => $code],
                    ['origin_location_id'=>$locations[$from]->id,'destination_location_id'=>$locations[$to]->id,'estimated_duration_minutes'=>$duration,'distance_km'=>$distance,'status'=>'active']
                ),
                'fare' => $fare,
            ];
        }
        return $routes;
    }

    private function seedSchedulesTripsAndSeats(array $routes, array $buses): void {
        $today = Carbon::today('Asia/Dhaka');
        $startDate = $today->copy()->subDays(7);
        $departures = [7, 20];
        foreach ($routes as $routeIndex => $routeData) {
            $route = $routeData['model'];
            foreach ($departures as $slotIndex => $hour) {
                $bus = $buses[(($routeIndex * 2) + $slotIndex) % count($buses)];
                $fare = $routeData['fare'] + ($slotIndex * 80) + (($routeIndex % 3) * 40);
                $schedule = Schedule::query()->updateOrCreate(
                    ['route_id'=>$route->id,'bus_id'=>$bus->id,'departure_time'=>sprintf('%02d:00:00',$hour)],
                    [
                        'arrival_time'=>Carbon::createFromTime($hour,0,'Asia/Dhaka')->addMinutes($route->estimated_duration_minutes)->format('H:i:s'),
                        'days_of_week'=>[0,1,2,3,4,5,6],'base_fare'=>$fare,'effective_from'=>$startDate,'status'=>'active',
                    ]
                );
                for ($dayOffset = 0; $dayOffset < self::TRIP_DAY_SPAN; $dayOffset++) {
                    $departure = $startDate->copy()->addDays($dayOffset)->setTime($hour,0);
                    $isPast = $departure->lt(now('Asia/Dhaka'));
                    $trip = Trip::query()->updateOrCreate(
                        ['public_id'=>'TRP-'.$route->code.'-'.$departure->format('ymd').'-'.($slotIndex+1)],
                        [
                            'schedule_id'=>$schedule->id,'route_id'=>$route->id,'bus_id'=>$bus->id,
                            'departure_at'=>$departure,'arrival_at'=>$departure->copy()->addMinutes($route->estimated_duration_minutes),
                            'trip_status'=>$isPast?'completed':'scheduled','booking_status'=>$isPast?'closed':'open','base_fare'=>$fare,
                        ]
                    );
                    if ($trip->seats()->exists()) {
                        continue;
                    }
                    $this->createTripSeats($trip, $bus, (float)$fare, $routeIndex, $dayOffset);
                }
            }
        }
    }

    private function createTripSeats(Trip $trip, Bus $bus, float $fare, int $routeIndex, int $dayOffset): void {
        $layoutSeats = $bus->seatLayout()->firstOrFail()->seats()->where('is_active',true)->orderBy('row_number')->orderBy('column_number')->get();
        $now = now();
        $rows = [];
        foreach ($layoutSeats as $index => $seat) {
            $premiumDelta = $seat->seat_type === 'premium' ? 180 : ($seat->is_window ? 40 : 0);
            $blocked = (($routeIndex + $dayOffset + $index) % 97) === 0;
            $rows[] = [
                'id'=>(string)Str::ulid(),'trip_id'=>$trip->id,'seat_layout_seat_id'=>$seat->id,'booking_id'=>null,
                'seat_number'=>$seat->seat_number,'fare'=>number_format($fare + $premiumDelta,2,'.',''),'status'=>$blocked?'blocked':'available',
                'hold_expires_at'=>null,'version'=>1,'created_at'=>$now,'updated_at'=>$now,
            ];
        }
        TripSeat::query()->insert($rows);
    }
}
