<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\SearchTripsRequest;
use App\Http\Resources\{TripResource,TripSeatResource};
use App\Models\{Trip,TripSeat};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

final class TripController extends Controller {
    public function search(SearchTripsRequest $request): AnonymousResourceCollection {
        $data = $request->validated();
        $day = Carbon::createFromFormat('Y-m-d', $data['date'], 'Asia/Dhaka');
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();
        $passengers = (int) ($data['passengers'] ?? 1);

        $trips = Trip::query()
            ->with(['route.origin','route.destination','bus.operator'])
            ->withCount(['seats as available_seats_count' => fn($q) => $q->where('status','available')])
            ->whereHas('route', fn($q) => $q->where('origin_location_id',$data['from'])->where('destination_location_id',$data['to']))
            ->whereHas('seats', fn($q) => $q->where('status','available'), '>=', $passengers)
            ->whereBetween('departure_at', [$start,$end])
            ->where('booking_status','open')
            ->where('trip_status','scheduled')
            ->orderBy('departure_at')
            ->get();

        return TripResource::collection($trips);
    }

    public function fareCalendar(Request $request): JsonResponse {
        $data = $request->validate([
            'from' => ['required','ulid','exists:locations,id'],
            'to' => ['required','ulid','different:from','exists:locations,id'],
            'start_date' => ['required','date_format:Y-m-d','after_or_equal:today'],
            'days' => ['sometimes','integer','min:3','max:14'],
            'passengers' => ['sometimes','integer','min:1','max:6'],
        ]);
        $days = (int) ($data['days'] ?? 7);
        $passengers = (int) ($data['passengers'] ?? 1);
        $start = Carbon::createFromFormat('Y-m-d', $data['start_date'], 'Asia/Dhaka')->startOfDay();
        $end = $start->copy()->addDays($days - 1)->endOfDay();

        $trips = Trip::query()
            ->withCount(['seats as available_seats_count' => fn($q) => $q->where('status','available')])
            ->whereHas('route', fn($q) => $q->where('origin_location_id',$data['from'])->where('destination_location_id',$data['to']))
            ->whereHas('seats', fn($q) => $q->where('status','available'), '>=', $passengers)
            ->whereBetween('departure_at', [$start,$end])
            ->where('booking_status','open')
            ->where('trip_status','scheduled')
            ->get(['id','departure_at','base_fare']);

        $byDate = $trips->groupBy(fn(Trip $trip) => $trip->departure_at->timezone('Asia/Dhaka')->format('Y-m-d'));
        $calendar = collect(range(0, $days - 1))->map(function (int $offset) use ($start, $byDate): array {
            $date = $start->copy()->addDays($offset)->format('Y-m-d');
            $dayTrips = $byDate->get($date, collect());
            return [
                'date' => $date,
                'min_fare' => $dayTrips->isEmpty() ? null : number_format((float) $dayTrips->min('base_fare'), 2, '.', ''),
                'trip_count' => $dayTrips->count(),
                'available_seats' => (int) $dayTrips->sum('available_seats_count'),
            ];
        });

        return response()->json(['data' => $calendar]);
    }

    public function seats(Trip $trip): AnonymousResourceCollection {
        $seats = TripSeat::query()->with('seatLayoutSeat')->where('trip_id',$trip->id)->orderBy('seat_number')->get();
        return TripSeatResource::collection($seats);
    }
}
