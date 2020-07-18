<?php

namespace App\Models\Squawk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class AbstractSquawkRange extends Model
{
    private const SQUAWK_REGEX = '/^[0-7]{4}$/';

    /**
     * Return all the possible squawks in a given range
     *
     * @return array
     */
    public function getAllSquawksInRange(): Collection
    {
        if (preg_match(self::SQUAWK_REGEX, $this->first()) !== 1) {
            throw new InvalidArgumentException("Invalid first squawk of range: " . $this->first());
        }

        if (preg_match(self::SQUAWK_REGEX, $this->last()) !== 1) {
            throw new InvalidArgumentException("Invalid last squawk of range: " . $this->last());
        }

        // Iterate the squawks as decimal numbers, convert to octal and pad
        $allowedSquawks = new Collection();
        for ($current = octdec($this->first()); $current <= octdec($this->last()); $current++) {
            $allowedSquawks->add(str_pad((string) decoct($current), 4, '0', STR_PAD_LEFT));
        }

        return $allowedSquawks;
    }

    /**
     * Get the first squawk in the range.
     *
     * @return string
     */
    abstract public function first(): string;

    /**
     * Get the last sauawk in the range.
     *
     * @return string
     */
    abstract public function last(): string;
}
