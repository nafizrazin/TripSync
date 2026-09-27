<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Booking,Payment,Trip};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{DB,Redis};

final class AdminController extends Controller {
    public function overview(): JsonResponse {
        $now = now('Asia/Dhaka');
        $today = $now->copy()->startOfDay();
        $week = $now->copy()->addDays(7)->endOfDay();
        $confirmed = Booking::query()->whereIn('status',['confirmed','completed']);
        $sold = DB::table('trip_seats')->join('trips','trips.id','=','trip_seats.trip_id')->whereBetween('trips.departure_at',[$now,$week])->where('trip_seats.status','sold')->count();
        $capacity = DB::table('trip_seats')->join('trips','trips.id','=','trip_seats.trip_id')->whereBetween('trips.departure_at',[$now,$week])->count();

        $recent = Booking::query()->with(['trip.route.origin','trip.route.destination','user:id,name,email'])->latest()->limit(8)->get()->map(fn($b)=>[
            'id'=>$b->id,'reference'=>$b->booking_reference,'customer'=>$b->user?->name,
            'route'=>$b->trip?->route?->origin?->name.' → '.$b->trip?->route?->destination?->name,
            'status'=>$b->status,'amount'=>$b->total_amount,'created_at'=>$b->created_at?->toIso8601String(),
        ]);
        $upcoming = Trip::query()->with(['route.origin','route.destination','bus.operator'])->where('departure_at','>=',$now)->where('trip_status','scheduled')->orderBy('departure_at')->limit(8)->get()->map(fn($t)=>[
            'id'=>$t->id,'public_id'=>$t->public_id,'route'=>$t->route->origin->name.' → '.$t->route->destination->name,
            'operator'=>$t->bus->operator->name,'departure_at'=>$t->departure_at->toIso8601String(),'booking_status'=>$t->booking_status,
        ]);

        return response()->json(['data'=>[
            'metrics'=>[
                'gross_booking_value'=>(string)(clone $confirmed)->sum('total_amount'),
                'confirmed_bookings'=>(clone $confirmed)->count(),
                'bookings_today'=>Booking::query()->where('created_at','>=',$today)->count(),
                'upcoming_trips'=>Trip::query()->whereBetween('departure_at',[$now,$week])->where('trip_status','scheduled')->count(),
                'occupancy_percent'=>$capacity?round(($sold/$capacity)*100,1):0,
                'successful_payments'=>Payment::query()->where('status','succeeded')->count(),
            ],
            'recent_bookings'=>$recent,
            'upcoming_trips'=>$upcoming,
            'revenue_trend'=>$this->revenueTrend(),
            'top_routes'=>$this->topRoutes(),
            'operator_performance'=>$this->operatorPerformance(),
            'booking_statuses'=>$this->bookingStatuses(),
            'system_health'=>$this->systemHealth(),
        ]]);
    }

    public function trips(): JsonResponse {
        return response()->json(Trip::query()->with(['route.origin','route.destination','bus.operator'])->latest('departure_at')->paginate(20));
    }
    public function bookings(): JsonResponse {
        return response()->json(Booking::query()->with(['trip.route.origin','trip.route.destination','user:id,name,email'])->latest()->paginate(20));
    }
    public function payments(): JsonResponse {
        return response()->json(Payment::query()->with('booking:id,booking_reference,user_id')->latest()->paginate(20));
    }
    public function auditLogs(): JsonResponse {
        return response()->json(DB::table('audit_logs')->leftJoin('users','users.id','=','audit_logs.actor_id')->select(['audit_logs.*','users.name as actor_name'])->latest('audit_logs.id')->paginate(30));
    }

    private function revenueTrend(): array {
        $start = now('Asia/Dhaka')->subDays(6)->startOfDay();
        $totals = DB::table('payments')
            ->where('status','succeeded')->where('paid_at','>=',$start)
            ->selectRaw("DATE(paid_at AT TIME ZONE 'Asia/Dhaka') as day, SUM(amount) as total")
            ->groupBy('day')->pluck('total','day');
        return collect(range(0,6))->map(function (int $offset) use ($start,$totals): array {
            $day = $start->copy()->addDays($offset)->format('Y-m-d');
            return ['date'=>$day,'total'=>(float)($totals[$day]??0)];
        })->all();
    }

    private function topRoutes(): array {
        return DB::table('bookings')
            ->join('trips','trips.id','=','bookings.trip_id')
            ->join('routes','routes.id','=','trips.route_id')
            ->join('locations as origin','origin.id','=','routes.origin_location_id')
            ->join('locations as destination','destination.id','=','routes.destination_location_id')
            ->whereIn('bookings.status',['confirmed','completed'])
            ->groupBy('routes.id','origin.name','destination.name')
            ->selectRaw('routes.id, origin.name as origin, destination.name as destination, COUNT(bookings.id) as bookings, SUM(bookings.total_amount) as revenue')
            ->orderByDesc('bookings')->limit(6)->get()->map(fn($row)=>[
                'id'=>$row->id,'route'=>$row->origin.' → '.$row->destination,'bookings'=>(int)$row->bookings,'revenue'=>(float)$row->revenue,
            ])->all();
    }

    private function operatorPerformance(): array {
        return DB::table('operators')
            ->join('buses','buses.operator_id','=','operators.id')
            ->leftJoin('trips','trips.bus_id','=','buses.id')
            ->leftJoin('bookings','bookings.trip_id','=','trips.id')
            ->groupBy('operators.id','operators.name','operators.rating','operators.punctuality_percent','operators.brand_color')
            ->selectRaw("operators.id, operators.name, operators.rating, operators.punctuality_percent, operators.brand_color, COUNT(DISTINCT trips.id) as trips, COUNT(DISTINCT bookings.id) FILTER (WHERE bookings.status IN ('confirmed','completed')) as bookings")
            ->orderByDesc('bookings')->limit(6)->get()->map(fn($row)=>[
                'id'=>$row->id,'name'=>$row->name,'rating'=>(float)$row->rating,'punctuality_percent'=>(float)$row->punctuality_percent,
                'brand_color'=>$row->brand_color,'trips'=>(int)$row->trips,'bookings'=>(int)$row->bookings,
            ])->all();
    }

    private function bookingStatuses(): array {
        return DB::table('bookings')->selectRaw('status, COUNT(*) as total')->groupBy('status')->orderByDesc('total')->get()->map(fn($row)=>['status'=>$row->status,'total'=>(int)$row->total])->all();
    }

    private function systemHealth(): array {
        $redis = 'operational';
        try { Redis::connection()->ping(); } catch (\Throwable) { $redis = 'degraded'; }
        return [
            'api'=>'operational','database'=>'operational','redis'=>$redis,
            'queue_backlog'=>DB::table('jobs')->count(),'failed_jobs'=>DB::table('failed_jobs')->count(),
        ];
    }
}
