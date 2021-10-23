<?php

namespace App\Rules\Coordinates;

use Illuminate\Contracts\Validation\Rule;

abstract class Coordinate implements Rule
{
    public function passes($attribute, $value): bool
    {
        return is_numeric($value) && $this->validFloat((float)$value);
    }

    private function validFloat(float $value): bool
    {
        return abs($value) <= $this->maximumAllowedValue();
    }

    /**
     * Returns the maximum allowed value
     */
    abstract protected function maximumAllowedValue(): float;

    public function message(): string
    {
        return sprintf('Invalid coordinate %s', $this->getTypeForMessage());
    }

    /**
     * Gets the type to display in the message
     */
    public abstract function getTypeForMessage(): string;
}
