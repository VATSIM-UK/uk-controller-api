<?php

namespace App\Services\IntentionCode\Builder;

use App\Models\IntentionCode\IntentionCode;

class IntentionCodeBuilder
{
    private CodeBuilder $codeBuilder;
    private PriorityBuilder $priorityBuilder;
    private ConditionBuilder $conditionBuilder;
    private IntentionCode $code;

    private function __construct(IntentionCode $code)
    {
        $this->codeBuilder = new CodeBuilder($code);
        $this->priorityBuilder = new PriorityBuilder($code);
        $this->code = $code ?? new IntentionCode();
        $this->conditionBuilder = ConditionBuilder::begin($this->code);
    }

    public static function begin(): IntentionCodeBuilder
    {
        return self::from(new IntentionCode());
    }

    public static function from(IntentionCode $code): IntentionCodeBuilder
    {
        return new self($code);
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

    public function save(): IntentionCode
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
        return $this->code->fill(
            [
                'priority' => $this->priorityBuilder->get(),
                'code' => $this->codeBuilder->get(),
                'conditions' => $this->conditionBuilder->get(),
            ]
        );
    }
}
