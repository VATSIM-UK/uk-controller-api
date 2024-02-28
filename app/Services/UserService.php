<?php

namespace App\Services;

use App\Exceptions\UserAlreadyExistsException;
use App\Helpers\User\UserConfig;
use App\Models\User\User;
use App\Models\User\UserStatus;
use App\Providers\AuthServiceProvider;

/**
 * Service for handling user administration.
 */
class UserService
{
    // The minimum possible VATSIM CID
    const MINIMUM_VATSIM_CID = 800000;

    /**
     * Service for creating User Configuration
     *
     * @var UserConfigService
     */
    private $userConfig;

    /**
     * Constructor
     *
     * @param UserConfigService $userConfig
     */
    public function __construct(UserConfigService $userConfig)
    {
        $this->userConfig = $userConfig;
    }

    /**
     * Sets the users account to active.
     *
     * @param integer $userCid
     * @throws ModelNotFoundException
     * @return void
     */
    public function reactivateUser(int $userCid)
    {
        User::findOrFail($userCid)->activate();
    }
    
    /**
     * Sets the users account to banned.
     *
     * @param integer $userCid
     * @throws ModelNotFoundException
     * @return void
     */
    public function banUser(int $userCid)
    {
        User::findOrFail($userCid)->ban();
    }

    /**
     * Sets the users account to disabled.
     *
     * @param integer $userCid
     * @throws ModelNotFoundException
     * @return void
     */
    public function disableUser(int $userCid)
    {
        User::findOrFail($userCid)->disable();
    }

    /**
     * Creates a user and generates their access tokens.
     *
     * @param int $userId The VATSIM CID of the user
     * @return UserConfig The users personal configuration for their plugin instance
     * @throws UserAlreadyExistsException
     */
    public function createUser(int $userCid) : UserConfig
    {
        if (User::find($userCid)) {
            throw new UserAlreadyExistsException('User with VATSIM CID ' . $userCid . ' already exists');
        }

        // Create the user
        $user = new User();
        $user->id = $userCid;
        $user->status = UserStatus::ACTIVE;
        $user->save();

        return $this->userConfig->create($userCid);
    }

    /**
     * Creates an admin user and returns the token
     *
     * @return string
     */
    public function createAdminUser() : string
    {
        return $this->createAdminUserModel()->createToken(
            'access',
            [
                AuthServiceProvider::SCOPE_USER_ADMIN,
                AuthServiceProvider::SCOPE_VERSION_ADMIN,
                AuthServiceProvider::SCOPE_USER
            ]
        )->accessToken;
    }

    /**
     * Creates a user with the scope of data administration,
     * returning the token of the user.
     *
     * @return string
     */
    public function createDataAdminUser() : string
    {
        return $this->createAdminUserModel()->createToken(
            'access',
            [
                AuthServiceProvider::SCOPE_DATA_ADMIN
            ]
        )->accessToken;
    }

    /**
     * Retrieves the user from the database
     *
     * @param int $userCid
     * @throws ModelNotFoundException
     * @return User
     */
    public function getUser(int $userCid) : User
    {
        return User::findOrFail($userCid);
    }

    /**
     * Create a user model as a 'pseudo' admin user.
     *
     * @return User
     */
    private function createAdminUserModel(): User
    {
        $admins = User::where('id', '<', self::MINIMUM_VATSIM_CID);
        $newUserCid = $admins->exists() ? $admins->max('id') + 1 : 0;

        $user = new User();
        $user->id = $newUserCid;
        $user->status = UserStatus::ACTIVE;
        $user->save();

        return $user;
    }
}
