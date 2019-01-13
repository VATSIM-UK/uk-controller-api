<?php

namespace App\Helpers\User;

use JsonSerializable;

class UserConfigCollection implements JsonSerializable
{
    /**
     * Array of UserConfig objects
     *
     * @var array
     */
    private $userConfigs = [];

    /**
     * Constuctor.
     *
     * @param array $userConfigs
     */
    public function __construct(UserConfig ...$userConfigs)
    {
        $this->userConfigs = $userConfigs;
    }

    /**
     * Serializes the collection to something that
     * can be used by json_encode
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->userConfigs;
    }
}
