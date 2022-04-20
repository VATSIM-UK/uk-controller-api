<?php

namespace App\Services\IntentionCode\Builder;

interface ConditionBuilderInterface
{
    /**
     * Returns the conditions as an array
     */
    public function get(): array;
}
