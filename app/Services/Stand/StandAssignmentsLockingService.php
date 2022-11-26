<?php

namespace App\Services\Stand;

use Illuminate\Support\Facades\Cache;

class StandAssignmentsLockingService
{
    private const STAND_ASSIGNMENTS_CACHE_LOCK = 'STAND_ASSIGNMENTS_LOCK';

    public static function performActionWithLock(callable $action): void
    {
        Cache::lock(self::STAND_ASSIGNMENTS_CACHE_LOCK)
            ->block(7, $action);
    }
}
