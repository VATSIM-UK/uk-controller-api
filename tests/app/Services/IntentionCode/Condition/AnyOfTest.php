<?php

namespace App\Services\IntentionCode\Condition;

use App\BaseUnitTestCase;
use App\Services\IntentionCode\Builder\ConditionBuilder;

class AnyOfTest extends BaseUnitTestCase
{
    private AnyOf $anyOf;

    public function setUp(): void
    {
        parent::setUp();

        $this->anyOf = new AnyOf(
            tap(
                ConditionBuilder::begin(),
                function (ConditionBuilder $conditionBuilder) {
                    $conditionBuilder->maximumCruisingLevel(35000);
                    $conditionBuilder->arrivalAirfields(['EGKK']);
                }
            )
        );
    }

    public function testItReturnsArray()
    {
        $expected = [
            'type' => 'any_of',
            'conditions' => [
                [
                    'type' => 'maximum_cruising_level',
                    'level' => 35000,
                ],
                [
                    'type' => 'arrival_airfields',
                    'airfields' => ['EGKK'],
                ]
            ]
        ];

        $this->assertEquals($expected, $this->anyOf->toArray());
    }
}
