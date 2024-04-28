<?php

namespace App\Services;

use App\Exceptions\UserAlreadyExistsException;
use App\Helpers\User\UserConfig;
use App\Models\Notification\Notification;
use App\Models\User\User;
use App\Models\User\UserStatus;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Eloquent\Collection;

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
    public function createUser(int $userCid) : void
    {
        if (User::find($userCid)) {
            throw new UserAlreadyExistsException('User with VATSIM CID ' . $userCid . ' already exists');
        }

        // Create the user
        $user = new User();
        $user->id = $userCid;
        $user->status = UserStatus::ACTIVE;
        $user->save();
    }

    /**
     * Creates a user and generates their access tokens.
     *
     * @param int $userId The VATSIM CID of the user
     * @return UserConfig The users personal configuration for their plugin instance
     * @throws UserAlreadyExistsException
     */
    public function createUserWithConfig(int $userCid)
    {
        $this->createUser($userCid);
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
     * Retrieves the user's unread notifications from the database. Creates the user if they do not exist.
     *
     * @param int $userCid
     * @return Collection
     */
    public function getUnreadNotificationsForUser(int $userCid, bool $includeInactive = false)
    {
        $query = Notification::orderBy('valid_from', 'desc')
            ->with('controllers')
            ->unreadBy($this->firstOrCreateUser($userCid));

        if (!$includeInactive) {
            $query->active();
        }

        return $query->get()
            ->map(fn (Notification $notification) => array_merge(
                $notification->toArray(),
                [
                    'valid_from' => $notification->valid_from->toDateTimeString(),
                    'valid_to' => $notification->valid_to->toDateTimeString(),
                ]
            ));
    }

    /**
     * Marks a notification as read for the given user
     *
     * @param int $userCid
     * @throws ModelNotFoundException
     * @return void
     */
    public function markNotificationAsReadForUser(int $userCid, int $notificationId)
    {
        Notification::findOrFail($notificationId)
            ->readBy()
            ->attach(User::findOrFail($userCid));
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

    private function firstOrCreateUser(int $userCid): User
    {
        return User::firstOrCreate(['id' => $userCid], ['status' => UserStatus::ACTIVE]);
    }
}
