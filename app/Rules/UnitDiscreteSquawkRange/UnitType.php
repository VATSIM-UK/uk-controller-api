<?php

namespace App\Rules\UnitDiscreteSquawkRange;

use Illuminate\Contracts\Validation\Rule;

class UnitType implements Rule
{
    /**
     * @var string
     */
    private $unitType;

    public function __construct(string $unitType)
    {
        $this->unitType = $unitType;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_array($value) && isset($value['unit_type']) &&
            ($value['unit_type'] === '' || $value['unit_type'] === $this->unitType);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Unit type does not match';
    }

    /**
     * @return string
     */
    public function getUnitType(): string
    {
        return $this->unitType;
    }
}
