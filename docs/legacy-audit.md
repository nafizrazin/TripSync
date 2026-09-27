# Legacy transport project audit

A source-only snapshot of the original project is preserved under `legacy/`. It is useful historical context, not a runtime dependency. The public snapshot omits the old sample data dump and bundled third-party/template assets.

## Architecture limitations

The legacy application is a PHP/MySQL page-oriented monolith with roughly all mutations and multiple query/rendering responsibilities concentrated in `action.php`. Authentication, booking, reporting and persistence logic are tightly coupled to HTML and global session state, which makes isolated testing and safe change difficult.

The frontend mixes generations of Bootstrap/jQuery/template assets and contains substantial duplicated/dead presentation code. There is no migration-driven schema lifecycle, automated test suite, environment abstraction, structured logging, CI, queueing, or clean API boundary.

## Security findings

- User passwords and the seeded admin password are stored in **plaintext**.
- Database credentials are hardcoded for local root access.
- SQL is assembled through string interpolation instead of a consistent parameterized-query layer.
- Multiple database values are echoed into HTML without a systematic escaping boundary, creating XSS exposure.
- State-changing operations have no CSRF protection.
- Session handling lacks modern login regeneration/session-management controls and purpose-specific throttling.
- Authorization is page/flow oriented instead of enforced through reusable backend policies.

## Booking/data integrity findings

The old schema conflates physical buses, recurring route/schedule information and dated journeys. `booking_det` stores `bus_id`, a mutable `vacant` counter, journey date, origin and destination rather than modeling a real trip and seat inventory.

The booking flow trusts the browser-provided available-seat count, subtracts requested passengers from that value, and writes the result. There is no database transaction or row lock protecting concurrent buyers. Two customers can therefore derive availability from stale state and overbook.

The vacancy update can occur before duplicate-ticket validation, so a rejected duplicate can still reduce inventory. If a later ticket insert fails, there is no transactional rollback. Seats are generated arithmetically from remaining counts instead of being first-class seat records.

`booking_det` has no primary key in the supplied schema and core tables lack the foreign keys/uniqueness constraints needed to guarantee user → trip → booking → payment/ticket integrity.

The search flow has a correctness bug: if any `booking_det` rows exist for a route/date, the code searches that table first and can omit other matching buses that have no booking row yet. The rebuilt system queries `trips` directly and derives availability from `trip_seats` for every result.

## Data-quality findings

- Dates and departure times are strings instead of proper temporal types.
- Status/type values vary (`Confirm` vs `Confirmed`, `Non AC` vs variants), making reliable querying harder.
- Passenger/ticket structure does not properly normalize multiple passengers and seat assignments.
- User registration presents fields that are not consistently persisted.
- NID is treated as a normal required profile field even though the core booking product does not need to collect it by default.

## Legacy-to-new mapping

| Legacy | TripSync |
|---|---|
| `user_info` | `users`, `roles`, `role_user` |
| `admin` | normal users with admin roles |
| `bus_details` | `operators`, `buses`, `seat_layouts`, `routes`, `schedules` |
| `booking_det` | `trips`, `trip_seats`, `seat_holds` |
| `ticket` | `bookings`, `booking_passengers`, `booking_seats`, `payments`, `tickets` |

No new code queries the legacy schema. V1 seeders are the canonical demo data; a one-off legacy importer can be added later if preservation of old sample rows becomes important.
