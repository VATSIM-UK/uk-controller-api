<?php

namespace App\Services\IntentionCode\Builder;

use App\BaseFunctionalTestCase;
use App\Models\IntentionCode\IntentionCode;

class IntentionCodeBuilderTest extends BaseFunctionalTestCase
{
    public function testItReturnsIntentionCode()
    {
        $code = IntentionCodeBuilder::begin()
            ->withPriority(5)
            ->withCode(function (CodeBuilder $builder) {
                $builder->singleCode('AM');
            })
            ->withCondition(function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->arrivalAirfields(['EHAM']);
            })->make();

        $this->assertDatabaseCount('intention_codes', 0);
        $this->assertEquals(5, $code->priority);
        $this->assertEquals(
            [
                'type' => 'single_code',
                'code' => 'AM',
            ],
            $code->code
        );
        $this->assertEquals(
            [
                [
                    'type' => 'arrival_airfields',
                    'airfields' => ['EHAM'],
                ]
            ],
            $code->conditions
        );
    }

    public function testItCreatesIntentionCode()
    {
        $code = IntentionCodeBuilder::begin()
            ->withPriority(5)
            ->withCode(function (CodeBuilder $builder) {
                $builder->singleCode('AM');
            })
            ->withCondition(function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->arrivalAirfields(['EHAM']);
            })->create();

        $expectedCode = [
            'code' => 'AM',
            'type' => 'single_code',
        ];
        $expectedCondition = [
            [
                'type' => 'arrival_airfields',
                'airfields' => ['EHAM'],
            ]
        ];

        $this->assertEquals(5, $code->priority);
        $this->assertEquals(
            $expectedCode,
            $code->code
        );
        $this->assertEquals(
            $expectedCondition,
            $code->conditions
        );

        $this->assertDatabaseCount('intention_codes', 1);
        $row = IntentionCode::query()->first();
        $this->assertEquals($code->priority, $row->priority);
        $this->assertEquals($code->code, $row->code);
        $this->assertEquals($code->conditions, $row->conditions);
    }
}
