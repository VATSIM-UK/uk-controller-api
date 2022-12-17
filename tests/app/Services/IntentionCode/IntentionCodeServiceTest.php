<?php

namespace App\Services\IntentionCode;

use App\BaseFunctionalTestCase;
use App\Models\IntentionCode\IntentionCode;

class IntentionCodeServiceTest extends BaseFunctionalTestCase
{
    private IntentionCodeService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(IntentionCodeService::class);
        IntentionCode::all()->each(fn(IntentionCode $code) => $code->delete());
    }

    public function testItReturnsIntentionCodeDependency()
    {
        $expected = IntentionCode::factory()->count(3)->create()->sortBy('priority')
            ->values()
            ->toArray();

        $this->assertEquals($expected, $this->service->getIntentionCodesDependency());
    }

    public function testItSavesInMiddleOfSequence()
    {
        $code1 = IntentionCode::factory()->create(['priority' => 1]);
        $code2 = IntentionCode::factory()->create(['priority' => 2]);
        $code3 = IntentionCode::factory()->create(['priority' => 3]);
        $code4 = IntentionCode::factory()->create(['priority' => 4]);

        $newCode = IntentionCode::factory()->make(['priority' => 2]);
        IntentionCodeService::saveIntentionCode($newCode);

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code1->id,
                'priority' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code2->id,
                'priority' => 3,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code3->id,
                'priority' => 4,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code4->id,
                'priority' => 5,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $newCode->id,
                'priority' => 2,
            ]
        );
    }

    public function testItSavesFromStartOfSequence()
    {
        $code1 = IntentionCode::factory()->create(['priority' => 1]);
        $code2 = IntentionCode::factory()->create(['priority' => 2]);
        $code3 = IntentionCode::factory()->create(['priority' => 3]);
        $code4 = IntentionCode::factory()->create(['priority' => 4]);

        $newCode = IntentionCode::factory()->make(['priority' => 1]);
        IntentionCodeService::saveIntentionCode($newCode);

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code1->id,
                'priority' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code2->id,
                'priority' => 3,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code3->id,
                'priority' => 4,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code4->id,
                'priority' => 5,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $newCode->id,
                'priority' => 1,
            ]
        );
    }

    public function testItSavesFromEndOfSequence()
    {
        $code1 = IntentionCode::factory()->create(['priority' => 1]);
        $code2 = IntentionCode::factory()->create(['priority' => 2]);
        $code3 = IntentionCode::factory()->create(['priority' => 3]);
        $code4 = IntentionCode::factory()->create(['priority' => 4]);

        $newCode = IntentionCode::factory()->make(['priority' => 5]);
        IntentionCodeService::saveIntentionCode($newCode);

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code1->id,
                'priority' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code2->id,
                'priority' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code3->id,
                'priority' => 3,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code4->id,
                'priority' => 4,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $newCode->id,
                'priority' => 5,
            ]
        );
    }

    public function testItHandlesMovingAnExistingDownThePriority()
    {
        $code1 = IntentionCode::factory()->create(['priority' => 1]);
        $code2 = IntentionCode::factory()->create(['priority' => 2]);
        $code3 = IntentionCode::factory()->create(['priority' => 3]);
        $code4 = IntentionCode::factory()->create(['priority' => 4]);
        $code5 = IntentionCode::factory()->create(['priority' => 5]);
        $code6 = IntentionCode::factory()->create(['priority' => 6]);
        $code7 = IntentionCode::factory()->create(['priority' => 7]);

        $code3->priority = 5;
        IntentionCodeService::saveIntentionCode($code3, 3);
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code1->id,
                'priority' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code2->id,
                'priority' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code4->id,
                'priority' => 3,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code5->id,
                'priority' => 4,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code3->id,
                'priority' => 5,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code6->id,
                'priority' => 6,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code7->id,
                'priority' => 7,
            ]
        );
    }

    public function testItHandlesMovingAnExistingUpThePriority()
    {
        $code1 = IntentionCode::factory()->create(['priority' => 1]);
        $code2 = IntentionCode::factory()->create(['priority' => 2]);
        $code3 = IntentionCode::factory()->create(['priority' => 3]);
        $code4 = IntentionCode::factory()->create(['priority' => 4]);
        $code5 = IntentionCode::factory()->create(['priority' => 5]);
        $code6 = IntentionCode::factory()->create(['priority' => 6]);
        $code7 = IntentionCode::factory()->create(['priority' => 7]);

        $code5->priority = 3;
        IntentionCodeService::saveIntentionCode($code5, 5);
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code1->id,
                'priority' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code2->id,
                'priority' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code5->id,
                'priority' => 3,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code3->id,
                'priority' => 4,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code4->id,
                'priority' => 5,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code6->id,
                'priority' => 6,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code7->id,
                'priority' => 7,
            ]
        );
    }

    public function testItHandlesSavingInPlace()
    {
        $code1 = IntentionCode::factory()->create(['priority' => 1]);
        $code2 = IntentionCode::factory()->create(['priority' => 2]);
        $code3 = IntentionCode::factory()->create(['priority' => 3]);
        $code4 = IntentionCode::factory()->create(['priority' => 4]);
        $code5 = IntentionCode::factory()->create(['priority' => 5]);
        $code6 = IntentionCode::factory()->create(['priority' => 6]);
        $code7 = IntentionCode::factory()->create(['priority' => 7]);

        IntentionCodeService::saveIntentionCode($code5, 5);
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code1->id,
                'priority' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code2->id,
                'priority' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code3->id,
                'priority' => 3,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code4->id,
                'priority' => 4,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code5->id,
                'priority' => 5,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code6->id,
                'priority' => 6,
            ]
        );
        $this->assertDatabaseHas(
            'intention_codes',
            [
                'id' => $code7->id,
                'priority' => 7,
            ]
        );
    }
}
