<?php

namespace App\Rules\UnitDiscreteSquawkRange;

use Illuminate\Contracts\Validation\Rule;

class FlightRules implements Rule
{
    /**
     * @var string
     */
    private $flightRules;

    public function __construct(string $flightRules)
    {
        $this->flightRules = $flightRules;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return isset($value['rules']) && is_array($value) &&
            ($value['rules'] === $this->flightRules || $value['rules'] === $this->flightRules[0]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Flight rules do not match';
    }

    /**
     * @return string
     */
    public function getFlightRules(): string
    {
        return $this->flightRules;
    }
}
