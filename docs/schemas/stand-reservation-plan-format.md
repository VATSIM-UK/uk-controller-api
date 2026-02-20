# Stand Reservation Plan JSON Format (Formal Specification)

This document is the normative specification for the stand-reservation plan JSON format.

## 1. Canonical machine-readable schemas

- **Payload schema**: `docs/schemas/stand-reservation-plan.schema.json`
  - Defines the plan payload object (`reservations`, optional defaults).
- **API request schema**: `docs/schemas/stand-reservation-plan-request.schema.json`
  - Defines the full request body for `POST /stand/reservations/plan`.

If there is any ambiguity in this markdown, the JSON Schema files are authoritative.

## 2. Data model

### 2.1 Payload object

A payload object MUST be a JSON object with these properties:

- `reservations` (**required**): array of one or more reservation objects.
- `start` (optional): default start datetime for reservation rows.
- `end` (optional): default end datetime for reservation rows.
- `active_from` (optional): alias default start datetime.
- `active_to` (optional): alias default end datetime.

Constraints:

- If `end` is present, `start` MUST also be present.
- If `active_to` is present, `active_from` MUST also be present.
- Additional top-level properties are not allowed.

### 2.2 Reservation row object

Each item in `reservations` MUST be a JSON object with:

- `stand` (**required**): stand identifier string.
- Exactly one of:
  - `airfield`: ICAO code, or
  - `airport`: ICAO code alias.

Optional row-level fields:

- `callsign`: VATSIM callsign.
- `cid`: positive integer VATSIM CID.
- `origin`: ICAO code.
- `destination`: ICAO code.
- `start`: row-specific start datetime.
- `end`: row-specific end datetime.

Constraints:

- If row `end` is present, row `start` MUST also be present.
- Additional properties are not allowed.

## 3. Datetime encoding

Datetime values MUST be strings matching one of the following forms:

- `YYYY-MM-DDTHH:MM:SS`
- `YYYY-MM-DDTHH:MM:SSZ`
- `YYYY-MM-DDTHH:MM:SS+HH:MM`
- `YYYY-MM-DDTHH:MM:SS-HH:MM`
- `YYYY-MM-DD HH:MM:SS`
- `YYYY-MM-DD HH:MM:SS+HH:MM`
- `YYYY-MM-DD HH:MM:SS-HH:MM`

Notes:

- Row-level `start`/`end` override top-level defaults.
- When row-level values are omitted, top-level defaults are used during import.

## 4. API submission body

`POST /stand/reservations/plan` expects an object containing:

- `name` (required string, max 255)
- `contact_email` (required email)
- Payload fields from section 2 (`reservations`, `start`, `end`, `active_from`, `active_to`)

This body is formally defined by:

- `docs/schemas/stand-reservation-plan-request.schema.json`

## 5. Minimal valid examples

### 5.1 Payload-only example

```json
{
  "start": "2026-02-20 09:00:00",
  "end": "2026-02-20 10:00:00",
  "reservations": [
    {
      "airfield": "EGLL",
      "stand": "1L",
      "callsign": "SBI24"
    }
  ]
}
```

### 5.2 API request example

```json
{
  "name": "Speedbird 24",
  "contact_email": "ops@example.com",
  "start": "2026-02-20 09:00:00",
  "end": "2026-02-20 10:00:00",
  "reservations": [
    {
      "airfield": "EGLL",
      "stand": "1L",
      "callsign": "SBI24",
      "cid": 1234567,
      "origin": "UUEE",
      "destination": "EGLL"
    }
  ]
}
```
