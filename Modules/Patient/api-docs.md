# Patient API Documentation

## POST /api/v1/patients

Creates a new patient for the authenticated psychologist.

**Auth:** Bearer token (Sanctum)

### Request

```json
{
  "name": "Maria Silva",
  "email": "maria@example.com",
  "phone": "11988887777",
  "birth_date": "1990-05-15",
  "notes": "Paciente com ansiedade."
}
```

### Validation Rules

| Field        | Rules                  |
|--------------|------------------------|
| `name`       | required, string, max:255 |
| `email`      | nullable, email        |
| `phone`      | nullable, string, max:20 |
| `birth_date` | nullable, date         |
| `notes`      | nullable, string       |

### Defaults (set server-side)

- `is_active`: `true`
- `psychologist_id`: from authenticated user

### Success Response (201)

```json
{
  "patient": {
    "id": "uuid",
    "psychologist_id": "uuid",
    "name": "Maria Silva",
    "email": "maria@example.com",
    "phone": "11988887777",
    "birth_date": "1990-05-15",
    "notes": "Paciente com ansiedade.",
    "is_active": true
  }
}
```

### Error Response (422)

```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```
