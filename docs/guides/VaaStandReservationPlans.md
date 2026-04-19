# VAA Stand Reservation Plan JSON Guide

This guide explains how Virtual Airline Administrators (VAAs) should build JSON payloads for stand reservation plans.

All times must be in ISO 8601 Zulu (UTC) format:

- `YYYY-MM-DDTHH:MM:SSZ`
- Example: `2026-06-12T14:30:00Z`

## JSON Schema

Use this schema for pre-validation in editors, CI, or upload tooling:

- `docs/guides/schemas/vaa-stand-reservation-plan.schema.json`

The schema validates structure, field types, allowed keys, timestamp shape, CID ranges, UK ICAO format, and stand reference mode.

Server-side checks are still required for rules that JSON Schema cannot enforce on its own:

- `event_end` must be after `event_start`.
- Reservation `timeto` must be after `timefrom`.
- Reservation times must stay within the event window.
- The same stand must not have overlapping reservation windows.

## Top-Level Schema

A stand reservation plan submission contains:

- `name` (string, required): Human-readable plan name.
- `contact_email` (string, required): Contact for validation/import questions.
- `payload` (object, required): Event metadata and reservations.

`payload` fields:

- `event_start` (string, required): Event start in Zulu.
- `event_end` (string, required): Event end in Zulu and after `event_start`.

Use exactly one of the following:

- `event_airport` (string, required if single-airport event): 4-letter ICAO.
- `event_airports` (array of strings, required if multi-airport event): non-empty, unique 4-letter ICAOs.
- `reservations` (array, required): One or more reservation objects. Multiple stands can be included in one plan, and the same stand can be reused at different times as long as the time windows do not overlap.

## Reservation Schema

Each item in `payload.reservations` must include:

- `cid` (integer, required): Valid VATSIM CID.
- `timefrom` (string, required): Reservation start in Zulu. Must be inside the event window (`event_start` to `event_end`).
- `timeto` (string, required): Reservation end in Zulu and after `timefrom`. Must be inside the event window (`event_start` to `event_end`).

Use exactly one stand reference mode:

- `stand_id` (integer, required if using stand ID mode)
- `stand` (string, required if using stand identifier mode)

Optional field:

- `airport` (string): 4-letter ICAO for stand identifier mode.

`airport` inference rule:

- If you use `stand` and the event has one airport (`event_airport`), `airport` may be omitted.
- If you use `stand` and the event has multiple airports (`event_airports`), `airport` is required per reservation.

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
