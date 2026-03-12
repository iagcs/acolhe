# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**PsiAgenda** — a SaaS for Brazilian psychologists to manage their practice. Features include appointment scheduling, WhatsApp reminders, patient management, and AI-powered pre-consultation screening. All UI text is in Portuguese (pt-BR).

## Current State

This is an early-stage MVP with two standalone React component files and a design spec document. There is no build system, package.json, backend, database, or test infrastructure yet.

## Files

- `psiagenda-v3.jsx` — Main application UI (dashboard, patients, messages, AI assistant configuration)
- `psiagenda-landing.jsx` — Marketing landing page with pricing, FAQ, testimonials
- `psiagenda-mvp-v3.docx` — Product specification document

## Architecture

### Design System

Colors, statuses, and typography are defined as constants at the top of each file rather than in shared modules:

- **`C` object** (`psiagenda-v3.jsx`): Design tokens — primary (#3571C5), accent (#1DAA7B), warning (#E8A817), danger (#D94848), AI (#7C3AED)
- **`STATUS` object**: Session states — scheduled, confirmed, cancelled, completed, no_show (each with bg, text, dot, label)
- **`MSG_STATUS` object**: WhatsApp message delivery states — read, delivered, sent, failed, pending

All styling is **inline CSS** — no CSS files, no Tailwind, no styled-components.

### Component Patterns

Components use short single-letter or abbreviated names:
- `I` — SVG icon system (24+ icons via `name` prop)
- `WA` — WhatsApp icon
- `Btn` — Button (variants: primary, outline, danger, ai)
- `Badge`, `Toggle`, `Card`, `CardHeader`, `Sidebar` — UI primitives

### AI Assistant System

The AI assistant is configurable across 5 therapeutic approaches, each with its own color, vocabulary (10-12 terms), sample questions, tone, and preview conversation:
- TCC (Terapia Cognitivo-Comportamental)
- Psicanálise
- Abordagem Humanista
- Terapia Sistêmica
- Gestalt-terapia

### Landing Page

`psiagenda-landing.jsx` exports a single `LP` component. Uses Source Serif 4 + Outfit fonts. Three pricing tiers: Solo (R$69), Profissional (R$99), Consultório (R$179). Scroll-based animations via IntersectionObserver.

## Key Conventions

- Language: all user-facing strings are in Portuguese (pt-BR)
- Healthcare compliance: LGPD references, AES-256 encryption mentions, CRP credential system
- WhatsApp is the primary patient communication channel
- Pricing in BRL (Brazilian Real)
