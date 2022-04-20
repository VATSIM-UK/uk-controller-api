<?php

namespace App\Services\IntentionCode\Builder;

use App\Models\IntentionCode\IntentionCode;

class IntentionCodeBuilder
{
    private CodeBuilder $codeBuilder;
    private PriorityBuilder $priorityBuilder;
    private ConditionBuilder $conditionBuilder;

    private function __construct()
    {
        $this->codeBuilder = new CodeBuilder();
        $this->priorityBuilder = new PriorityBuilder();
        $this->conditionBuilder = ConditionBuilder::begin();
    }

    public static function begin(): IntentionCodeBuilder
    {
        return new self();
    }

    public function withPriority(int $priority): IntentionCodeBuilder
    {
        $this->priorityBuilder->withPriority($priority);

        return $this;
    }

    public function withCode(callable $codeCallback): IntentionCodeBuilder
    {
        $codeCallback($this->codeBuilder);

        return $this;
    }

    public function withCondition(callable $conditionCallback): IntentionCodeBuilder
    {
        $conditionCallback($this->conditionBuilder);

        return $this;
    }

    public function create(): IntentionCode
    {
        return tap(
            $this->make(),
            function (IntentionCode $intentionCode) {
                $intentionCode->save();
            }
        );
    }

    public function make(): IntentionCode
    {
        return new IntentionCode(
            [
                'priority' => $this->priorityBuilder->get(),
                'code' => $this->codeBuilder->get(),
                'conditions' => $this->conditionBuilder->get(),
            ]
        );
    }
}
