<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\NavaidResource;
use App\Filament\Resources\NavaidResource\Pages\ListNavaids;
use App\Filament\Resources\NavaidResource\RelationManagers\HoldsRelationManager;
use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use App\Models\Navigation\Navaid;
use Filament\Pages\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\NavaidResource\Pages\EditNavaid;
use Illuminate\Support\Str;
use Livewire\Livewire;

class NavaidResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorAccess;
    use ChecksOperationsContributorActionVisibility;

    protected function tearDown(): void
    {
        parent::tearDown();
        ;
        Str::createUuidsNormally();
    }

    public function testItLoadsDataForView()
    {
        Livewire::test(NavaidResource\Pages\ViewNavaid::class, ['record' => 'WILLO'])
            ->assertSet('data.identifier', 'WILLO')
            ->assertSet('data.latitude', 50.9850000)
            ->assertSet('data.longitude', -0.1916667);
    }

    public function testItCreatesANavaid()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => 12.3,
                'longitude' => 45.6,
            ]
        );
    }

    public function testItCreatesANavaidMaxValues()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 90)
            ->set('data.longitude', 180)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => 90,
                'longitude' => 180,
            ]
        );
    }

    public function testItCreatesANavaidMinValues()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', -90)
            ->set('data.longitude', -180)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => -90,
                'longitude' => -180,
            ]
        );
    }

    public function testItDoesntCreateANavaidDuplicateIdentifier()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntCreateANavaidIdentifierTooLong()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO2')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntCreateANavaidLatitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', -91)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntCreateANavaidLatitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 91)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntCreateANavaidLongitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', -181)
            ->call('create')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItDoesntCreateANavaidLongitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', 181)
            ->call('create')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItEditsANavaid()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'WILLO',
                'latitude' => 12.3,
                'longitude' => 45.6,
            ]
        );
    }

    public function testItEditsANavaidMaxValues()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.latitude', 90)
            ->set('data.longitude', 180)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'id' => 1,
                'identifier' => 'WILLO',
                'latitude' => 90,
                'longitude' => 180,
            ]
        );
    }

    public function testItEditsANavaidMinValues()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.latitude', -90)
            ->set('data.longitude', -180)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'id' => 1,
                'identifier' => 'WILLO',
                'latitude' => -90,
                'longitude' => -180,
            ]
        );
    }

    public function testItDoesntEditANavaidIdentifierTooLong()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO2')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntEditANavaidDuplicateIdentifier()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'TIMBA')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntEditANavaidLatitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', -91)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntEditANavaidLatitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 91)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntEditANavaidLongitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', -181)
            ->call('save')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItDoesntEditANavaidLongitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', 181)
            ->call('save')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItCreatesPublishedHoldsWithNoRestrictions()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Nice Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'outbound_leg_value' => 1.5,
                    'outbound_leg_unit' => 2,
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'navaid_id' => $navaid->id,
                'description' => 'A Nice Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
                'outbound_leg_value' => 1.5,
                'outbound_leg_unit' => 2,
            ]
        );
    }

    public function testItCreatesPublishedHoldsWithLevelBlockRestriction()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 12000,
                                    ],
                                    [
                                        'level' => 13000,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => Hold::max('id'),
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'level-block',
                'levels' => [12000, 13000],
            ],
            $hold->restrictions->first()->restriction
        );
    }

    public function testItCreatesPublishedHoldsWithMinimumLevelRestrictionFullData()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 5000,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => Hold::max('id'),
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLL',
                'override' => 5000,
                'runway' => [
                    'designator' => '27L',
                    'type' => 'any',
                ],
            ],
            $hold->restrictions->first()->restriction
        );
    }

    public function testItCreatesPublishedHoldsWithMinimumLevelRestrictionMinimalData()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => null,
                                'runway' => [
                                    'designator' => null,
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => Hold::max('id'),
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLL',
            ],
            $hold->restrictions->first()->restriction
        );
    }

    public function testItDoesntCreateHoldDescriptionTooLong()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => Str::padLeft('', 256, 'a'),
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['description']);
    }

    public function testItDoesntCreateHoldInboundHeadingTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 0,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['inbound_heading']);
    }

    public function testItDoesntCreateHoldInboundHeadingTooLong()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 361,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['inbound_heading']);
    }

    public function testItDoesntCreateHoldMinimumAltitudeTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 999,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['minimum_altitude']);
    }

    public function testItDoesntCreateHoldMinimumAltitudeTooLarge()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 60001,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['minimum_altitude']);
    }

    public function testItDoesntCreateHoldMaximumAltitudeTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 999,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['maximum_altitude']);
    }

    public function testItDoesntCreateHoldMaximumAltitudeTooBig()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 60001,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['maximum_altitude']);
    }

    public function testItDoesntCreateHoldMaximumAltitudeLessThanMinimum()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['maximum_altitude']);
    }

    public function testItDoesntCreateHoldWithNonNumericOutboundLegValue()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'turn_direction' => 'left',
                    'outbound_leg_value' => 'abc',
                    'outbound_leg_unit' => 2,
                ]
            )
            ->assertHasTableActionErrors(['outbound_leg_value']);
    }

    public function testItDoesntCreateHoldWithOutboundLegValueButNoUnit()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'turn_direction' => 'left',
                    'outbound_leg_value' => 1.5,
                ]
            )
            ->assertHasTableActionErrors(['outbound_leg_unit']);
    }


    public function testItDoesntCreateHoldWithOutboundLegUnitButNoValue()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'turn_direction' => 'left',
                    'outbound_leg_unit' => 2,
                ]
            )
            ->assertHasTableActionErrors(['outbound_leg_value']);
    }

    public function testItDoesntCreateHoldWithLevelBlockRestrictionLevelTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 999,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.levels.0.level']);
    }

    public function testItDoesntCreateHoldWithLevelBlockRestrictionLevelTooBig()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 60001,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.levels.0.level']);
    }

    public function testItDoesntCreatePublishedHoldsWithMinimumLevelRestrictionIfOverrideTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 999,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.override']);
    }

    public function testItDoesntCreatePublishedHoldsWithMinimumLevelRestrictionIfOverrideTooBig()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 60001,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.override']);
    }

    public function testItDoesntCreatePublishedHoldsWithMinimumLevelRestrictionIfOverrideNotANumber()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'create',
            data:
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 'abc',
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.override']);
    }

    public function testItLoadsPublishedHoldDataWithLevelBlockForView()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        $restriction = HoldRestriction::factory()->withLevelBlockRestriction([1000, 2000])
            ->create(['hold_id' => $hold->id]);
        $hold->load('restrictions');

        // For this test, we override the default UUID factory in Laravel's Str facade, so we get predictable results
        // for restrictions
        Str::createUuidsUsingSequence([0, 1, 0]);

        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->mountTableAction(ViewAction::class, $hold)
            ->assertTableActionDataSet(
                [
                    'description' => $hold->description,
                    'inbound_heading' => $hold->inbound_heading,
                    'minimum_altitude' => $hold->minimum_altitude,
                    'maximum_altitude' => $hold->maximum_altitude,
                    'turn_direction' => $hold->turn_direction,
                    'outbound_leg_value' => $hold->outbound_leg_value,
                    'inbound_leg_unit' => $hold->inbound_leg_unit,
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 1000,
                                    ],
                                    [
                                        'level' => 2000,
                                    ],
                                ],
                                'id' => $restriction->id,
                            ],
                        ],
                    ],
                ],
            );
    }

    public function testItLoadsPublishedHoldDataWithMinimumLevelForViewWithFullData()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        $restriction = HoldRestriction::factory()->withMinimumLevelRestriction('MSL', 'EGLL', 5000, '27L')
            ->create(['hold_id' => $hold->id]);
        $hold->load('restrictions');

        // For this test, we override the default UUID factory in Laravel's Str facade, so we get predictable results
        // for restrictions
        Str::createUuidsUsingSequence([0]);

        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->mountTableAction(ViewAction::class, $hold)
            ->assertTableActionDataSet(
                [
                    'description' => $hold->description,
                    'inbound_heading' => $hold->inbound_heading,
                    'minimum_altitude' => $hold->minimum_altitude,
                    'maximum_altitude' => $hold->maximum_altitude,
                    'turn_direction' => $hold->turn_direction,
                    'outbound_leg_value' => $hold->outbound_leg_value,
                    'inbound_leg_unit' => $hold->inbound_leg_unit,
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 5000,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                                'type' => 'minimum-level',
                                'id' => $restriction->id,
                            ],
                        ],
                    ],
                ]
            );
    }

    public function testItLoadsPublishedHoldDataWithMinimumLevelForViewWithMinimumData()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        $restriction = HoldRestriction::factory()->withMinimumLevelRestriction('MSL', 'EGLL')
            ->create(['hold_id' => $hold->id]);
        $hold->load('restrictions');

        // For this test, we override the default UUID factory in Laravel's Str facade, so we get predictable results
        // for restrictions
        Str::createUuidsUsingSequence([0]);

        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->mountTableAction(ViewAction::class, $hold)
            ->assertTableActionDataSet(
                [
                    'description' => $hold->description,
                    'inbound_heading' => $hold->inbound_heading,
                    'minimum_altitude' => $hold->minimum_altitude,
                    'maximum_altitude' => $hold->maximum_altitude,
                    'turn_direction' => $hold->turn_direction,
                    'outbound_leg_value' => $hold->outbound_leg_value,
                    'inbound_leg_unit' => $hold->inbound_leg_unit,
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => null,
                                'runway' => [
                                    'designator' => null,
                                ],
                                'type' => 'minimum-level',
                                'id' => $restriction->id,
                            ],
                        ],
                    ],
                ]
            );
    }

    public function testItEditsPublishedHoldsWithNoRestrictions()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'outbound_leg_value' => 34.3,
                    'outbound_leg_unit' => 2,
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => $hold->id,
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
                'outbound_leg_value' => 34.3,
                'outbound_leg_unit' => 2,
            ]
        );
    }

    public function testItEditsPublishedHoldsWithLevelBlockRestriction()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 12000,
                                    ],
                                    [
                                        'level' => 13000,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => $hold->id,
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'level-block',
                'levels' => [12000, 13000],
            ],
            $hold->restrictions->first()->restriction
        );
    }

    public function testItEditsPublishedHoldsWithMinimumLevelRestrictionFullData()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 5000,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => $hold->id,
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLL',
                'override' => 5000,
                'runway' => [
                    'designator' => '27L',
                    'type' => 'any',
                ],
            ],
            $hold->restrictions->first()->restriction
        );
    }

    public function testItEditsPublishedHoldsWithMinimumLevelRestrictionMinimalData()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => null,
                                'runway' => [
                                    'designator' => null,
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'id' => Hold::max('id'),
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLL',
            ],
            $hold->restrictions->first()->restriction
        );
    }

    public function testItDoesntEditHoldDescriptionTooLong()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => Str::padLeft('', 256, 'a'),
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['description']);
    }

    public function testItDoesntEditHoldInboundHeadingTooSmall()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 0,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['inbound_heading']);
    }

    public function testItDoesntEditHoldInboundHeadingTooLong()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 361,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['inbound_heading']);
    }

    public function testItDoesntEditHoldMinimumAltitudeTooSmall()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 999,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['minimum_altitude']);
    }

    public function testItDoesntEditHoldMinimumAltitudeTooLarge()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 60001,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['minimum_altitude']);
    }

    public function testItDoesntEditHoldMaximumAltitudeTooSmall()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 999,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['maximum_altitude']);
    }

    public function testItDoesntEditHoldMaximumAltitudeTooBig()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 60001,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['maximum_altitude']);
    }

    public function testItDoesntEditHoldMaximumAltitudeLessThanMinimum()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['maximum_altitude']);
    }

    public function testItDoesntEditHoldNonNumericOutboundLegValue()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'outbound_leg_value' => 'abc',
                    'outbound_leg_unit' => 2,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['outbound_leg_value']);
    }

    public function testItDoesntEditHoldWithOutboundLegUnitButNoValue()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'outbound_leg_unit' => 2,
                    'outbound_leg_value' => null,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['outbound_leg_value']);
    }

    public function testItDoesntEditHoldWithOutboundLegValueButNoUnit()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 3900,
                    'outbound_leg_value' => 1.5,
                    'outbound_leg_unit' => null,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasTableActionErrors(['outbound_leg_unit']);
    }

    public function testItDoesntEditHoldWithLevelBlockRestrictionLevelTooSmall()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 999,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.levels.0.level']);
    }

    public function testItDoesntEditHoldWithLevelBlockRestrictionLevelTooBig()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'level-block',
                            'data' => [
                                'levels' => [
                                    [
                                        'level' => 60001,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.levels.0.level']);
    }

    public function testItDoesntEditPublishedHoldsWithMinimumLevelRestrictionIfOverrideTooSmall()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 999,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.override']);
    }

    public function testItDoesntEditPublishedHoldsWithMinimumLevelRestrictionIfOverrideTooBig()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 60001,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.override']);
    }

    public function testItDoesntEditPublishedHoldsWithMinimumLevelRestrictionIfOverrideNotANumber()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                'edit',
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 'abc',
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertHasTableActionErrors(['restrictions.0.data.override']);
    }

    public function testItRemovesHoldRestrictionsOnEdit()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        $restriction = HoldRestriction::factory()->withMinimumLevelRestriction('MSL', 'EGLL', 5000, '27L')
            ->create(['hold_id' => $hold->id]);
        $hold->load('restrictions');

        // For this test, we override the default UUID factory in Laravel's Str facade, so we get predictable results
        // for restrictions
        Str::createUuidsUsingSequence([0]);

        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                EditAction::class,
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [],
                ]
            );

        $this->assertDatabaseMissing(
            'hold_restrictions',
            [
                'id' => $restriction->id,
            ]
        );
    }

    public function testItUpdatesHoldRestrictions()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(Hold::factory()->make());
        $restriction = HoldRestriction::factory()->withMinimumLevelRestriction('MSL', 'EGLL', 5000, '27L')
            ->create(['hold_id' => $hold->id]);
        $hold->load('restrictions');

        // For this test, we override the default UUID factory in Laravel's Str facade, so we get predictable results
        // for restrictions
        Str::createUuidsUsingSequence([0]);

        Livewire::test(HoldsRelationManager::class, ['ownerRecord' => $navaid, 'pageClass' => EditNavaid::class])
            ->callTableAction(
                EditAction::class,
                $hold,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                    'restrictions' => [
                        [
                            'type' => 'minimum-level',
                            'data' => [
                                'id' => $restriction->id,
                                'level' => 'MSL',
                                'target' => 'EGLL',
                                'override' => 8000,
                                'runway' => [
                                    'designator' => '27L',
                                ],
                            ],
                        ],
                    ],
                ]
            );

        $hold = Hold::latest('id')->first();
        $this->assertCount(1, $hold->restrictions);
        $this->assertEquals(
            [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLL',
                'override' => 8000,
                'runway' => [
                    'designator' => '27L',
                    'type' => 'any',
                ],
            ],
            $hold->restrictions->first()->restriction
        );
    }

    protected static function resourceClass(): string
    {
        return NavaidResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit WILLO';
    }

    protected function getCreateText(): string
    {
        return 'Create Navaids And Holds';
    }

    protected function getViewText(): string
    {
        return 'View WILLO';
    }

    protected function getIndexText(): array
    {
        return ['WILLO', 'TIMBA', 'MAY'];
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceRecordClass(): string
    {
        return Navaid::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListNavaids::class;
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

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function tableActionRecordClass(): array
    {
        return [HoldsRelationManager::class => Hold::class];
    }

    protected static function tableActionRecordId(): array
    {
        return [HoldsRelationManager::class => 1];
    }

    protected static function writeTableActions(): array
    {
        return [
            HoldsRelationManager::class => [
                'create',
                'edit',
                'delete',
            ],
        ];
    }

    protected function getEditRecord(): Model
    {
        return Navaid::findOrFail(1);
    }

    protected function getViewRecord(): Model
    {
        return Navaid::findOrFail(1);
    }
}
