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
    }

    public function testItReturnsIntentionCodeDependency()
    {
        $expected = IntentionCode::factory()->count(3)->create()->sortBy('priority')
            ->values()
            ->toArray();

        $this->assertEquals($expected, $this->service->getIntentionCodesDependency());
    }
}
