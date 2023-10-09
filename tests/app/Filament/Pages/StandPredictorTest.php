<?php

namespace App\Filament\Pages;

use App\Allocator\Stand\AirlineCallsignArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignSlugArrivalStandAllocator;
use App\Allocator\Stand\CargoAirlineFallbackStandAllocator;
use App\Allocator\Stand\OriginAirfieldStandAllocator;
use App\BaseFilamentTestCase;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\User\RoleKeys;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class StandPredictorTest extends BaseFilamentTestCase
{
    #[DataProvider('renderRoleProvider')]
    public function testItRenders(?RoleKeys $role, bool $shouldRender)
    {
        if ($role !== null) {
            $this->assumeRole($role);
        } else {
            $this->noRole();
        }

        $response = Livewire::test(StandPredictor::class);
        if ($shouldRender) {
            $response->assertOk();
        } else {
            $response->assertForbidden();
        }
    }

    public static function renderRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }

    public function testItPresentsStandPredictionsOnEvent()
    {
        // Create models for the test
        $arrivalAirfield = Airfield::factory()->create();
        $departureAirfield = Airfield::factory()->create();

        // Callsign specific
        $stand1 = Stand::factory()->create(
            ['airfield_id' => $arrivalAirfield->id, 'identifier' => '1A', 'assignment_priority' => 1]
        );
        $stand1->airlines()->sync([1 => ['full_callsign' => '221']]);
        $stand2 = Stand::factory()->create(
            ['airfield_id' => $arrivalAirfield->id, 'identifier' => '2A', 'assignment_priority' => 1]
        );
        $stand2->airlines()->sync([1 => ['full_callsign' => '221']]);

        // Callsign specfic, but with a lower priorityy
        $stand3 = Stand::factory()->create(['airfield_id' => $arrivalAirfield->id, 'assignment_priority' => 2]);
        $stand3->airlines()->sync([1 => ['full_callsign' => '221', 'priority' => 101]]);

        // Generic
        $stand4 = Stand::factory()->create(['airfield_id' => $arrivalAirfield->id, 'assignment_priority' => 3]);
        $stand4->airlines()->sync([1]);

        Livewire::test(StandPredictor::class)
            ->emit(
                'standPredictorFormSubmitted',
                [
                    'callsign' => 'BAW999',
                    'cid' => 1202533,
                    'planned_destairport' => $arrivalAirfield->code,
                    'planned_depairport' => $departureAirfield->code,
                    'aircraft_id' => 1,
                    'airline_id' => 1,
                ]
            )
            ->assertSeeHtmlInOrder(
                [
                    sprintf(
                        'Allocator: %s',
                        CargoAirlineFallbackStandAllocator::class
                    ),
                    'No stands for this allocator',
                    sprintf(
                        'Allocator: %s',
                        OriginAirfieldStandAllocator::class
                    )
                ]
            )->assertSeeHtml(
                [
                    sprintf(
                        'Allocator: %s',
                        AirlineCallsignArrivalStandAllocator::class
                    ),
                    'Rank 1',
                    implode(',', [$stand1->identifier, $stand2->identifier]),
                    'Rank 2',
                    $stand3->identifier,
                    sprintf(
                        'Allocator: %s',
                        AirlineCallsignSlugArrivalStandAllocator::class
                    )
                ]
            );
    }
}
