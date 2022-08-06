<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\NavaidResource;
use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use App\Models\Navigation\Navaid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class NavaidResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;

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
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
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

    public function testItEditsANavaidMaxValues()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 90)
            ->set('data.longitude', 180)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'id' => 1,
                'identifier' => 'NOVMA',
                'latitude' => 90,
                'longitude' => 180,
            ]
        );
    }

    public function testItEditsANavaidMinValues()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', -90)
            ->set('data.longitude', -180)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'id' => 1,
                'identifier' => 'NOVMA',
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
                [
                    'description' => 'A Hold',
                    'inbound_heading' => 123,
                    'minimum_altitude' => 4000,
                    'maximum_altitude' => 5000,
                    'turn_direction' => 'left',
                ]
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'holds',
            [
                'navaid_id' => $navaid->id,
                'description' => 'A Hold',
                'inbound_heading' => 123,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'left',
            ]
        );
    }

    public function testItCreatesPublishedHoldsWithLevelBlockRestriction()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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

        $this->assertDatabaseHas(
            'hold_restrictions',
            [
                'hold_id' => Hold::max('id'),
                'restriction' => $this->castAsJson([
                    'type' => 'level-block',
                    'levels' => [12000, 13000],
                ]),
            ]
        );
    }

    public function testItCreatesPublishedHoldsWithMinimumLevelRestrictionFullData()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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

        $this->assertDatabaseHas(
            'hold_restrictions',
            [
                'hold_id' => Hold::max('id'),
                'restriction' => $this->castAsJson([
                    'type' => 'minimum-level',
                    'level' => 'MSL',
                    'target' => 'EGLL',
                    'override' => 5000,
                    'runway' => [
                        'designator' => '27L',
                        'type' => 'any',
                    ],
                ]),
            ]
        );
    }

    public function testItCreatesPublishedHoldsWithMinimumLevelRestrictionMinimalData()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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

        $this->assertDatabaseHas(
            'hold_restrictions',
            [
                'hold_id' => Hold::max('id'),
                'restriction' => $this->castAsJson([
                    'type' => 'minimum-level',
                    'level' => 'MSL',
                    'target' => 'EGLL',
                ]),
            ]
        );
    }

    public function testItDoesntCreateHoldDescriptionTooLong()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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

    public function testItDoesntCreateHoldWithLevelBlockRestrictionLevelTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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

    public function testItDoesntCredatePublishedHoldsWithMinimumLevelRestrictionIfOverrideTooSmall()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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

    public function testItDoesntCredatePublishedHoldsWithMinimumLevelRestrictionIfOverrideTooBig()
    {
        $navaid = Navaid::factory()->create();
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction(
                'create', $navaid,
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
        $hold = $navaid->holds()->save(
            Hold::factory()
                ->has(HoldRestriction::factory()->withLevelBlockRestriction([1000, 2000]))->make()
        );

        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction('view', $hold)
            ->assertSet('form', $hold->description)
            ->assertSet('inbound_heading', $hold->inbound_heading)
            ->assertSet('minimum_altitude', $hold->minimum_altitude)
            ->assertSet('maximum_altitude', $hold->maximum_altitude)
            ->assertSet('turn_direction', $hold->turn_direction)
            ->assertSet('restrictions.0.type', 'level-block')
            ->assertSet('restrictions.0.data.levels.0.level', 1000)
            ->assertSet('restrictions.0.data.levels.1.level', 2000);
    }

    public function testItLoadsPublishedHoldDataWithMinimumLevelForViewWithFullData()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(
            Hold::factory()
                ->has(HoldRestriction::factory()->withMinimumLevelRestriction('MSL', 'EGLL', 5000, '27L'))->make()
        );

        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction('view', $hold)
            ->assertSet('description', $hold->description)
            ->assertSet('inbound_heading', $hold->inbound_heading)
            ->assertSet('minimum_altitude', $hold->minimum_altitude)
            ->assertSet('maximum_altitude', $hold->maximum_altitude)
            ->assertSet('turn_direction', $hold->turn_direction)
            ->assertSet('restrictions.0.type', 'minimum-level')
            ->assertSet('restrictions.0.data.level', 'MSL')
            ->assertSet('restrictions.0.data.target', 'EGLL')
            ->assertSet('restrictions.0.data.override', 5000)
            ->assertSet('restrictions.0.data.runway.designator', '27L');
    }

    public function testItLoadsPublishedHoldDataWithMinimumLevelForViewWithMinimumData()
    {
        $navaid = Navaid::factory()->create();
        $hold = $navaid->holds()->save(
            Hold::factory()
                ->has(HoldRestriction::factory()->withMinimumLevelRestriction('MSL', 'EGLL'))->make()
        );

        Livewire::test(NavaidResource\RelationManagers\HoldsRelationManager::class, ['ownerRecord' => $navaid])
            ->callTableAction('view', $hold)
            ->assertSet('description', $hold->description)
            ->assertSet('inbound_heading', $hold->inbound_heading)
            ->assertSet('minimum_altitude', $hold->minimum_altitude)
            ->assertSet('maximum_altitude', $hold->maximum_altitude)
            ->assertSet('turn_direction', $hold->turn_direction)
            ->assertSet('restrictions.0.type', 'minimum-level')
            ->assertSet('restrictions.0.data.level', 'MSL')
            ->assertSet('restrictions.0.data.target', 'EGLL')
            ->assertSet('restrictions.0.data.override', null)
            ->assertSet('restrictions.0.data.runway.designator', null);
    }

    protected function getViewEditRecord(): Model
    {
        return Navaid::findOrFail(1);
    }

    protected function getResourceClass(): string
    {
        return NavaidResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit WILLO';
    }

    protected function getCreateText(): string
    {
        return 'Create Navaids and Holds';
    }

    protected function getViewText(): string
    {
        return 'View WILLO';
    }

    protected function getIndexText(): array
    {
        return ['WILLO', 'TIMBA', 'MAY'];
    }
}
