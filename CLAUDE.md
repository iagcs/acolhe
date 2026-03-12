# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**PsiAgenda** — a SaaS for Brazilian psychologists to manage their practice. Features include appointment scheduling, WhatsApp reminders, patient management, and AI-powered pre-consultation screening. All UI text is in Portuguese (pt-BR).

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Database**: SQLite (default, at `database/database.sqlite`)
- **Frontend**: React JSX prototypes (pre-build-system), Vite
- **Linting**: Laravel Pint (PHP-CS-Fixer wrapper)
- **Testing**: PHPUnit 11
- **Local dev**: Laravel Herd

## Commands

```bash
# Development (starts server, queue, logs, and vite concurrently)
composer dev

# Run all tests
composer test

# Run a single test file
php artisan test --filter=ExampleTest

# Run a single test method
php artisan test --filter=test_the_application_returns_a_successful_response

# Lint/format PHP code
./vendor/bin/pint

# Run migrations
php artisan migrate

# Fresh migrate (drops all tables)
php artisan migrate:fresh

# Create a model with migration, factory, and seeder
php artisan make:model Patient -mfs
```

## Architecture

### Laravel (Backend)

Standard Laravel 12 structure:
- `app/Models/` — Eloquent models
- `app/Http/Controllers/` — Controllers
- `app/Providers/` — Service providers
- `routes/web.php` — Web routes
- `routes/console.php` — Artisan commands
- `database/migrations/` — Database migrations
- `database/factories/` — Model factories for testing
- `database/seeders/` — Database seeders
- `tests/Feature/` — Feature tests (HTTP, integration)
- `tests/Unit/` — Unit tests
- `config/` — Configuration files (app, auth, database, etc.)

### Frontend Prototypes

Standalone React component files from the initial MVP phase — not yet integrated into the Laravel/Vite build:
- `psiagenda-v3.jsx` — Main app UI (dashboard, patients, messages, AI assistant)
- `psiagenda-landing.jsx` — Marketing landing page
- `psiagenda-mvp-v3.docx` — Product specification document

### Design System (from prototypes)

- **Colors**: primary (#3571C5), accent (#1DAA7B), warning (#E8A817), danger (#D94848), AI (#7C3AED)
- **Session statuses**: scheduled, confirmed, cancelled, completed, no_show
- **Message statuses**: read, delivered, sent, failed, pending
- **Components use short names**: `I` (icons), `Btn` (button), `WA` (WhatsApp icon), `Badge`, `Toggle`, `Card`

### AI Assistant System

Configurable across 5 therapeutic approaches, each with its own vocabulary, sample questions, tone, and color:
TCC (CBT), Psicanálise, Abordagem Humanista, Terapia Sistêmica, Gestalt-terapia

## Key Conventions

- All user-facing strings in Portuguese (pt-BR)
- Healthcare compliance: LGPD, AES-256 encryption for private notes
- WhatsApp is the primary patient communication channel
- Pricing in BRL (R$69/99/179 per month across 3 tiers)
- CRP credential system (Brazilian psychology board)
