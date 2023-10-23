<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\AirfieldResource;
use App\Filament\Resources\AirfieldResource\Pages\CreateAirfield;
use App\Filament\Resources\AirfieldResource\Pages\EditAirfield;
use App\Filament\Resources\AirfieldResource\Pages\ListAirfields;
use App\Filament\Resources\AirfieldResource\RelationManagers\ControllersRelationManager;
use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Services\ControllerPositionHierarchyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

class AirfieldResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentActionVisibility;
    use ChecksDefaultFilamentAccess;

    protected function setUp(): void
    {
        parent::setUp();
        Airfield::findOrFail(1)->update(['handoff_id' => 1]);
    }

    public function testItLoadsDataForView()
    {
        Livewire::test(AirfieldResource\Pages\ViewAirfield::class, ['record' => 1])
            ->assertSet('data.code', 'EGLL')
            ->assertSet('data.latitude', 51.4775)
            ->assertSet('data.longitude', -0.461389)
            ->assertSet('data.elevation', 1)
            ->assertSet('data.wake_category_scheme_id', 1)
            ->assertSet('data.transition_altitude', 6000)
            ->assertSet('data.standard_high', true);
    }

    public function testItCreatesAnAirfield()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airfield',
            [
                'code' => 'EGKK',
                'latitude' => 12.3,
                'longitude' => 45.6,
                'elevation' => 123,
                'transition_altitude' => 3000,
                'standard_high' => 1,
                'wake_category_scheme_id' => 1,
            ]
        );
    }

    public function testItCreatesADefaultHandoffWithTheAirfield()
    {
        $this->assertFalse(
            Handoff::where('description', 'Default departure handoff for EGKK')->exists()
        );
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasNoErrors();

        $handoff = Handoff::where('description', 'Default departure handoff for EGKK')->firstOrFail();

        $this->assertDatabaseHas(
            'airfield',
            [
                'code' => 'EGKK',
                'latitude' => 12.3,
                'longitude' => 45.6,
                'elevation' => 123,
                'transition_altitude' => 3000,
                'standard_high' => 1,
                'wake_category_scheme_id' => 1,
                'handoff_id' => $handoff->id,
            ]
        );
    }

    public function testItDoesntCreateAirfieldNoIcao()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntCreateAirfieldInvalidIcao()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGLLLL')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntCreateAirfieldDuplicateIcao()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGLL')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntCreateAirfieldNoCoordinates()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntCreateAirfieldInvalidCoordinates()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 11111)
            ->set('data.longitude', 1111)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntCreateAirfieldNoElevation()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntCreateAirfieldElevationInvalid()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 'abc')
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntCreateAirfieldNoWakeScheme()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.wake_category_scheme_id']);
    }

    public function testItDoesntCreateAirfieldNoTransitionAltitude()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntCreateAirfieldInvalidTransitionAltitude()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 'abc')
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntCreateAirfieldInvalidTransitionAltitudeTooSmall()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', -1)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntCreateAirfieldInvalidTransitionAltitudeTooBig()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 20001)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItEditsAnAirfield()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.34)
            ->set('data.longitude', 45.67)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airfield',
            [
                'id' => 1,
                'code' => 'EGKK',
                'latitude' => 12.34,
                'longitude' => 45.67,
                'elevation' => 123,
                'transition_altitude' => 3000,
                'standard_high' => 1,
                'wake_category_scheme_id' => 1,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItDoesntEditAirfieldNoIcao()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntEditAirfieldInvalidIcao()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGLLLL')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntEditAirfieldNoCoordinates()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude')
            ->set('data.longitude')
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntEditAirfieldInvalidCoordinates()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 11111)
            ->set('data.longitude', 1111)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntEditAirfieldNoElevation()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation')
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntEditAirfieldElevationInvalid()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 'abc')
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntEditAirfieldNoWakeScheme()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id')
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.wake_category_scheme_id']);
    }

    public function testItDoesntEditAirfieldNoTransitionAltitude()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude')
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntEditAirfieldInvalidTransitionAltitude()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 'abc')
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntEditAirfieldInvalidTransitionAltitudeTooSmall()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', -1)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntEditAirfieldInvalidTransitionAltitudeTooBig()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 20001)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testAirfieldsCanBeDeletedFromTheEditPage()
    {
        $airfield = Airfield::factory()->create();
        $airfield->msl()->create(['msl' => 123]);
        $this->assertDatabaseHas('msl_airfield', ['airfield_id' => $airfield->id]);

        Livewire::test(EditAirfield::class, ['record' => $airfield->id])
            ->callPageAction('delete')
            ->assertHasNoErrors()
            ->assertRedirect(AirfieldResource::getUrl('index'));

        $this->assertDatabaseMissing('airfield', ['id' => $airfield->id]);
        $this->assertDatabaseMissing('msl_airfield', ['airfield_id' => $airfield->id]);
    }

    public function testItDisplaysControllers()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )->assertCanSeeTableRecords([1, 2]);
    }

    public function testControllersCanBeAttachedAtTheEnd()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('attach', data: ['recordId' => 4])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeAttachedAfterAnotherController()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('attach', data: ['recordId' => 3, 'insert_after' => 1])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemoved()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemovedAtTheEnd()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_N_APP',
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrderAtTheTop()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(1))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownTheOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownAtTheBottom()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testAttachingAControllerChangesDefaultHandoffOrder()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('attach', data: ['recordId' => 4])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->handoff
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testRemovingControllersChangesDefaultHandoffOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->handoff
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testMovingControllersDownChangesDefaultHandoffOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'LON_S_CTR',
                'EGLL_N_APP',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->handoff
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testMovingControllersUpChangesDefaultHandoffOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Airfield::findOrFail(1)]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(3))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'LON_S_CTR',
                'EGLL_N_APP',
                'LON_C_CTR',
            ],
            Airfield::findOrFail(1)
                ->handoff
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    protected function getCreateText(): string
    {
        return 'Create Airfield';
    }

    protected function getEditRecord(): Model
    {
        return Airfield::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit EGLL';
    }

    protected function getIndexText(): array
    {
        return ['Airfields', 'EGLL', 'EGBB', 'EGKR'];
    }

    protected function getViewText(): string
    {
        return 'View EGLL';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected function resourceClass(): string
    {
        return AirfieldResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return Airfield::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function resourceListingClass(): string
    {
        return ListAirfields::class;
    }

    protected static function tableActionRecordClass(): array
    {
        return [ControllersRelationManager::class => ControllerPosition::class];
    }

    protected static function tableActionRecordId(): array
    {
        return [ControllersRelationManager::class => 1];
    }

    protected static function writeTableActions(): array
    {
        return [
            ControllersRelationManager::class => [
                'attach',
                'detach',
                'moveUp',
                'moveDown',
            ],
        ];
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }
}
