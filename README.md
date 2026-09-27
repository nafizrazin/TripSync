# TripSync

TripSync is a modern intercity bus booking platform built from an earlier PHP/MySQL university DBMS project. The current application uses a Next.js frontend, a Laravel API, PostgreSQL for transactional data, Redis for queues/cache support, and Docker Compose for local development.

The project focuses on the parts that make transport booking difficult to implement correctly: dated trip inventory, exact seat availability, temporary seat holds, concurrent booking protection, idempotent payments, ticketing, cancellations, refunds, role-based administration, and useful search/comparison tools.

## Main features

### Passenger experience

- Search trips by origin, destination, date, and passenger count.
- Compare trips with a seven-day fare calendar, filters, sorting, operator information, amenities, and TripScore.
- View Cheapest, Fastest, Best Value, and Most Seats labels.
- Select exact seats from a live seat map.
- Get adjacent-seat suggestions for groups.
- Hold seats for ten minutes while completing checkout.
- Add passenger details and complete simulated DemoPay transactions.
- View tickets, booking history, payment information, cancellations, and refunds.
- Use a travel wallet for upcoming and past journeys.

### Operations and administration

- Role-based admin access for operations, finance, and super-admin users.
- Revenue, booking, occupancy, route, operator, and booking-status analytics.
- Upcoming-trip and recent-booking views.
- Payment and refund visibility.
- Audit logging for important business actions.
- System-health indicators for the API, database, Redis, and queue services.

### Booking integrity

- PostgreSQL is authoritative for seat inventory.
- Seat holds use database row locking to prevent double booking.
- Client-supplied prices and availability are never trusted.
- Payment completion is idempotent.
- Booking confirmation, ticket issuance, cancellation, and seat release use database transactions.

## Technology stack

| Layer | Technology |
|---|---|
| Web | Next.js 16, React 19, TypeScript |
| API | Laravel 13, PHP 8.5 |
| Database | PostgreSQL 18 |
| Cache / queue | Redis 8 |
| Reverse proxy | Nginx |
| Local mail | Mailpit |
| Environment | Docker Compose, PowerShell |
| CI | GitHub Actions |

## Quick start

The easiest local setup is on Windows with WSL 2 and Docker Desktop. The setup script can install or verify the required Windows tools, create local environment values, build the containers, migrate and seed the database, and run health checks.

```powershell
Set-ExecutionPolicy -Scope Process Bypass
.\setup.ps1
```

After the first setup:

```powershell
.\start.ps1
.\status.ps1
.\stop.ps1
```

Open:

- Application: `http://localhost`
- Mailpit: `http://localhost:8025`

The setup output prints the local demo-account credentials used for testing.

## Demo data

A fresh development database is populated with realistic sample data so the application is useful immediately. The seeders create locations, operators, buses, routes, seat layouts, dated trips, customers, bookings, payments, tickets, cancellations, refunds, and audit activity.

The demo dataset is generated only for local development/testing. It is not intended as production data.

## Testing

Run the project verification suite with:

```powershell
.\test.ps1
```

The repository also contains focused contract tests for the database model, booking rules, runtime configuration, search/discovery features, admin analytics, and frontend domain logic.

## Project structure

```text
TripSync/
├── apps/
│   ├── api/                 # Laravel API and database migrations/seeders
│   └── web/                 # Next.js customer and admin interface
├── docs/                    # Architecture, API, demo and legacy notes
├── infrastructure/nginx/    # Reverse-proxy configuration
├── legacy/                  # Source-only snapshot of the original project
├── scripts/                 # Shared PowerShell helpers and smoke checks
├── tests/contracts/         # Repository/runtime contract tests
├── compose.yaml
├── setup.ps1
├── start.ps1
├── status.ps1
└── test.ps1
```

## Documentation

- [Feature walkthrough](docs/DEMO_GUIDE.md)
- [Architecture](docs/architecture.md)
- [API reference](docs/api.md)
- [Legacy project audit](docs/legacy-audit.md)

## Legacy project

The original PHP project is kept as a small source-only reference under `legacy/` so the modernization can be compared with the starting point. Third-party template assets and the old sample database rows are intentionally excluded from the public repository.
