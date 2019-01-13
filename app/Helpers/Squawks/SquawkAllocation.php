<?php

namespace App\Helpers\Squawks;

/**
 * Represents a squawk allocation, provides properties
 * to get the squawk but also to say whether or not a squawk
 * was previously allocated.
 */
class SquawkAllocation
{

    /**
     * The squawk code
     *
     * @var string
     */
    protected $squawk;

    /**
     * Whether or not the squawk allocation was created on this request.
     *
     * @var bool
     */
    protected $newAllocation;

    /**
     * Constructor
     *
     * @param string $squawk
     * @param boolean $newAllocation
     */
    public function __construct(string $squawk, bool $newAllocation)
    {
        $this->squawk = $squawk;
        $this->newAllocation = $newAllocation;
    }

    /**
     * Get whether or not the squawk allocation was created on this request.
     *
     * @return bool
     */
    public function isNewAllocation() : bool
    {
        return $this->newAllocation;
    }

    /**
     * Get the squawk code
     *
     * @return string
     */
    public function squawk() : string
    {
        return $this->squawk;
    }
}
