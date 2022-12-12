<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\IntentionCodeResource;
use App\Filament\Resources\IntentionCodeResource\Pages\CreateIntentionCode;
use App\Filament\Resources\IntentionCodeResource\Pages\EditIntentionCode;
use App\Filament\Resources\IntentionCodeResource\Pages\ListIntentionCodes;
use App\Filament\Resources\IntentionCodeResource\Pages\ViewIntentionCode;
use App\Models\IntentionCode\IntentionCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class IntentionCodeResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;
    use ChecksFilamentActionVisibility;

    public function testItLoadsAnIntentionCodeForViewWithAirfieldIdentifier()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'airfield_identifier',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL'],
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.code_type', 'airfield_identifier')
            ->assertSet('data.single_code', null);
    }

    public function testItLoadsAnIntentionCodeForViewWithSingleCode()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL'],
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.code_type', 'single_code')
            ->assertSet('data.single_code', 'A1');
    }

    public function testItLoadsArrivalAirfieldsConditionForView()
    {
        Str::createUuidsUsingSequence([0, 1, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL', 'EGKK'],
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'arrival_airfields')
            ->assertSet('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']]);
    }

    public function testItLoadsArrivalAirfieldPatternConditionForView()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfield_pattern',
                        'pattern' => 'EG',
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'arrival_airfield_pattern')
            ->assertSet('data.conditions.0.data.pattern', 'EG');
    }

    public function testItLoadsExitPointConditionForView()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'exit_point',
                        'exit_point' => 1,
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'exit_point')
            ->assertSet('data.conditions.0.data.exit_point', 1);
    }

    public function testItLoadsMaximumCruisingLevelConditionForView()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'maximum_cruising_level',
                        'level' => 15000,
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'maximum_cruising_level')
            ->assertSet('data.conditions.0.data.maximum_cruising_level', 15000);
    }

    public function testItLoadsCruisingLevelAboveConditionForView()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'cruising_level_above',
                        'level' => 15000,
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'cruising_level_above')
            ->assertSet('data.conditions.0.data.cruising_level_above', 15000);
    }

    public function testItLoadsRoutingViaConditionForView()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'routing_via',
                        'point' => 'TEST',
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'routing_via')
            ->assertSet('data.conditions.0.data.routing_via', 'TEST');
    }

    public function testItLoadsControllerPositionStartsWithConditionForView()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'controller_position_starts_with',
                        'starts_with' => 'EGP',
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItLoadsNotConditionForView()
    {
        Str::createUuidsUsingSequence([0, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'not',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'not')
            ->assertSet('data.conditions.0.data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItLoadsAnyOfConditionForView()
    {
        Str::createUuidsUsingSequence([0, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'any_of')
            ->assertSet('data.conditions.0.data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItLoadsAllOfConditionForView()
    {
        Str::createUuidsUsingSequence([0, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'all_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(ViewIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'all_of')
            ->assertSet('data.conditions.0.data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItCreatesAnAirfieldIdentifierCode()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals(['type' => 'airfield_identifier'], $code->code);
    }

    public function testItDoesntCreateCodeIfNoCodeTypeSelected()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasErrors(['data.code_type']);
    }

    public function testItCreatesASingleCodeCode()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'single_code')
            ->set('data.single_code', 'A1')
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals(['type' => 'single_code', 'code' => 'A1'], $code->code);
    }

    public function testItDoesntCreateSingleCodeIfNoCode()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'single_code')
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasErrors('data.single_code');
    }

    public function testItDoesntCreateSingleCodeIfSingleCodeTooLong()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'single_code')
            ->set('data.single_code', 'A12')
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasErrors('data.single_code');
    }

    public function testItCreatesACodeWithAnArrivalAirfieldsCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'arrival_airfields', 'airfields' => ['EGLL', 'EGKK']]], $code->conditions);
    }

    public function testItDoesntCreateWithAirfieldsConditionIfAirfieldInvalid()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL@@@'], ['airfield' => 'EGKK']])
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.airfields.0.airfield');
    }

    public function testItDoesntCreateWithAirfieldsConditionIfNoAirfields()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [])
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.airfields');
    }

    public function testItCreatesACodeWithAnArrivalAirfieldPatternCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfield_pattern')
            ->set('data.conditions.0.data.pattern', 'EGP')
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'arrival_airfield_pattern', 'pattern' => 'EGP']], $code->conditions);
    }

    public function testItDoesntCreateWithAirfieldPatternConditionIfPatternInvalid()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfield_pattern')
            ->set('data.conditions.0.data.pattern', 'X@@@@')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.pattern');
    }

    public function testItDoesntCreateWithAirfieldPatternConditionIfPatternMissing()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfield_pattern')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.pattern');
    }

    public function testItCreatesACodeWithAnExitPointCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'exit_point')
            ->set('data.conditions.0.data.exit_point', 1)
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'exit_point', 'exit_point' => 1]], $code->conditions);
    }

    public function testItDoesntCreateWithExitPointConditionIfNoExitPoint()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'exit_point')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.exit_point');
    }

    public function testItCreatesACodeWithAMaximumCruisingLevelCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->set('data.conditions.0.data.maximum_cruising_level', 5000)
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'maximum_cruising_level', 'level' => 5000]], $code->conditions);
    }

    public function testItDoesntCreateWithMaximumCruisingLevelConditionIfLevelMissing()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.maximum_cruising_level');
    }

    public function testItDoesntCreateWithMaximumCruisingLevelConditionIfLevelTooLow()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->set('data.conditions.0.data.maximum_cruising_level', 999)
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.maximum_cruising_level');
    }

    public function testItDoesntCreateWithMaximumCruisingLevelConditionIfLevelTooHigh()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->set('data.conditions.0.data.maximum_cruising_level', 999999999)
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.maximum_cruising_level');
    }

    public function testItCreatesACodeWithACruisingLevelAboveCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->set('data.conditions.0.data.cruising_level_above', 5000)
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'cruising_level_above', 'level' => 5000]], $code->conditions);
    }

    public function testItDoesntCreateWithCruisingLevelAboveConditionIfLevelMissing()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.cruising_level_above');
    }

    public function testItDoesntCreateWithCruisingLevelAboveConditionIfLevelTooLow()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->set('data.conditions.0.data.cruising_level_above', 999)
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.cruising_level_above');
    }

    public function testItDoesntCreateWithCruisingLevelAboveConditionIfLevelTooHigh()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->set('data.conditions.0.data.cruising_level_above', 999999999)
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.cruising_level_above');
    }


    public function testItCreatesACodeWithARoutingViaCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'routing_via')
            ->set('data.conditions.0.data.routing_via', 'TEST')
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'routing_via', 'point' => 'TEST']], $code->conditions);
    }

    public function testItDoesntCreateWithRoutingViaConditionIfNoVia()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'routing_via')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.routing_via');
    }

    public function testItDoesntCreateWithRoutingViaConditionIfViaTooLong()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'routing_via')
            ->set('data.conditions.0.data.routing_via', 'TESTTTT')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.routing_via');
    }

    public function testItCreatesACodeWithAControllerPositionStartsWithCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'controller_position_starts_with')
            ->set('data.conditions.0.data.controller_position_starts_with', 'TEST')
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'controller_position_starts_with', 'starts_with' => 'TEST']], $code->conditions);
    }


    public function testItDoesntCreateWithControllerPositionStartsWithConditionIfWithMissing()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'controller_position_starts_with')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.controller_position_starts_with');
    }

    public function testItDoesntCreateWithControllerPositionStartsWithConditionIfWithInvalid()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'controller_position_starts_with')
            ->set('data.conditions.0.data.controller_position_starts_with', 'EG@@')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.controller_position_starts_with');
    }

    public function testItCreatesACodeWithANotCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'not')
            ->set('data.conditions.0.data.conditions', [['type' => 'controller_position_starts_with', 'data' => ['controller_position_starts_with' => 'EGP']]])
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'not', 'conditions' => [['type' => 'controller_position_starts_with', 'starts_with' => 'EGP']]]], $code->conditions);
    }

    public function testItDoesntCreateWithANotConditionIfNoConditions()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'not')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.conditions');
    }

    public function testItCreatesACodeWithAnAnyOfCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'any_of')
            ->set('data.conditions.0.data.conditions', [['type' => 'controller_position_starts_with', 'data' => ['controller_position_starts_with' => 'EGP']]])
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'any_of', 'conditions' => [['type' => 'controller_position_starts_with', 'starts_with' => 'EGP']]]], $code->conditions);
    }

    public function testItDoesntCreateWithAnAnyOfConditionIfNoConditions()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'any_of')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.conditions');
    }

    public function testItCreatesACodeWithAnAllOfCondition()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'all_of')
            ->set('data.conditions.0.data.conditions', [['type' => 'controller_position_starts_with', 'data' => ['controller_position_starts_with' => 'EGP']]])
            ->call('create')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'all_of', 'conditions' => [['type' => 'controller_position_starts_with', 'starts_with' => 'EGP']]]], $code->conditions);
    }



    public function testItDoesntCreateWithAnAllOfConditionIfNoConditions()
    {
        Livewire::test(CreateIntentionCode::class)
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'all_of')
            ->call('create')
            ->assertHasErrors('data.conditions.0.data.conditions');
    }

    public function testItLoadsAnIntentionCodeForEditWithAirfieldIdentifier()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'airfield_identifier',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL'],
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.code_type', 'airfield_identifier')
            ->assertSet('data.single_code', null);
    }

    public function testItLoadsAnIntentionCodeForEditWithSingleCode()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL'],
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.code_type', 'single_code')
            ->assertSet('data.single_code', 'A1');
    }

    public function testItLoadsArrivalAirfieldsConditionForEdit()
    {
        Str::createUuidsUsingSequence([0, 1, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL', 'EGKK'],
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'arrival_airfields')
            ->assertSet('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']]);
    }

    public function testItLoadsArrivalAirfieldPatternConditionForEdit()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfield_pattern',
                        'pattern' => 'EG',
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'arrival_airfield_pattern')
            ->assertSet('data.conditions.0.data.pattern', 'EG');
    }

    public function testItLoadsExitPointConditionForEdit()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'exit_point',
                        'exit_point' => 1,
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'exit_point')
            ->assertSet('data.conditions.0.data.exit_point', 1);
    }

    public function testItLoadsMaximumCruisingLevelConditionForEdit()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'maximum_cruising_level',
                        'level' => 15000,
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'maximum_cruising_level')
            ->assertSet('data.conditions.0.data.maximum_cruising_level', 15000);
    }

    public function testItLoadsCruisingLevelAboveConditionForEdit()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'cruising_level_above',
                        'level' => 15000,
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'cruising_level_above')
            ->assertSet('data.conditions.0.data.cruising_level_above', 15000);
    }

    public function testItLoadsRoutingViaConditionForEdit()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'routing_via',
                        'point' => 'TEST',
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'routing_via')
            ->assertSet('data.conditions.0.data.routing_via', 'TEST');
    }

    public function testItLoadsControllerPositionStartsWithConditionForEdit()
    {
        Str::createUuidsUsingSequence([0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'controller_position_starts_with',
                        'starts_with' => 'EGP',
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItLoadsNotConditionForEdit()
    {
        Str::createUuidsUsingSequence([0, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'not',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'not')
            ->assertSet('data.conditions.0.data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItLoadsAnyOfConditionForEdit()
    {
        Str::createUuidsUsingSequence([0, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'any_of')
            ->assertSet('data.conditions.0.data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItLoadsAllOfConditionForEdit()
    {
        Str::createUuidsUsingSequence([0, 0]);
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'all_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->assertSet('data.conditions.0.type', 'all_of')
            ->assertSet('data.conditions.0.data.conditions.0.type', 'controller_position_starts_with')
            ->assertSet('data.conditions.0.data.conditions.0.data.controller_position_starts_with', 'EGP');
    }

    public function testItEditsAnAirfieldIdentifierCode()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals(['type' => 'airfield_identifier'], $code->code);
    }

    public function testItDoesntEditCodeIfNoCodeTypeSelected()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.single_code', null)
            ->set('data.code_type', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasErrors(['data.code_type']);
    }

    public function testItEditsASingleCodeCode()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'single_code')
            ->set('data.single_code', 'A1')
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals(['type' => 'single_code', 'code' => 'A1'], $code->code);
    }

    public function testItDoesntEditSingleCodeIfNoCode()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'single_code')
            ->set('data.single_code', null)
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasErrors('data.single_code');
    }

    public function testItDoesntEditSingleCodeIfSingleCodeTooLong()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'single_code')
            ->set('data.single_code', 'A12')
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasErrors('data.single_code');
    }

    public function testItEditsACodeWithAnArrivalAirfieldsCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();

        $this->assertEquals([['type' => 'arrival_airfields', 'airfields' => ['EGLL', 'EGKK']]], $code->conditions);
    }

    public function testItDoesntEditWithAirfieldsConditionIfAirfieldInvalid()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [['airfield' => 'EGLL@@@'], ['airfield' => 'EGKK']])
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.airfields.0.airfield');
    }

    public function testItDoesntEditWithAirfieldsConditionIfNoAirfields()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'arrival_airfields')
            ->set('data.conditions.0.data.airfields', [])
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.airfields');
    }

    public function testItEditsACodeWithAnArrivalAirfieldPatternCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'arrival_airfield_pattern')
            ->set('data.conditions.0.data.pattern', 'EGP')
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'arrival_airfield_pattern', 'pattern' => 'EGP']], $code->conditions);
    }

    public function testItDoesntEditWithAirfieldPatternConditionIfPatternInvalid()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'arrival_airfield_pattern')
            ->set('data.conditions.0.data.pattern', 'X@@@@')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.pattern');
    }

    public function testItDoesntEditWithAirfieldPatternConditionIfPatternMissing()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'arrival_airfield_pattern')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.pattern');
    }

    public function testItEditsACodeWithAnExitPointCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'exit_point')
            ->set('data.conditions.0.data.exit_point', 1)
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'exit_point', 'exit_point' => 1]], $code->conditions);
    }

    public function testItDoesntEditWithExitPointConditionIfNoExitPoint()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'exit_point')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.exit_point');
    }

    public function testItEditssACodeWithAMaximumCruisingLevelCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->set('data.conditions.0.data.maximum_cruising_level', 5000)
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'maximum_cruising_level', 'level' => 5000]], $code->conditions);
    }

    public function testItDoesntEditWithMaximumCruisingLevelConditionIfLevelMissing()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.maximum_cruising_level');
    }

    public function testItDoesntEditWithMaximumCruisingLevelConditionIfLevelTooLow()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->set('data.conditions.0.data.maximum_cruising_level', 999)
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.maximum_cruising_level');
    }

    public function testItDoesntEditWithMaximumCruisingLevelConditionIfLevelTooHigh()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'maximum_cruising_level')
            ->set('data.conditions.0.data.maximum_cruising_level', 999999999)
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.maximum_cruising_level');
    }

    public function testItEditsACodeWithACruisingLevelAboveCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->set('data.conditions.0.data.cruising_level_above', 5000)
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'cruising_level_above', 'level' => 5000]], $code->conditions);
    }

    public function testItDoesntEditWithCruisingLevelAboveConditionIfLevelMissing()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.cruising_level_above');
    }

    public function testItDoesntEditWithCruisingLevelAboveConditionIfLevelTooLow()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->set('data.conditions.0.data.cruising_level_above', 999)
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.cruising_level_above');
    }

    public function testItDoesntEditWithCruisingLevelAboveConditionIfLevelTooHigh()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'cruising_level_above')
            ->set('data.conditions.0.data.cruising_level_above', 999999999)
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.cruising_level_above');
    }


    public function testItEditsACodeWithARoutingViaCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'routing_via')
            ->set('data.conditions.0.data.routing_via', 'TEST')
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'routing_via', 'point' => 'TEST']], $code->conditions);
    }

    public function testItDoesntEditWithRoutingViaConditionIfNoVia()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'routing_via')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.routing_via');
    }

    public function testItDoesntEditWithRoutingViaConditionIfViaTooLong()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'routing_via')
            ->set('data.conditions.0.data.routing_via', 'TESTTTT')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.routing_via');
    }

    public function testItEditsACodeWithAControllerPositionStartsWithCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'controller_position_starts_with')
            ->set('data.conditions.0.data.controller_position_starts_with', 'TEST')
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'controller_position_starts_with', 'starts_with' => 'TEST']], $code->conditions);
    }


    public function testItDoesntEditWithControllerPositionStartsWithConditionIfWithMissing()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'controller_position_starts_with')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.controller_position_starts_with');
    }

    public function testItDoesntEditWithControllerPositionStartsWithConditionIfWithInvalid()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'controller_position_starts_with')
            ->set('data.conditions.0.data.controller_position_starts_with', 'EG@@')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.controller_position_starts_with');
    }

    public function testItEditsACodeWithANotCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'not')
            ->set('data.conditions.0.data.conditions', [['type' => 'controller_position_starts_with', 'data' => ['controller_position_starts_with' => 'EGP']]])
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'not', 'conditions' => [['type' => 'controller_position_starts_with', 'starts_with' => 'EGP']]]], $code->conditions);
    }

    public function testItDoesntEditWithANotConditionIfNoConditions()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'not')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.conditions');
    }

    public function testItEditACodeWithAnAnyOfCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'any_of')
            ->set('data.conditions.0.data.conditions', [['type' => 'controller_position_starts_with', 'data' => ['controller_position_starts_with' => 'EGP']]])
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'any_of', 'conditions' => [['type' => 'controller_position_starts_with', 'starts_with' => 'EGP']]]], $code->conditions);
    }

    public function testItDoesntEditWithAnAnyOfConditionIfNoConditions()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'any_of')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.conditions');
    }

    public function testItEditsACodeWithAnAllOfCondition()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'all_of')
            ->set('data.conditions.0.data.conditions', [['type' => 'controller_position_starts_with', 'data' => ['controller_position_starts_with' => 'EGP']]])
            ->call('save')
            ->assertHasNoErrors();

        $code = IntentionCode::latest()->firstOrFail();
        $this->assertEquals([['type' => 'all_of', 'conditions' => [['type' => 'controller_position_starts_with', 'starts_with' => 'EGP']]]], $code->conditions);
    }



    public function testItDoesntEditWithAnAllOfConditionIfNoConditions()
    {
        $code = IntentionCode::create(
            [
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'controller_position_starts_with',
                                'starts_with' => 'EGP',
                            ]
                        ]
                    ],
                ],
                'priority' => 99,
            ]
        );

        Livewire::test(EditIntentionCode::class, ['record' => $code->id])
            ->set('data.code_type', 'airfield_identifier')
            ->set('data.single_code', null)
            ->set('data.conditions', [])
            ->set('data.conditions.0.type', 'all_of')
            ->call('save')
            ->assertHasErrors('data.conditions.0.data.conditions');
    }

    protected function getResourceClass(): string
    {
        return IntentionCodeResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit Intention Code';
    }

    protected function getCreateText(): string
    {
        return 'Create Intention Code';
    }

    protected function getViewText(): string
    {
        return 'View Intention Code';
    }

    protected function getIndexText(): array
    {
        return ['Intention Codes', 'A1'];
    }

    protected function resourceId(): int|string
    {
        return 1;
    }

    protected function resourceRecordClass(): string
    {
        return IntentionCode::class;
    }

    protected function resourceListingClass(): string
    {
        return ListIntentionCodes::class;
    }

    protected function writeResourceTableActions(): array
    {
        return [
            'edit',
            'delete',
        ];
    }

    protected function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }

    protected function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected function getEditRecord(): Model
    {
        return IntentionCode::find(1);
    }

    protected function getViewRecord(): Model
    {
        return IntentionCode::find(1);
    }

    protected function resourceClass(): string
    {
        return IntentionCodeResource::class;
    }
}
