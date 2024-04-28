<?php

namespace App\Helpers\Vatsim;

/**
 * Class for validating VATSIM CIDs
 */
class VatsimCidValidator
{
    // The minimum possible CID
    const MINIMUM_CID = 800000;

    // The maximum realistic founder CID
    const MAXIMUM_FOUNDER_CID = 800150;

    // The minimum "normal" CID
    const MINIMUM_MEMBER_CID = 810000;

    /**
     * Validates VATSIM CIDs
     *
     * @param $cid The user CID
     * @return bool
     */
    public static function isValid(int $cid) : bool
    {
        return $cid >= self::MINIMUM_MEMBER_CID ||
            ($cid >= self::MINIMUM_CID && $cid <= self::MAXIMUM_FOUNDER_CID);
    }
}
