<?php

namespace App\Services\IntentionCode\Condition;

interface ConditionInterface
{
    /**
     * Returns the condition in array form;
     */
    public function toArray(): array;
}
