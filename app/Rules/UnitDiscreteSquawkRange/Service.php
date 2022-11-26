<?php

namespace App\Rules\UnitDiscreteSquawkRange;

use Illuminate\Contracts\Validation\Rule;

class Service implements Rule
{
    private readonly string $service;

    public function __construct(string $flightRules)
    {
        $this->service = $flightRules;
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
        return isset($value['service']) && is_array($value) &&
            ($value['service'] === $this->service);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Services do not match';
    }

    public function getService(): string
    {
        return $this->service;
    }
}
