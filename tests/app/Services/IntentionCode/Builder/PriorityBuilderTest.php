<?php

namespace App\Services\IntentionCode\Builder;

use App\BaseUnitTestCase;
use App\Exceptions\IntentionCode\IntentionCodeInvalidException;

class PriorityBuilderTest extends BaseUnitTestCase
{
    private PriorityBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new PriorityBuilder();
    }

    public function testItConvertsToArray()
    {
        $this->builder->withPriority(55);
        $this->assertEquals(55, $this->builder->get());
    }

    public function testItThrowsExceptionIfPrioritySetTwice()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Priority already set for intention code');
        $this->builder->withPriority(55);
        $this->builder->withPriority(56);
    }
    public function testItThrowsExceptionIfPriorityInvalid()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Intention code priority must be greater than 0');
        $this->builder->withPriority(-1);
    }

    public function testItThrowsExceptionIfPriorityNotSet()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Intention code priority not set');
        $this->builder->get();
    }
}
