# TripSync Feature Walkthrough

This guide covers the main flows worth demonstrating when reviewing the project locally.

## 1. Trip discovery

Open the home page and search for a route using an origin, destination, travel date, and passenger count.

The results page supports:

- a seven-day fare calendar;
- operator, bus type, time, amenity, fare, and capacity filters;
- multiple sorting modes;
- TripScore and comparison labels such as Cheapest, Fastest, and Best Value.

The search API only advertises trips whose current authoritative seat inventory can fit the requested passenger count.

## 2. Seat selection

Open a trip and inspect the seat map.

Seats are backed by trip-specific inventory rather than a single remaining-seat counter. The UI shows seat position information and can suggest adjacent seats for a group. Selecting seats and continuing creates a temporary server-side hold.

### Technical flow

```text
Trip search
    |
    v
Load trip-seat inventory
    |
    v
Select seats
    |
    v
Lock selected rows in PostgreSQL
    |
    v
Create temporary seat hold
    |
    v
Checkout
```

If another customer obtains a seat first, the backend rejects the conflicting hold even if the browser still showed the seat as available.

## 3. Checkout and DemoPay

After a seat hold is created:

1. Enter passenger details.
2. Review the booking amount calculated by the server.
3. Start a DemoPay transaction.
4. Choose success, failure, or cancellation for testing.
5. On success, view the confirmed booking and ticket.

Payment callbacks are handled idempotently so retrying the same successful payment cannot issue duplicate tickets.

## 4. Travel wallet

The customer booking area separates upcoming and past journeys and provides booking, ticket, passenger, payment, and cancellation information.

Eligible confirmed bookings can be cancelled. TripSync releases the associated seats transactionally and records the simulated refund state.

## 5. Admin control center

Sign in with one of the seeded admin roles and open `/admin`.

The dashboard includes:

- gross booking value and booking metrics;
- occupancy and payment indicators;
- seven-day revenue trend;
- top route performance;
- operator performance;
- booking-status distribution;
- upcoming trips and recent bookings;
- API, database, Redis, and queue health information.

Permissions are enforced by the Laravel API, not only by hiding interface controls.

## 6. Architecture points to demonstrate

For a technical review, the most important implementation choices are:

- normalized trip/seat inventory instead of a mutable vacant-seat counter;
- PostgreSQL row locking for concurrent seat holds;
- transaction boundaries around booking confirmation and cancellation;
- session/CSRF authentication for the browser application;
- provider-independent payment logic with a simulation provider for local development;
- Redis-backed queues and Laravel scheduler jobs;
- Docker health checks and same-origin Nginx routing;
- migration-driven schema changes and repeatable demo seeders.
