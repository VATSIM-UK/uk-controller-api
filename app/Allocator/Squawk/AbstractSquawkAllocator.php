<?php

namespace App\Allocator\Squawk;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

abstract class AbstractSquawkAllocator
{
    /**
     * Assign a squawk code from those provided. If a clash is detected,
     * try the next one in sequence.
     *
     * @param Closure $assign A function that will create and return the squawk assignment
     * @param Collection $potentialCodes All the potential codes we've identified
     * @return SquawkAssignmentInterface|null
     */
    public static function assignSquawk(Closure $assign, Collection $potentialCodes): ?SquawkAssignmentInterface
    {
        foreach ($potentialCodes as $code) {
            try {
                return $assign($code);
            } catch (QueryException $queryException) {
                if ($queryException->errorInfo[1] !== 1062) {
                    throw $queryException;
                }
            }
        }

        return null;
    }
}
