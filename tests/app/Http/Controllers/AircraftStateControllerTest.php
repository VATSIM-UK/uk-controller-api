<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Vatsim\GroundState;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class AircraftStateControllerTest extends BaseApiTestCase
{
    private const URI = 'aircraft-state';

    public function setUp(): void
    {
        parent::setUp();

        // The first test in a process writes permanently - migrate:fresh commits the DDL out from
        // under DatabaseTransactions. Query builder, not Eloquent, which would bump updated_at.
        DB::table('network_aircraft')->update(
            [
                'clearance_flag' => false,
                'ground_state' => null,
                'clearance_flag_updated_at' => null,
                'ground_state_updated_at' => null,
            ]
        );
    }

    private static function at(string $time = 'now'): string
    {
        return Carbon::parse($time)->toDateTimeString();
    }

    private function sendUpdates(array $updates): TestResponse
    {
        return $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::URI, ['updates' => $updates]);
    }

    public function testItReturnsAircraftWithAState()
    {
        NetworkAircraft::findOrFail('BAW123')->update(
            [
                'clearance_flag' => true,
                'ground_state' => GroundState::Taxi,
            ]
        );
        NetworkAircraft::findOrFail('BAW456')->update(
            [
                'clearance_flag' => false,
                'ground_state' => GroundState::Pushback,
            ]
        );

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, self::URI)
            ->assertJson(
                [
                    [
                        'callsign' => 'BAW123',
                        'clearance_flag' => true,
                        'ground_state' => 'TAXI',
                    ],
                    [
                        'callsign' => 'BAW456',
                        'clearance_flag' => false,
                        'ground_state' => 'PUSH',
                    ],
                ]
            )
            ->assertStatus(200);
    }

    public function testItDoesNotReturnAircraftWithNoState()
    {
        NetworkAircraft::findOrFail('BAW123')->update(['clearance_flag' => true]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, self::URI)
            ->assertStatus(200);

        $this->assertCount(1, $response->json());
        $this->assertEquals('BAW123', $response->json()[0]['callsign']);
    }

    public function testItReturnsEmptyArrayWhenNoAircraftHaveState()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, self::URI)
            ->assertJson([])
            ->assertStatus(200);
    }

    public function testItRejectsUnauthenticatedStateFetches()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::URI)
            ->assertStatus(401);
    }

    public function testItSetsTheClearanceFlag()
    {
        $this->sendUpdates(
            [['callsign' => 'BAW123', 'clearance_flag' => true, 'clearance_flag_at' => self::at()]]
        )->assertStatus(200);

        $this->assertDatabaseHas(
            'network_aircraft',
            [
                'callsign' => 'BAW123',
                'clearance_flag' => true,
                'ground_state' => null,
            ]
        );
    }

    public function testItSetsTheGroundState()
    {
        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Pushback->value,
                'ground_state_at' => self::at(),
            ]]
        )->assertStatus(200);

        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW123', 'ground_state' => 'PUSH']);
    }

    public function testItAppliesEveryUpdateInTheBatch()
    {
        $this->sendUpdates(
            [
                ['callsign' => 'BAW123', 'ground_state' => GroundState::Taxi->value, 'ground_state_at' => self::at()],
                ['callsign' => 'BAW456', 'clearance_flag' => true, 'clearance_flag_at' => self::at()],
                [
                    'callsign' => 'BAW789',
                    'ground_state' => GroundState::Departure->value,
                    'ground_state_at' => self::at(),
                ],
            ]
        )->assertStatus(200);

        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW123', 'ground_state' => 'TAXI']);
        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW456', 'clearance_flag' => true]);
        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW789', 'ground_state' => 'DEPA']);
    }

    public function testItIgnoresUpdatesOlderThanWhatIsStored()
    {
        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Taxi->value,
                'ground_state_at' => self::at('2026-08-29 12:00:10'),
            ]]
        )->assertStatus(200);

        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Pushback->value,
                'ground_state_at' => self::at('2026-08-29 12:00:05'),
            ]]
        )->assertStatus(200);

        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW123', 'ground_state' => 'TAXI']);
    }

    public function testItAppliesUpdatesNewerThanWhatIsStored()
    {
        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Taxi->value,
                'ground_state_at' => self::at('2026-08-29 12:00:05'),
            ]]
        )->assertStatus(200);

        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Departure->value,
                'ground_state_at' => self::at('2026-08-29 12:00:10'),
            ]]
        )->assertStatus(200);

        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW123', 'ground_state' => 'DEPA']);
    }

    public function testItOrdersEachFieldIndependently()
    {
        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'clearance_flag' => true,
                'clearance_flag_at' => self::at('2026-08-29 12:00:10'),
            ]]
        )->assertStatus(200);

        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Taxi->value,
                'ground_state_at' => self::at('2026-08-29 12:00:05'),
            ]]
        )->assertStatus(200);

        $this->assertDatabaseHas(
            'network_aircraft',
            ['callsign' => 'BAW123', 'clearance_flag' => true, 'ground_state' => 'TAXI']
        );
    }

    public function testItLeavesUnspecifiedFieldsUntouched()
    {
        NetworkAircraft::findOrFail('BAW123')->update(
            [
                'clearance_flag' => true,
                'ground_state' => GroundState::Taxi,
            ]
        );

        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'ground_state' => GroundState::Departure->value,
                'ground_state_at' => self::at(),
            ]]
        )->assertStatus(200);

        $this->assertDatabaseHas(
            'network_aircraft',
            [
                'callsign' => 'BAW123',
                'clearance_flag' => true,
                'ground_state' => 'DEPA',
            ]
        );
    }

    public function testItClearsTheGroundState()
    {
        NetworkAircraft::findOrFail('BAW123')->update(['ground_state' => GroundState::Taxi]);

        $this->sendUpdates(
            [['callsign' => 'BAW123', 'ground_state' => null, 'ground_state_at' => self::at()]]
        )->assertStatus(200);

        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'BAW123', 'ground_state' => null]);
    }

    public function testItDoesNotWriteWhenTheValuesAreUnchanged()
    {
        NetworkAircraft::findOrFail('BAW123')->update(
            [
                'clearance_flag' => true,
                'ground_state' => GroundState::Taxi,
                'clearance_flag_updated_at' => self::at('2026-08-29 12:00:05'),
                'ground_state_updated_at' => self::at('2026-08-29 12:00:05'),
            ]
        );

        $this->sendUpdates(
            [[
                'callsign' => 'BAW123',
                'clearance_flag' => true,
                'clearance_flag_at' => self::at('2026-08-29 12:00:06'),
                'ground_state' => GroundState::Taxi->value,
                'ground_state_at' => self::at('2026-08-29 12:00:06'),
            ]]
        )->assertStatus(200);

        // Unchanged timestamps prove the save was skipped.
        $this->assertDatabaseHas(
            'network_aircraft',
            [
                'callsign' => 'BAW123',
                'clearance_flag_updated_at' => self::at('2026-08-29 12:00:05'),
                'ground_state_updated_at' => self::at('2026-08-29 12:00:05'),
            ]
        );
    }

    public function testItCreatesAPlaceholderAircraftIfNotOnTheNetwork()
    {
        $this->sendUpdates(
            [['callsign' => 'EZY789', 'clearance_flag' => true, 'clearance_flag_at' => self::at()]]
        )->assertStatus(200);

        $this->assertDatabaseHas('network_aircraft', ['callsign' => 'EZY789', 'clearance_flag' => true]);
    }

    public function testItRejectsUnauthenticatedUpdates()
    {
        $this->makeUnauthenticatedApiRequest(
            self::METHOD_PUT,
            self::URI,
            ['updates' => [['callsign' => 'BAW123', 'clearance_flag' => true, 'clearance_flag_at' => self::at()]]]
        )->assertStatus(401);
    }

    #[DataProvider('badUpdateDataProvider')]
    public function testItRejectsBadUpdateData(array $data)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::URI, $data)
            ->assertStatus(422);
    }

    public static function badUpdateDataProvider(): array
    {
        $at = '2026-08-29 12:00:00';

        return [
            'Empty payload' => [[]],
            'Updates not an array' => [['updates' => 'wibble']],
            'No callsign' => [['updates' => [['clearance_flag' => true, 'clearance_flag_at' => $at]]]],
            'Invalid callsign' => [
                ['updates' => [[
                    'callsign' => 'not a callsign!',
                    'clearance_flag' => true,
                    'clearance_flag_at' => $at,
                ]]],
            ],
            'No fields' => [['updates' => [['callsign' => 'BAW123']]]],
            'Unknown ground state' => [
                ['updates' => [['callsign' => 'BAW123', 'ground_state' => 'WIBBLE', 'ground_state_at' => $at]]],
            ],
            'Ground state not a string' => [
                ['updates' => [['callsign' => 'BAW123', 'ground_state' => 123, 'ground_state_at' => $at]]],
            ],
            'Clearance flag not a boolean' => [
                ['updates' => [['callsign' => 'BAW123', 'clearance_flag' => 'abc', 'clearance_flag_at' => $at]]],
            ],
            'Clearance flag with no timestamp' => [
                ['updates' => [['callsign' => 'BAW123', 'clearance_flag' => true]]],
            ],
            'Ground state with no timestamp' => [
                ['updates' => [['callsign' => 'BAW123', 'ground_state' => 'TAXI']]],
            ],
            'Cleared ground state with no timestamp' => [
                ['updates' => [['callsign' => 'BAW123', 'ground_state' => null]]],
            ],
            'Timestamp not a date' => [
                ['updates' => [['callsign' => 'BAW123', 'clearance_flag' => true, 'clearance_flag_at' => 'wibble']]],
            ],
        ];
    }
}
