# TripSync V1 API

Browser calls are same-origin under `/api/v1`. State-changing requests require an authenticated session where indicated and a CSRF token obtained from `GET /api/v1/auth/csrf`.

## Public/session bootstrap

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/v1/auth/csrf` | Establish session + obtain CSRF token |
| POST | `/api/v1/auth/register` | Create customer account |
| POST | `/api/v1/auth/login` | Sign in |
| GET | `/api/v1/locations` | Active search locations |
| GET | `/api/v1/trips/search?from=&to=&date=` | Search dated trips |
| GET | `/api/v1/trips/{trip}/seats` | Live seat map |

## Authenticated customer

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/v1/auth/me` | Current user/roles |
| POST | `/api/v1/auth/logout` | Sign out and invalidate session |
| POST | `/api/v1/seat-holds` | Atomically hold 1–6 seats |
| GET | `/api/v1/seat-holds/{hold}` | Reload current hold |
| DELETE | `/api/v1/seat-holds/{hold}` | Release current hold |
| GET | `/api/v1/bookings` | Customer booking history |
| POST | `/api/v1/bookings` | Create pending booking from a hold |
| GET | `/api/v1/bookings/{booking}` | Booking/ticket/payment detail |
| POST | `/api/v1/bookings/{booking}/payments` | Initiate provider payment |
| POST | `/api/v1/payments/{payment}/simulate` | DemoPay success/failure/cancel |
| POST | `/api/v1/bookings/{booking}/cancel` | Cancel eligible confirmed booking |

## Admin

Under `/api/v1/admin`, all routes require an admin role. Overview/bookings/audit are available to operations, finance and super-admin roles; trip feed requires operations/super-admin; payment feed requires finance/super-admin.

## Error shape

```json
{
  "message": "Seat 4A is no longer available.",
  "code": "SEAT_UNAVAILABLE",
  "context": { "seat_number": "4A" },
  "request_id": "REQ-..."
}
```

Important codes include `VALIDATION_ERROR`, `INVALID_CREDENTIALS`, `FORBIDDEN`, `TRIP_CLOSED`, `SEAT_UNAVAILABLE`, `HOLD_EXPIRED`, `PASSENGER_SEAT_MISMATCH`, `BOOKING_NOT_PAYABLE`, `PAYMENT_ALREADY_PROCESSED`, and `BOOKING_NOT_CANCELLABLE`.

## V1.1 discovery additions

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/v1/discovery/overview` | Public live stats, popular routes and top operators for discovery surfaces |
| GET | `/api/v1/trips/fare-calendar?from=&to=&date=&passengers=` | Seven-day lowest fare and available-capacity calendar |
| GET | `/api/v1/trips/search?from=&to=&date=&passengers=` | Passenger-aware dated trip search; `passengers` defaults to 1 and is limited to 1–6 |

Trip search keeps all V1.0.6 fields and additionally exposes operator rating/punctuality/brand metadata plus bus comfort class and amenities when present.

## V1.1 admin overview fields

`GET /api/v1/admin/overview` keeps the original `metrics`, `recent_bookings` and `upcoming_trips` keys and adds:

- `revenue_trend` — bounded seven-day successful-payment totals;
- `top_routes` — booking/revenue leaders;
- `operator_performance` — operator booking/revenue/punctuality context;
- `booking_statuses` — booking-state distribution;
- `system_health` — API/database/Redis/queue operational signals.

These analytics are informational. They do not participate in payment or seat-inventory decisions.
