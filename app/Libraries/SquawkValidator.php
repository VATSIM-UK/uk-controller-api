<?php
namespace App\Libraries;

use App\Models\Squawks\Reserved;

/**
 * Class for validating that a given squawk code is valid.
 *
 * Class SquawkValidator
 * @package App\Libraries
 */
class SquawkValidator
{

    /**
     * Check if squawk is valid
     *
     * @param  string $squawk The squawk to check.
     * @return boolean
     */
    public static function isValidSquawk(string $squawk)
    {
        // Check if found in reserved
        $reserved = Reserved::where('squawk', $squawk)->first();
        if ($reserved) {
            return false;
        }

        // Check if it is octal
        foreach (str_split($squawk) as $digit) {
            if ($digit > 7) {
                return false;
            }
        }

        return true;
    }
}
