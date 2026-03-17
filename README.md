# PsiAgenda

A SaaS platform for Brazilian psychologists to manage their practice. Features include appointment scheduling, WhatsApp reminders, patient management, and AI-powered pre-consultation screening.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4
- **Database:** SQLite (development), MySQL (production)
- **Frontend:** React + Vite
- **Testing:** Pest (PHPUnit 11)
- **Linting:** Laravel Pint
- **Auth:** Laravel Sanctum (token-based API)

## Getting Started

### Requirements

- PHP 8.4+
- Composer
- Node.js & npm

### Installation

```bash
git clone https://github.com/iagcs/acolhe.git
cd acolhe
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Development

```bash
# Start server, queue, logs, and Vite concurrently
composer dev
```

### Testing

```bash
# Run all tests
composer test

# Run a specific test file
php artisan test --filter=RegisterTest

# Run a specific test method
php artisan test --filter="it registers successfully"
```

### Linting

```bash
./vendor/bin/pint
```

## Architecture

The project uses a modular architecture powered by [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules). Each domain is a self-contained module under `Modules/`:

| Module | Description |
|--------|-------------|
| **Auth** | Registration, login, Sanctum tokens |
| **Agenda** | Availability and scheduling |
| **Patient** | Patient records and consent |
| **Session** | Therapy sessions |
| **Booking** | Appointment booking |
| **Confirmation** | Session confirmations |
| **Reminder** | Automated reminders |
| **Notification** | Notification delivery |
| **WhatsApp** | WhatsApp integration |
| **AIScreening** | AI-powered pre-consultation screening |
| **Document** | Document management |
| **Billing** | Payments and billing settings |
| **Receipt** | Receipt generation |
| **Report** | Reporting and analytics |
| **RiskScore** | Patient risk scoring |
| **WaitingList** | Waiting list management |
| **Core** | Shared infrastructure |

### Module Structure

Each module follows the pattern:

```
Modules/{Name}/
  app/
    Actions/         # Business logic (single-responsibility classes)
    DTOs/            # Data Transfer Objects (spatie/laravel-data)
    Enums/           # PHP backed enums
    Http/
      Controllers/
      Requests/      # FormRequests (validation + WithData trait)
    Models/
    Providers/
  config/
  database/
    migrations/
    seeders/
  routes/
    api.php
  tests/
    Feature/
```

### Request Flow

```
HTTP Request
  -> FormRequest (validation + WithData trait)
    -> Controller
      -> $request->getData() returns DTO
        -> Action::execute(DTO)
          -> Business logic + DB operations
        <- Response
```

## API

API documentation is available per module:

- [Auth API](Modules/Auth/api-docs.md) — Login & Registration

## Key Conventions

- All user-facing strings in Portuguese (pt-BR)
- Healthcare compliance: LGPD, AES-256 encryption for private notes
- CRP credential system (Brazilian psychology board)
- WhatsApp as primary patient communication channel
