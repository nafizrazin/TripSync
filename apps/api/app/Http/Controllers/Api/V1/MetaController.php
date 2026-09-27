<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Location,Operator};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Cache,DB};

final class MetaController extends Controller {
    public function locations(): JsonResponse {
        return response()->json(['data' => Location::query()->where('is_active',true)->orderBy('name')->get(['id','name','slug'])]);
    }

    public function discoveryOverview(): JsonResponse {
        $data = Cache::remember('discovery.overview.v1', 60, function (): array {
            $todayStart = now('Asia/Dhaka')->startOfDay();
            $todayEnd = now('Asia/Dhaka')->endOfDay();
            $futureEnd = now('Asia/Dhaka')->addDays(14)->endOfDay();

            $popularRoutes = DB::table('routes')
                ->join('locations as origin','origin.id','=','routes.origin_location_id')
                ->join('locations as destination','destination.id','=','routes.destination_location_id')
                ->join('trips','trips.route_id','=','routes.id')
                ->leftJoin('trip_seats','trip_seats.trip_id','=','trips.id')
                ->whereBetween('trips.departure_at',[now(),$futureEnd])
                ->where('trips.trip_status','scheduled')
                ->where('trips.booking_status','open')
                ->groupBy('routes.id','routes.code','origin.id','origin.name','destination.id','destination.name')
                ->selectRaw('routes.id, routes.code, origin.id as origin_id, origin.name as origin_name, destination.id as destination_id, destination.name as destination_name, MIN(trips.base_fare) as min_fare, COUNT(DISTINCT trips.id) as trip_count, COUNT(trip_seats.id) FILTER (WHERE trip_seats.status = ?) as available_seats', ['available'])
                ->orderByDesc('trip_count')
                ->limit(6)
                ->get();

            $operators = Operator::query()->where('status','active')->orderByDesc('rating')->orderByDesc('review_count')->limit(6)->get([
                'id','name','slug','short_code','tagline','brand_color','rating','review_count','punctuality_percent',
            ]);

            return [
                'stats' => [
                    'active_operators' => Operator::query()->where('status','active')->count(),
                    'active_routes' => DB::table('routes')->where('status','active')->count(),
                    'journeys_today' => DB::table('trips')->whereBetween('departure_at',[$todayStart,$todayEnd])->where('trip_status','scheduled')->count(),
                    'available_seats' => DB::table('trip_seats')->join('trips','trips.id','=','trip_seats.trip_id')->whereBetween('trips.departure_at',[now(),$futureEnd])->where('trip_seats.status','available')->count(),
                ],
                'popular_routes' => $popularRoutes,
                'top_operators' => $operators,
            ];
        });
        return response()->json(['data' => $data]);
    }
}
