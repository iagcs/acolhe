# Session API Documentation

## POST /api/v1/sessions

Creates a new session for the authenticated psychologist.

**Auth:** Bearer token (Sanctum)

### Request

```json
{
  "patient_id": "uuid",
  "scheduled_at": "2026-03-20T14:00:00-03:00",
  "duration_minutes": 50,
  "type": "online",
  "notes": "Primeira sessão."
}
```

### Validation Rules

| Field              | Rules                                    |
|--------------------|------------------------------------------|
| `patient_id`       | required, uuid, exists:patients,id       |
| `scheduled_at`     | required, date, after:now                |
| `duration_minutes` | sometimes, integer, min:15, max:180      |
| `type`             | required, in: online, in_person          |
| `notes`            | nullable, string                         |

### Defaults (set server-side)

- `status`: `scheduled`
- `price`: from psychologist's `session_price`
- `starts_at`: from `scheduled_at`
- `ends_at`: `starts_at` + `duration_minutes`
- `duration_minutes` defaults to `50` if not provided

### Success Response (201)

```json
{
  "session": {
    "id": "uuid",
    "psychologist_id": "uuid",
    "patient_id": "uuid",
    "starts_at": "2026-03-20T17:00:00.000000Z",
    "ends_at": "2026-03-20T17:50:00.000000Z",
    "status": "scheduled",
    "type": "online",
    "private_notes": "Primeira sessão.",
    "price": "200.00"
  }
}
```

### Error Response (422)

```json
{
  "message": "The patient id field is required.",
  "errors": {
    "patient_id": ["The patient id field is required."]
  }
}
```
