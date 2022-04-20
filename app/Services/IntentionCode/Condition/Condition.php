<?php

namespace App\Services\IntentionCode\Condition;

class Condition implements ConditionInterface
{
    private array $condition;

    public function __construct(array $condition)
    {
        $this->condition = $condition;
    }

    public function toArray(): array
    {
        return $this->condition;
    }
}
