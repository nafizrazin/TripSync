export type Location = { id: string; name: string; slug: string };
export type Trip = {
  id: string; public_id: string; departure_at: string; arrival_at: string; base_fare: string;
  trip_status: string; booking_status: string; available_seats: number;
  route: { code: string; origin: Location; destination: Location; duration_minutes: number };
  bus: {
    id: string; label: string; type: string; comfort_class: string; amenities: string[]; registration_number: string;
    operator: { id: string; name: string; short_code: string|null; rating: string; review_count: number; punctuality_percent: string; brand_color: string|null };
  };
};
export type FareCalendarDay = { date:string; min_fare:string|null; trip_count:number; available_seats:number };
export type DiscoveryOverview = {
  stats:{active_operators:number;active_routes:number;journeys_today:number;available_seats:number};
  popular_routes:Array<{id:string;code:string;origin_id:string;origin_name:string;destination_id:string;destination_name:string;min_fare:string;trip_count:number;available_seats:number}>;
  top_operators:Array<{id:string;name:string;slug:string;short_code:string|null;tagline:string|null;brand_color:string|null;rating:string;review_count:number;punctuality_percent:string}>;
};
export type TripSeat = {
  id: string; seat_number: string; fare: string; status: 'available'|'held'|'sold'|'blocked'; hold_expires_at: string|null;
  layout: { row: number; column: number; deck: number; type: string; is_window: boolean; is_aisle: boolean };
};
export type SeatHold = { id: string; token: string; trip_id: string; expires_at: string; seats: Array<{ id: string; seat_number: string; fare: string }> };
export type Booking = { id: string; public_id: string; booking_reference: string; status: string; subtotal: string; service_fee: string; discount_amount: string; total_amount: string; currency: string; expires_at: string|null; confirmed_at: string|null; cancelled_at: string|null; trip?: Trip; passengers?: Array<{id:string;full_name:string;phone:string;email:string|null;seat?:string}>; payments?: Payment[]; tickets?: Ticket[] };
export type Payment = { id: string; public_id: string; booking_id: string; provider: string; provider_transaction_id: string|null; amount: string; currency: string; status: string; paid_at: string|null };
export type Ticket = { id:string; ticket_number:string; status:string; qr_payload:string; seat_number?:string; passenger_name?:string };
export type User = { id:string; name:string; email:string; phone:string|null; roles:string[] };
