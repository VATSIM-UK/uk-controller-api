<?php

namespace App\Services;

use App\Helpers\User\UserConfig;
use App\Models\User\User;

/**
 * Class for creating configuration files that users
 * can use to run the plugin.
 */
class UserConfigService implements UserConfigCreatorInterface
{
    /**
     * Manages user tokens
     *
     * @var UserTokenService
     */
    private $tokenService;

    /**
     * Constructor
     */
    public function __construct(UserTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Creates a new user configuration
     *
     * @param  int  $cid
     *
     * @throws ModelNotFoundException
     */
    public function create(int $userCid): UserConfig
    {
        return new UserConfig(
            $this->tokenService->create($userCid)
        );
    }
}
