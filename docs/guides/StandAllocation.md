# Stand Allocation

The UK Controller Plugin is responsible for assigning stands to aircraft arriving into UK airports on the VATSIM network.
Whilst the final decision on where aircraft should park belongs to the controller, the plugin will attempt to ensure that
a realistic stand is assigned to each flight, based on a number of parameters. This is a highly complex system, so this guide
is intended to explain how it all works under the hood.

# Stand Occupation

Every minute, the system will look at aircraft currently on the ground at UK airports. If an aircraft is deemed to be within
a small distance from a stand, then the plugin will mark that stand as "occupied". This prevents the stand from being assigned
by the automatic stand allocator for arriving aircraft, though it does not prevent controllers from manually setting the stand.

## Occupation to assignment

Once an aircraft files a flightplan, if the origin airport matches the airport of its currently occupied stand, that stand will
be formally assigned to the aircraft. This allows the stand assignment to be shown automatically in controllers datablocks.

## Stand pairings

There are a number of stands that cannot be used simultaneously due to their proximity to each other. If a stand is deemed to be
"occupied" or "assigned", then any stand to which it is paired will be removed from consideration for automatic assignment
to arriving aircraft.

# Departure

For aircraft departing UK airports, the UK Controller Plugin will automatically allocate the stand the aircraft occupies once
its flightplan has been filed.

# Arrival

Stands are assigned to arriving aircraft when they are **20 minutes** straight line flying time from their destination. Stands will be re-issued in the case
of the stand becoming occupied by another aircraft.

The principle behind the arrival stand allocation process is very simple, albeit with a number of moving parts.

It operates on the basis of a number of "specific rules". Broadly speaking, the rules go from "more specialised", aka specific CID and callsign, all the
way to "just give them a stand". This ensures that the quality of stands gracefully degrades and therefore realism is maintained wherever possible. For example,
we'll always try to match an aircraft to a stand for its airline and where it's come from, before trying to just give it one that its airline uses.

There are also a number of "common rules", which are applied in addition to every specific rule. These check for things such as stand occupancy,
suitable size, etc.

The specific and common rules may also apply ordering to the returned stands, which is executed sequentially. For example, we might
sort stands by a match on where they came from, and then sort those that share the same value by the size of the stand.

The sequence of operations is broadly as follows:

1. Pick the first "specific rule" and execute its logic.
2. For any possible stands returned by that rule, apply the "common rules".
3. If a viable stand is found, use it.
4. If no viable stand found, repeat steps 1-4 for the next "specific rule". If no more rules remaining, then no stand could be assigned, stop.

## The Rules

Below is a description of the specific rules used to assign stands to arriving aircraft, in the order in which they are applied.

### CID Reserved

This rule matches flights for a particular arrival airfield and a specific member CID. It is only used in organised events
where stand reservations are necessary.

### Callsign Reserved

This rule matches flights for a particular arrival airfield and a specific callsign. It is only used in organised events
where stand reservations are necessary.

### User Requested

This rule matches flights for a particular user, if they have requested a stand.

The requested stand will be eligible for assignment from 40 minutes before, until 20 minutes after the requested time.

### Cargo Flight (Airline Specific)

This rule only applies to flights that have `RMK/CARGO` in their flightplan remarks.

It will allocate stands of type `CARGO` assigned to the aircraft's airline, according to that airlines preferred priority for stands.

### Cargo Flight

This rule only applies to flights that have `RMK/CARGO` in their flightplan remarks.

It will allocate any stands of type `CARGO`.

### Business Aviation Aircraft

This rule only applies to aircaft that are designated as Business Aviation.

It will allocate any stands of type `BUSINESS AVIATION`.

### Airline Callsign

This rule takes the callsign slug (the bit after the airline code in the callsign), along with the airline for the given flight.

It then allocates any stands assigned to that airline which have a specified callsign that matches. These matches are exact matches
only. For partial matches, see below.

### Airline Callsign Slug

This rule takes the callsign slug (the bit after the airline code in the callsign), along with the airline for the given flight.

It then allocates any stands assigned to that airline which have a specified callsign slug that matches. Callsign slug matches may be
partial, although more complete matches are preferred. Of those stands with the "best" callsign match, one is chosen based on the airlines priority preference.

For example, a flight with callsign `BAW1234` will match any stand assigned to BA with a callsign slug of (in priority order):

- 1234
- 123
- 12
- 1

### Airline Aircraft Type

