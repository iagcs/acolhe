# PsiAgenda

A SaaS platform for Brazilian psychologists to manage their practice — from scheduling to patient care.

## Features

- **Appointment Scheduling** — Manage your agenda with configurable availability, session duration, and intervals
- **Patient Management** — Keep patient records organized with consent tracking and waiting lists
- **WhatsApp Reminders** — Automated reminders and confirmations via WhatsApp
- **AI Screening** — AI-powered pre-consultation screening tailored to your therapeutic approach (TCC, Psicanálise, Humanista, Sistêmica, Gestalt)
- **Billing & Receipts** — Track payments and generate receipts
- **Reports & Risk Scoring** — Analytics and patient risk assessment tools
- **Document Management** — Store and organize clinical documents securely

## Plans

| | Free | Solo | Clinic |
|---|---|---|---|
| Trial | 14 days | - | - |
| Patients | Limited | Unlimited | Unlimited |
| Multi-professional | - | - | Yes |

## Compliance

- **LGPD** — Full compliance with Brazilian data protection law
- **CRP** — Integrated with Brazilian psychology board credential system
- **Encryption** — AES-256 encryption for private clinical notes

## Getting Started

```bash
git clone https://github.com/iagcs/acolhe.git
cd acolhe
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer dev
```

## API Documentation

- [Auth API](Modules/Auth/api-docs.md) — Login & Registration
