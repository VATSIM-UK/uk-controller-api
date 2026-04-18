# VAA Stand Reservation Plan JSON Guide

This guide explains how Virtual Airline Administrators (VAAs) should build JSON payloads for stand reservation plans.

All times must be in Zulu (UTC) format:

- `YYYY-MM-DDTHH:MM:SSZ`
- Example: `2026-06-12T14:30:00Z`

## Top-Level Schema

A stand reservation plan submission contains:

- `name` (string, required): Human-readable plan name.
- `contact_email` (string, required): Contact for validation/import questions.
- `payload` (object, required): Event metadata and reservations.

`payload` fields:

- `event_start` (string, required): Event start in Zulu.
- `event_end` (string, required): Event end in Zulu and after `event_start`.
- Exactly one of:
- `event_airport` (string, required if single-airport event): 4-letter ICAO.
- `event_airports` (array of strings, required if multi-airport event): non-empty, unique 4-letter ICAOs.
- `reservations` (array, required): One or more reservation objects.

## Reservation Schema

Each item in `payload.reservations` must include:

- `cid` (integer, required): Valid VATSIM CID.
- `timefrom` (string, required): Reservation start in Zulu.
- `timeto` (string, required): Reservation end in Zulu and after `timefrom`.
- Exactly one stand reference mode:
- `stand_id` (integer, required if using stand ID mode)
- `stand` (string, required if using stand identifier mode)

Optional field:

- `airport` (string): 4-letter ICAO for stand identifier mode.

`airport` inference rule:

- If you use `stand` and the event has one airport (`event_airport`), `airport` may be omitted.
- If you use `stand` and the event has multiple airports (`event_airports`), `airport` is required per reservation.

## Validation Rules

The server applies these rules:

- Unknown fields are rejected (strict schema).
- Reservation times must be inside the event window (`event_start` to `event_end`).
- Multiple stands can be included in one plan.
- The same stand can be reused at different times if time windows do not overlap.
- Overlapping reservations for the same stand are rejected.

## Valid Example

```json
{
  "name": "Summer Fly-In Plan",
  "contact_email": "ops@example.org",
  "payload": {
    "event_start": "2026-06-12T08:00:00Z",
    "event_end": "2026-06-12T20:00:00Z",
    "event_airports": ["EGLL", "EGKK"],
    "reservations": [
      {
        "stand_id": 1201,
        "cid": 1203533,
        "timefrom": "2026-06-12T08:00:00Z",
        "timeto": "2026-06-12T10:00:00Z"
      },
      {
        "stand_id": 1201,
        "cid": 1203534,
        "timefrom": "2026-06-12T10:15:00Z",
        "timeto": "2026-06-12T12:00:00Z"
      },
      {
        "airport": "EGLL",
        "stand": "A23",
        "cid": 1203535,
        "timefrom": "2026-06-12T09:30:00Z",
        "timeto": "2026-06-12T11:00:00Z"
      },
      {
        "airport": "EGKK",
        "stand": "55",
        "cid": 1203536,
        "timefrom": "2026-06-12T13:00:00Z",
        "timeto": "2026-06-12T15:30:00Z"
      }
    ]
  }
}
```

## Invalid Example

```json
{
  "name": "Invalid Overlap",
  "contact_email": "ops@example.org",
  "payload": {
    "event_start": "2026-06-12T08:00:00Z",
    "event_end": "2026-06-12T20:00:00Z",
    "event_airport": "EGLL",
    "reservations": [
      {
        "stand_id": 1201,
        "cid": 1203533,
        "timefrom": "2026-06-12T10:00:00Z",
        "timeto": "2026-06-12T11:00:00Z"
      },
      {
        "stand_id": 1201,
        "cid": 1203534,
        "timefrom": "2026-06-12T10:30:00Z",
        "timeto": "2026-06-12T11:30:00Z"
      }
    ]
  }
}
```

This is rejected because the same stand has overlapping periods.
