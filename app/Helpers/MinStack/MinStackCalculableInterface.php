<?php

namespace App\Helpers\MinStack;

/**
 * Interface MinStackCalculableInterface
 *
 * An interface that classes implement that indicate that they can
 * calculate a Min Stack Level
 */
interface MinStackCalculableInterface
{
    /**
     * Return the minimum stack level
     *
     * @return int|null
     */
    public function calculateMinStack() : ?int;
}
