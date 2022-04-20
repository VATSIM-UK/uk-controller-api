<?php

namespace App\Services\IntentionCode\Condition;

use App\Services\IntentionCode\Builder\ConditionBuilderInterface;

class Not implements ConditionInterface
{
    private ConditionBuilderInterface $conditionBuilder;

    public function __construct(ConditionBuilderInterface $conditionBuilder)
    {
        $this->conditionBuilder = $conditionBuilder;
    }

    public function toArray(): array
    {
        return [
            'type' => 'not',
            'conditions' => $this->conditionBuilder->get(),
        ];
    }
}
