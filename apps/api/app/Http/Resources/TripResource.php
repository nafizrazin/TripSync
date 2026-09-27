<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TripResource extends JsonResource {
    public function toArray(Request $request): array {
        $operator = $this->bus->operator;
        return [
            'id' => $this->id,
            'public_id' => $this->public_id,
            'departure_at' => $this->departure_at?->toIso8601String(),
            'arrival_at' => $this->arrival_at?->toIso8601String(),
            'base_fare' => $this->base_fare,
            'trip_status' => $this->trip_status,
            'booking_status' => $this->booking_status,
            'route' => [
                'code' => $this->route->code,
                'origin' => ['id' => $this->route->origin->id, 'name' => $this->route->origin->name],
                'destination' => ['id' => $this->route->destination->id, 'name' => $this->route->destination->name],
                'duration_minutes' => $this->route->estimated_duration_minutes,
            ],
            'bus' => [
                'id' => $this->bus->id,
                'label' => $this->bus->label,
                'type' => $this->bus->bus_type,
                'comfort_class' => $this->bus->comfort_class ?? 'standard',
                'amenities' => $this->bus->amenities ?? [],
                'registration_number' => $this->bus->registration_number,
                'operator' => [
                    'id' => $operator->id,
                    'name' => $operator->name,
                    'short_code' => $operator->short_code,
                    'rating' => $operator->rating,
                    'review_count' => (int) $operator->review_count,
                    'punctuality_percent' => $operator->punctuality_percent,
                    'brand_color' => $operator->brand_color,
                ],
            ],
            'available_seats' => (int) ($this->available_seats_count ?? 0),
        ];
    }
}