This rule takes the aircraft type, along with the airline for the given flight.

It then allocates any stands assigned to that airline with a specific aircraft type that matches.

### Airline Destination

This rule takes the origin airport (ie, the one they departed from), along with the airline for the given flight.

It then allocates any stands assigned to that airline which have a specified destination that matches. Destination matches may be
partial, although more complete matches are preferred. Of those stands with the "best" destination match, one is chosen based on the airlines priority preference.

For example, a flight with callsign `BAW1234` arriving from `LFPG` will match any stand assigned to BA with a destination of (in priority order):

- LFPG
- LFP
- LF
- L

This allows stands to be assigned for anything from specific airports, to terminal areas, to entire countries.

### Airline

This rule will allocate any stands assigned to the given airline that do not have a callsign slug or destination specified. Stands are chosen
based on the airlines priority preference.

This is helpful when airlines only park on a few particular stands on a given pier/terminal.

### Airline Callsign (Terminal)

This rule takes the callsign slug (the bit after the airline code in the callsign), along with the airline for the given flight.

It then allocates any stand on a terminal with a specific callsign that matches for that airline. These matches are exact matches
only. For partial matches, see below.

### Airline Callsign Slug (Terminal)

This rule takes the callsign slug (the bit after the airline code in the callsign), along with the airline for the given flight.

It then allocates any stand on a terminal with a specified callsign slug that matches for that airline. Callsign slug matches may be
partial, although more complete matches are preferred. Of those terminals with the "best" callsign match, one is chosen based on the airlines priority preference.

For example, a flight with callsign `BAW1234` will match any stand assigned to BA with a callsign slug of (in priority order):

- 1234
- 123
- 12
- 1

### Airline Aircraft Type (Terminal)

This rule takes the aircraft type, along with the airline for the given flight.

It then allocates any stand on a terminal with a specific aircraft type that matches for that airline.

### Airline Destination (Terminal)

This rule takes the origin airport (ie, the one they departed from), along with the airline for the given flight.

It then allocates any stand on a terminal with a specified destination that matches for that airline. Destination matches may be
partial, although more complete matches are preferred. Of those stands with the "best" destination match, one is chosen based on the airlines priority preference.

For example, a flight with callsign `BAW1234` arriving from `LFPG` will match any stand assigned to BA with a destination of (in priority order):

- LFPG
- LFP
- LF
- L

### Airline (Terminal)

This rule will allocate any stand at a terminal assigned to the given airline, where the terminal does not have a callsign slug or destination specified. Stands are chosen
based on the airlines priority preference.

### Cargo Airline

This is a fallback rule that will assign any stand of type `CARGO` if the airline in question is designated as a cargo airline.

### Origin Airfield

This rule will assign any stand which has a specified origin airfield that matches the aircrafts origin.

Origin matches may be partial, although more complete matches are preferred.

For example, a flight with callsign `BAW1234` arriving from `LFPG` will match any stand with an origin of of (in priority order):

- LFPG
- LFP
- LF
- L

### Domestic / International

This rule will assign any stand that has a specified `DOMESTIC` / `INTERNATIONAL` type, depending on the aircrafts origin.

For the purpose of this rule, `DOMESTIC` is deemed to mean airfields with ICAOs starting `EG` or `EI`.

**PLEASE NOTE**: You should only set `DOMESTIC` / `INTERNATIONAL` for a stand at an airfield if you plan to do this for all non-cargo stands. Failure to
do this will result in unusual stands being allocated, as this allocator will not consider "more desirable" stands that have not been marked as such.

### Fallback

If everything else has failed, then this allocator will pick a random stand.


## Common rules applied after specific rules

After a specific rule has found a potential match, the following rules are applied:

- The stand must be large enough (in terms of the stands max WTC or aircraft size) to accomodate the flight
- The stand must be available (not reserved, not occupied, not assigned (or paired to one that this))

The "best" stands left over at this point are then:

- Sorted by ascending size (so we don't give a huge stand to a LearJet when there's an A380 on the way!)
- Then sorted by general priority, or "desirability". This is especially useful when airlines don't have specific preferences - many VATSIM pilots just want to be close to the terminal, so this makes sure we don't send everyone to a remote stand.
- Then sorted randomly, this spices things up a bit and means we don't assign the same stands every single time.

If the "common rules" reject all possible stands, then the next specific rule is invoked and we start over. Otherwise,
we've found a match, and that stand is assigned!
