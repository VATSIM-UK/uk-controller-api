<?php

namespace App\Services\IntentionCode\Condition;

use App\BaseUnitTestCase;

class ConditionTest extends BaseUnitTestCase
{
    private Condition $condition;

    public function setUp(): void
    {
        parent::setUp();
        $this->condition = new Condition(['foo']);
    }

    public function testItReturnsArray()
    {
        $this->assertEquals(['foo'], $this->condition->toArray());
    }
}
