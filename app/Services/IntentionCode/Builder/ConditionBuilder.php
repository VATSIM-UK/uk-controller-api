<?php

namespace App\Services\IntentionCode\Builder;

use App\Exceptions\IntentionCode\IntentionCodeInvalidException;
use App\Models\IntentionCode\IntentionCode;
use App\Rules\Airfield\AirfieldIcao;
use App\Rules\Heading\ValidHeading;
use App\Services\IntentionCode\Condition\AnyOf;
use App\Services\IntentionCode\Condition\Condition;
use App\Services\IntentionCode\Condition\ConditionInterface;
use App\Services\IntentionCode\Condition\Not;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ConditionBuilder implements ConditionBuilderInterface
{
    private Collection $conditions;
    private bool $requiresKeyCondition;

    private function __construct(bool $requiresKeyCondition, ?IntentionCode $code)
    {
        $this->conditions = collect(
            $code && $code->conditions
                ? array_map(fn(array $condition) => $this->makeCondition($condition), $code->conditions)
                : []
        );
        $this->requiresKeyCondition = $requiresKeyCondition;
    }

    public static function begin(IntentionCode $code): ConditionBuilder
    {
        return new self(true, $code);
    }

    public function anyOf(callable $callback): ConditionBuilder
    {
        $this->conditions->add(
            new AnyOf(
                tap(
                    new self(false, null),
                    function (ConditionBuilder $conditionBuilder) use ($callback) {
                        $callback($conditionBuilder);
                    }
                )
            )
        );

        return $this;
    }

    public function not(callable $callback): ConditionBuilder
    {
        $this->conditions->add(
            new Not(
                tap(
                    new self(false, null),
                    function (ConditionBuilder $conditionBuilder) use ($callback) {
                        $callback($conditionBuilder);
                    }
                )
            )
        );

        return $this;
    }

    public function routingVia(string $point): ConditionBuilder
    {
        if (!$this->pointValid($point)) {
            throw new IntentionCodeInvalidException('Routing via not valid');
        }

        return $this->addCondition([
            'type' => 'routing_via',
            'point' => $point,
        ]);
    }

    public function controllerPositionStartWith(string $startsWith): ConditionBuilder
    {
        if (Str::length($startsWith) === 0) {
            throw new IntentionCodeInvalidException('Controller position starts with invalid');
        }

        return $this->addCondition([
            'type' => 'controller_position_starts_with',
            'starts_with' => $startsWith,
        ]);
    }

    public function maximumCruisingLevel(int $level): ConditionBuilder
    {
        if (!$this->cruisingLevelValid($level)) {
            throw new IntentionCodeInvalidException('Maximum cruising level not valid');
        }

        return $this->addCondition([
            'type' => 'maximum_cruising_level',
            'level' => $level,
        ]);
    }

    public function cruisingAbove(int $level): ConditionBuilder
    {
        if (!$this->cruisingLevelValid($level)) {
            throw new IntentionCodeInvalidException('Cruising above level not valid');
        }

        return $this->addCondition([
            'type' => 'cruising_level_above',
            'level' => $level,
        ]);
    }

    public function arrivalAirfields(array $airfields): ConditionBuilder
    {
        $validator = new AirfieldIcao();
        foreach ($airfields as $airfield) {
            if (!$validator->passes('', $airfield)) {
                throw new IntentionCodeInvalidException(
                    sprintf('Airfield %s is not valid for intention code', $airfield)
                );
            }
        }

        return $this->addCondition([
            'type' => 'arrival_airfields',
            'airfields' => $airfields,
        ]);
    }

    public function arrivalAirfieldPattern(string $pattern): ConditionBuilder
    {
        if (Str::length($pattern) < 1 || Str::length($pattern) > 4) {
            throw new IntentionCodeInvalidException('Invalid airfield pattern');
        }

        return $this->addCondition([
            'type' => 'arrival_airfield_pattern',
            'pattern' => $pattern,
        ]);
    }

    public function exitPoint(string $exitPoint, int $headingStart, int $headingEnd): ConditionBuilder
    {
        if (!$this->pointValid($exitPoint)) {
            throw new IntentionCodeInvalidException('Invalid exit point identifier');
        }

        $headingValidator = new ValidHeading();

        if (
            !$headingValidator->passes('', $headingStart) ||
            !$headingValidator->passes('', $headingEnd)) {
            throw new IntentionCodeInvalidException('Invalid intention code headings');
        }


        return $this->addCondition([
            'type' => 'exit_point',
            'exit_point' => $exitPoint,
            'exit_direction' => [
                'start' => $headingStart,
                'end' => $headingEnd,
            ],
        ]);
    }

    private function makeCondition(array $condition): Condition
    {
        return new Condition($condition);
    }

    private function addCondition(array $condition): ConditionBuilder
    {
        $this->conditions->add($this->makeCondition($condition));

        return $this;
    }

    public function get(): array
    {
        if (!$this->conditionsValid()) {
            throw new IntentionCodeInvalidException('Conditions are not valid for intention code');
        }

        return $this->conditions->map(fn(ConditionInterface $condition) => $condition->toArray())->toArray();
    }

    /**
     * Make sure we always have a key condition to work on.
     */
    private function conditionsValid(): bool
    {
        if (!$this->requiresKeyCondition) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if ($this->checkCondition($condition)) {
                return true;
            }
        }

        return false;
    }

    private function checkCondition(ConditionInterface $condition): bool
    {
        $conditionData = $condition->toArray();
        if ($this->conditionIsKeyCondition($conditionData['type'])) {
            return true;
        }

        if ($conditionData['type'] === 'any_of') {
            return array_reduce(
                $conditionData['conditions'],
                fn($carry, $nextCondition) => $carry && $this->conditionIsKeyCondition($nextCondition['type']),
                true
            );
        }

        return false;
    }

    private function conditionIsKeyCondition(string $condition): bool
    {
        return in_array(
            $condition,
            ['exit_point', 'arrival_airfields', 'arrival_airfield_pattern']
        );
    }

    private function pointValid(string $point): bool
    {
        return Str::length($point) > 0 && Str::length($point) < 6;
    }

    private function cruisingLevelValid(int $level): bool
    {
        return $level > 0;
    }
}
