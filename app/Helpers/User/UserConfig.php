<?php

namespace App\Helpers\User;

use JsonSerializable;

/**
 * Represents an individual users configuration file
 * for using the API with the plugin.
 */
class UserConfig implements JsonSerializable
{
    /**
     * The users access token
     *
     * @var string
     */
    private $accessToken;

    /**
     * Constructor
     *
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Returns the api key associated with this config
     *
     * @return string
     */
    public function apiKey() : string
    {
        return $this->accessToken;
    }

    /**
     * Returns the API url for the config
     *
     * @return string
     */
    public function apiUrl() : string
    {
        return config('app.url');
    }

    /**
     * Returns the user configuration as an array
     * so that it can be json_encoded.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return  [
            'api-url' => config('app.url'),
            'api-key' => $this->accessToken,
        ];
    }
}
