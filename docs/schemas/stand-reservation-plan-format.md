# Stand Reservation Plan JSON Format

This document is the normative specification for the stand-reservation plan JSON format.

## 1. Where to find the schemas

- **Payload schema**: `docs/schemas/stand-reservation-plan.schema.json`
  - Defines the plan payload object (`reservations` and/or `stand_slots`, optional defaults).
- **API request schema**: `docs/schemas/stand-reservation-plan-request.schema.json`
  - Defines the full request body for `POST /stand/reservations/plan`.

If there is any ambiguity in this markdown, the JSON Schema files are authoritative.

## 2. Data model

### 2.1 Payload object

A payload object MUST be a JSON object with these properties:

- At least one of:
  - `reservations`: array of one or more reservation objects.
  - `stand_slots`: array of one or more stand-slot objects.
- Optional top-level default datetimes:
  - `event_start` / `event_finish` (preferred event-window names)
  - `start` / `end` (legacy)
  
Constraints:

- If `end` is present, `start` MUST also be present.
- If `event_finish` is present, `event_start` MUST also be present.
- Additional top-level properties are not allowed.

### 2.2 Reservation row object

Each item in `reservations` (and each item in `stand_slots[].slot_reservations`) MUST be a JSON object with:

- `stand` (**required**): stand identifier string (optional when inherited from `stand_slots[].stand`).
- `airport` (**required**): ICAO code (optional when inherited from `stand_slots[].airport`).

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

### 2.3 Stand-slot object

Each item in `stand_slots` MUST be an object with:

- `stand` (**required**): stand identifier string.
- `airport` (**required**).
- `slot_reservations` (**required**): array of one or more reservation row objects.

`slot_reservations` is where multiple callsigns can be scheduled on the same stand at different times.

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
- When row-level values are omitted, defaults are resolved in this order:
  1. `event_start` / `event_finish`
  2. `start` / `end`

## 4. API submission body

`POST /stand/reservations/plan` expects an object containing:

- `name` (required string, max 255)
- `contact_email` (required email)
- Payload fields from section 2 (`reservations`, `stand_slots`, default datetime fields)

This body is formally defined by:

- `docs/schemas/stand-reservation-plan-request.schema.json`

## 5. Minimal valid examples

### 5.1 Stand-slot payload example

```json
{
  "event_start": "2026-02-20 09:00:00",
  "event_finish": "2026-02-20 10:00:00",
  "stand_slots": [
    {
      "airport": "EGLL",
      "stand": "531",
      "slot_reservations": [
        {
          "callsign": "BAW1234",
          "cid": "12345",
          "slotstart": "2026-02-20 09:00:00",
          "slotend": "2026-02-20 09:30:00"
        },
        {
          "callsign": "BAW4321",
          "cid": "54321",
          "slotstart": "2026-02-20 09:31:00",
          "slotend": "2026-02-20 10:00:00"
        }
      ]
    }
  ]
}
```

### 5.2 API request example

```json
{
  "name": "Speedbird 24",
  "contact_email": "ops@example.com",
  "event_start": "2026-02-20 09:00:00",
  "event_finish": "2026-02-20 10:00:00",
  "stand_slots": [
    {
      "airport": "EGLL",
      "stand": "531",
      "slot_reservations": [
        {
          "callsign": "BAW1234",
          "start": "2026-02-20 09:00:00",
          "end": "2026-02-20 09:30:00",
          "origin": "EGCC",
          "destination": "EGLL"
        },
        {
          "callsign": "BAW4321",
          "start": "2026-02-20 09:31:00",
          "end": "2026-02-20 10:00:00",
          "origin": "EHAM",
          "destination": "EGLL"
        }
      ]
    }
  ]
}
```
