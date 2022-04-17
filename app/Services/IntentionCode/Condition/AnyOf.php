<?php

namespace App\Services\IntentionCode\Condition;

use App\Services\IntentionCode\Builder\ConditionBuilderInterface;

class AnyOf implements ConditionInterface
{
    private ConditionBuilderInterface $conditions;

    public function __construct(ConditionBuilderInterface $conditions)
    {
        $this->conditions = $conditions;
    }

    public function toArray(): array
    {
        return [
            'type' => 'any_of',
            'conditions' => $this->conditions->get(),
        ];
    }
}
