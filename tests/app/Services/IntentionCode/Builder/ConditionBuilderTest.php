<?php

namespace App\Services\IntentionCode\Builder;

use App\BaseUnitTestCase;
use App\Exceptions\IntentionCode\IntentionCodeInvalidException;

class ConditionBuilderTest extends BaseUnitTestCase
{
    private ConditionBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = ConditionBuilder::begin();
    }

    public function testItConvertsToArrayJustArrivalAirfields()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGKK', 'EGLL'],
            ]
        ];

        $this->builder->arrivalAirfields(['EGKK', 'EGLL']);
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItThrowsExceptionArrivalAirfieldNotValid()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Airfield EG is not valid for intention code');
        $this->builder->arrivalAirfields(['EGKK', 'EG']);
    }

    public function testItConvertsToArrayJustArrivalAirfieldPattern()
    {
        $expected = [
            [
                'type' => 'arrival_airfield_pattern',
                'pattern' => 'EG',
            ]
        ];

        $this->builder->arrivalAirfieldPattern('EG');
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItThrowsExceptionArrivalAirfieldPatternTooShort()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Invalid airfield pattern');
        $this->builder->arrivalAirfieldPattern('');
    }

    public function testItThrowsExceptionArrivalAirfieldPatternTooLong()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Invalid airfield pattern');
        $this->builder->arrivalAirfieldPattern('EGGGD');
    }

    public function testItConvertsToArrayJustExitPoint()
    {
        $expected = [
            [
                'type' => 'exit_point',
                'exit_point' => 'ETRAT',
                'exit_direction' => [
                    'start' => 55,
                    'end' => 180,
                ],
            ]
        ];

        $this->builder->exitPoint('ETRAT', 55, 180);
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItThrowsExceptionExitPointIdentifierTooShort()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Invalid exit point identifier');
        $this->builder->exitPoint('', 55, 180);
    }

    public function testItThrowsExceptionExitPointIdentifierTooLong()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Invalid exit point identifier');
        $this->builder->exitPoint('ETRAT2', 55, 180);
    }

    public function testItThrowsExceptionExitPointStartHeadingInvalid()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Invalid intention code headings');
        $this->builder->exitPoint('ETRAT', -1, 180);
    }

    public function testItThrowsExceptionExitPointEndHeadingInvalid()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Invalid intention code headings');
        $this->builder->exitPoint('ETRAT', 55, 361);
    }

    public function testItCombinesConditions()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGKK', 'EGLL'],
            ],
            [
                'type' => 'exit_point',
                'exit_point' => 'ETRAT',
                'exit_direction' => [
                    'start' => 55,
                    'end' => 180,
                ],
            ],
        ];

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->exitPoint('ETRAT', 55, 180);
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItCombinesConditionsInAnyOf()
    {
        $expected = [
            [
                'type' => 'any_of',
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGKK', 'EGLL'],
                    ],
                    [
                        'type' => 'exit_point',
                        'exit_point' => 'ETRAT',
                        'exit_direction' => [
                            'start' => 55,
                            'end' => 180,
                        ],
                    ],
                ],
            ],
        ];

        $this->builder->anyOf(function (ConditionBuilder $conditionBuilder) {
            $conditionBuilder->arrivalAirfields(['EGKK', 'EGLL'])
                ->exitPoint('ETRAT', 55, 180);
        });
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItCombinesConditionsInNot()
    {
        $expected = [
            [
                'type' => 'exit_point',
                'exit_point' => 'ETRAT',
                'exit_direction' => [
                    'start' => 55,
                    'end' => 180,
                ],
            ],
            [
                'type' => 'not',
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGKK', 'EGLL'],
                    ],
                    [
                        'type' => 'arrival_airfield_pattern',
                        'pattern' => 'EG',
                    ],
                ],
            ],
        ];

        $this->builder->exitPoint('ETRAT', 55, 180)
            ->not(function (ConditionBuilder $builder) {
                $builder->arrivalAirfields(['EGKK', 'EGLL'])
                    ->arrivalAirfieldPattern('EG');
            });
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItAddsRoutingViaCondition()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGKK', 'EGLL'],
            ],
            [
                'type' => 'routing_via',
                'point' => 'KOK',
            ],
        ];

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->routingVia('KOK');
        $this->assertEquals($expected, $this->builder->get());
    }

    public function itThrowsExceptionRoutingViaTooShort()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Routing via not valid');

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->routingVia('');
    }

    public function itThrowsExceptionRoutingViaTooLong()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Routing via not valid');

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->routingVia('ETRAT2');
    }

    public function testItAddsControllerPositionCondition()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGKK', 'EGLL'],
            ],
            [
                'type' => 'controller_position_starts_with',
                'starts_with' => 'EG',
            ],
        ];

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->controllerPositionStartWith('EG');
        $this->assertEquals($expected, $this->builder->get());
    }

    public function itThrowsExceptionControllerPositionEmpty()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Controller position starts with invalid');

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->controllerPositionStartWith('');
    }

    public function testItMaximumCruisingLevelCondition()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGKK', 'EGLL'],
            ],
            [
                'type' => 'maximum_cruising_level',
                'level' => 35000,
            ],
        ];

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->maximumCruisingLevel(35000);
        $this->assertEquals($expected, $this->builder->get());
    }

    public function itThrowsExceptionMaximumCruisingLevelInvalid()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Maximum cruising level not valid');

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->maximumCruisingLevel(0);
    }

    public function testItCruisingAboveLevelCondition()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGKK', 'EGLL'],
            ],
            [
                'type' => 'cruising_level_above',
                'level' => 35000,
            ],
        ];

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->cruisingAbove(35000);
        $this->assertEquals($expected, $this->builder->get());
    }

    public function itThrowsExceptionCruisingAboveLevelInvalid()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Cruising above level not valid');

        $this->builder->arrivalAirfields(['EGKK', 'EGLL'])
            ->cruisingAbove(0);
    }

    /**
     * @dataProvider badConditionProvider
     */
    public function itThrowsExceptionIfNoPrimaryConditionsSet(callable $setCondition)
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Conditions are not valid for intention code');

        $setCondition($this->builder);
        $this->builder->get();
    }

    private function badConditionProvider(): array
    {
        return [
            'Only routing via' => [
                function (ConditionBuilder $builder) {
                    $builder->routingVia('KOK');
                }
            ],
            'Only controller position' => [
                function (ConditionBuilder $builder) {
                    $builder->controllerPositionStartWith('EG');
                }
            ],
            'Only max cruise' => [
                function (ConditionBuilder $builder) {
                    $builder->maximumCruisingLevel(5000);
                }
            ],
            'Only cruising above' => [
                function (ConditionBuilder $builder) {
                    $builder->cruisingAbove(5000);
                }
            ],
            'Only arrival airfields inside negation' => [
                function (ConditionBuilder $builder) {
                    $builder->not(function (ConditionBuilder $builder) {
                        $builder->arrivalAirfields(['EGKK']);
                    });
                }
            ],
            'Only arrival airfield pattern inside negation' => [
                function (ConditionBuilder $builder) {
                    $builder->not(function (ConditionBuilder $builder) {
                        $builder->arrivalAirfieldPattern('EG');
                    });
                }
            ],
            'Only exit fix inside negation' => [
                function (ConditionBuilder $builder) {
                    $builder->not(function (ConditionBuilder $builder) {
                        $builder->exitPoint('ETRAT', 100, 200);
                    });
                }
            ],
            'Any of doesnt have primary guaranteed on all routes' => [
                function (ConditionBuilder $builder) {
                    $builder->anyOf(function (ConditionBuilder $builder) {
                        $builder->exitPoint('ETRAT', 100, 200)
                            ->routingVia('KOK');
                    });
                }
            ],
        ];
    }

    public function testItAllowsCombinedPrimaryConditionsIfAllRoutesValid()
    {
        $expected = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EGBB'],
            ],
            [
                'type' => 'any_of',
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGKK', 'EGLL'],
                    ],
                    [
                        'type' => 'exit_point',
                        'exit_point' => 'ETRAT',
                        'exit_direction' => [
                            'start' => 55,
                            'end' => 180,
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cruising_level_above',
                'level' => 27000,
            ],
            [
                'type' => 'not',
                'conditions' => [
                    [
                        'type' => 'any_of',
                        'conditions' => [
                            [
                                'type' => 'cruising_level_above',
                                'level' => 35000,
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $this->builder->arrivalAirfields(['EGBB'])
            ->anyOf(function (ConditionBuilder $builder) {
                $builder->arrivalAirfields(['EGKK', 'EGLL'])
                    ->exitPoint('ETRAT', 55, 180);
            })
            ->cruisingAbove(27000)
            ->not(function (ConditionBuilder $builder) {
                $builder->anyOf(function (ConditionBuilder $builder) {
                    $builder->cruisingAbove(35000);
                });
            });

        $this->assertEquals($expected, $this->builder->get());
    }
}
