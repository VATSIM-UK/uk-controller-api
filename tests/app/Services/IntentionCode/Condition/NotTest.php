<?php

namespace App\Services\IntentionCode\Condition;

use App\BaseUnitTestCase;
use App\Services\IntentionCode\Builder\ConditionBuilder;

class NotTest extends BaseUnitTestCase
{
    private Not $not;

    public function setUp(): void
    {
        parent::setUp();

        $this->not = new Not(
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
            'type' => 'not',
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

        $this->assertEquals($expected, $this->not->toArray());
    }
}
