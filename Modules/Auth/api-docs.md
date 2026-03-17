# Auth API Documentation

## POST /api/v1/login

Authenticates a psychologist and returns a Sanctum API token.

**Auth:** None

### Request

```json
{
  "email": "psi@example.com",
  "password": "secret123"
}
```

### Validation Rules

| Field      | Rules            |
|------------|------------------|
| `email`    | required, email  |
| `password` | required         |

### Success Response (200)

```json
{
  "token": "1|abc123...",
  "user": {
    "id": "uuid",
    "name": "Dr. João Silva",
    "email": "psi@example.com",
    "crp": "06/12345",
    "phone": "11999999999",
    "therapeutic_approach": "tcc",
    "session_duration": 50,
    "session_interval": 10,
    "session_price": "200.00",
    "plan": "free",
    "slug": "dr-joao-silva",
    "timezone": "America/Sao_Paulo"
  }
}
```

### Error Response (422)

```json
{
  "message": "These credentials do not match our records.",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

---

## POST /api/v1/register

Creates a new psychologist account with availabilities and returns a Sanctum API token.

**Auth:** None

### Request

```json
{
  "name": "Dr. João Silva",
  "email": "psi@example.com",
  "password": "secret1234",
  "password_confirmation": "secret1234",
  "crp": "06/12345",
  "phone": "11999999999",
  "therapeutic_approach": "tcc",
  "session_duration": 50,
  "session_interval": 10,
  "session_price": 200.00,
  "availabilities": [
    { "day_of_week": 1, "start_time": "08:00", "end_time": "12:00" },
    { "day_of_week": 3, "start_time": "14:00", "end_time": "18:00" }
  ]
}
```

### Validation Rules

| Field                           | Rules                                                        |
|---------------------------------|--------------------------------------------------------------|
| `name`                          | required, string, max:255                                    |
| `email`                         | required, email, unique:psychologists                        |
| `password`                      | required, string, min:8, confirmed                           |
| `crp`                           | required, string, max:20                                     |
| `phone`                         | required, string, max:20                                     |
| `therapeutic_approach`          | required, enum: tcc, psychoanalysis, humanistic, systemic, gestalt, other |
| `session_duration`              | required, integer, min:15, max:180                           |
| `session_interval`              | required, integer, min:0, max:60                             |
| `session_price`                 | required, numeric, min:0                                     |
| `availabilities`                | required, array, min:1                                       |
| `availabilities.*.day_of_week`  | required, integer, between:0,6                               |
| `availabilities.*.start_time`   | required, date_format:H:i                                    |
| `availabilities.*.end_time`     | required, date_format:H:i, after:availabilities.*.start_time |

### Defaults (set server-side)

- `plan`: `free`
- `plan_expires_at`: 14 days from registration
- `slug`: generated from `name`
- `timezone`: `America/Sao_Paulo`

### Success Response (201)

```json
{
  "token": "1|abc123...",
  "user": {
    "id": "uuid",
    "name": "Dr. João Silva",
    "email": "psi@example.com",
    "crp": "06/12345",
    "phone": "11999999999",
    "therapeutic_approach": "tcc",
    "session_duration": 50,
    "session_interval": 10,
    "session_price": "200.00",
    "plan": "free",
    "plan_expires_at": "2026-03-31T00:00:00.000000Z",
    "slug": "dr-joao-silva",
    "timezone": "America/Sao_Paulo"
  }
}
```

### Error Response (422)

```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

## PATCH /api/v1/profile

Updates the authenticated psychologist's profile (photo and/or bio).

**Auth:** Bearer token (Sanctum)

### Request

Multipart form data or JSON:

```json
{
  "bio": "Psicóloga especializada em TCC com 10 anos de experiência."
}
```

For photo upload, send as `multipart/form-data` with an `image` file in the `photo` field.

### Validation Rules

| Field   | Rules                        |
|---------|------------------------------|
| `photo` | nullable, image, max:2048 KB |
| `bio`   | nullable, string, max:1000   |

### Success Response (200)

```json
{
  "user": {
    "id": "uuid",
    "name": "Dr. João Silva",
    "photo": "photos/abc123.jpg",
    "bio": "Psicóloga especializada em TCC com 10 anos de experiência.",
    "..."
  }
}
```

### Error Response (422)

```json
{
  "message": "The photo field must be an image.",
  "errors": {
    "photo": ["The photo field must be an image."]
  }
}
```

---

## GET /api/v1/onboarding/status

Returns the onboarding state for the authenticated psychologist.

**Auth:** Bearer token (Sanctum)

### Success Response (200)

```json
{
  "has_photo": false,
  "has_bio": false,
  "patient_count": 0,
  "session_count": 0,
  "dismissed": false
}
```

---

## PATCH /api/v1/onboarding/dismiss

Marks onboarding as dismissed for the authenticated psychologist. Idempotent.

**Auth:** Bearer token (Sanctum)

### Success Response (200)

```json
{
  "dismissed": true
}
```
