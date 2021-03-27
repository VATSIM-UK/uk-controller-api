<?php

namespace App\Helpers\MinStack;

use Illuminate\Support\Collection;

/**
 * Interface MinStackCalculableInterface
 *
 * An interface that classes implement that indicate that they can
 * calculate a Min Stack Level
 */
interface MinStackCalculableInterface
{
    public function calculateMinStack(Collection $metars) : ?int;
}
