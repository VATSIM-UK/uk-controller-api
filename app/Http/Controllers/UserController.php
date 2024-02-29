<?php

namespace App\Http\Controllers;

use App\Exceptions\TooManyTokensException;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User\Admin;
use App\Providers\AuthServiceProvider;
use App\Services\UserConfigService;
use App\Services\UserService;
use App\Services\UserTokenService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    /**
     * Service for administering users.
     *
     * @var UserService;
     */
    private $userService;

    /**
     * Creates user configs
     *
     * @var UserConfigService
     */
    private $userConfigService;


    /**
     * Administers users tokens
     *
     * @var UserTokenService
     */
    private $userTokenService;

    /**
     * Constructor
     *
     * @param UserService $userService
     */
    public function __construct(
        UserService $userService,
        UserConfigService $userConfigService,
        UserTokenService $userTokenService
    ) {
        $this->userService = $userService;
        $this->userConfigService = $userConfigService;
        $this->userTokenService = $userTokenService;
    }

    /**
     * Bans the given user
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function banUser(int $cid) : JsonResponse
    {
        try {
            $this->userService->banUser($cid);
            return response()->json()->setStatusCode(204);
        } catch (ModelNotFoundException $e) {
            Log::error('Attempted to ban user with CID ' . $cid . ' which does not exist.');
            return response()->json(
                [
                    'message' => $this->getNotExistsMessage($cid),
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Creates a user with the given CID
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function createUser(int $cid) : JsonResponse
    {
        try {
            return response()->json(
                $this->userService->createUser($cid)
            )->setStatusCode(201);
        } catch (UserAlreadyExistsException $e) {
            Log::error('Unable to create user with CID ' . $cid . ', already exists');
            return response()->json(
                [
                    'message' => 'User with CID ' . $cid . ' already exists',
                ]
            )->setStatusCode(422);
        }
    }

    /**
     * Creates a token for the given user
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function createUserToken(int $cid) : JsonResponse
    {
        try {
            return response()->json(
                $this->userConfigService->create($cid)
            )->setStatusCode(201);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'message' => $this->getNotExistsMessage($cid),
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Creates a token for the given user
     *
     * @param string $tokenId
     * @return JsonResponse
     */
    public function deleteUserToken(string $tokenId) : JsonResponse
    {
        try {
            return response()->json(
                $this->userTokenService->delete($tokenId)
            )->setStatusCode(204);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'message' => 'Token not found',
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Disables the given user account
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function disableUser(int $cid) : JsonResponse
    {
        try {
            $this->userService->banUser($cid);
            return response()->json()->setStatusCode(204);
        } catch (ModelNotFoundException $e) {
            Log::error('Attempted to disable user with CID ' . $cid . ' which does not exist.');
            return response()->json(
                [
                    'message' => $this->getNotExistsMessage($cid),
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Returns the user as JSON
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function getUser(int $cid) : JsonResponse
    {
        try {
            return response()->json($this->userService->getUser($cid));
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'message' => $this->getNotExistsMessage($cid),
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Reactivates the given user account
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function reactivateUser(int $cid) : JsonResponse
    {
        try {
            $this->userService->reactivateUser($cid);
            return response()->json()->setStatusCode(204);
        } catch (ModelNotFoundException $e) {
            Log::error('Unable to reactivate user with CID ' . $cid . ', does not exists');
            return response()->json(
                [
                    'message' => $this->getNotExistsMessage($cid),
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Get the unread notifications for the given user
     *
     * @param integer $cid
     * @return JsonResponse
     */
    public function getUnreadNotificationsForUser(int $cid)
    {
        try {
            return response()
                ->json($this->userService->getUnreadNotificationsForUser(
                    $cid,
                    request()->get('inactive', false)
                ));
        } catch (ModelNotFoundException $e) {
            Log::error('Unable to reactivate user with CID ' . $cid . ', does not exists');
            return response()->json(
                [
                    'message' => $this->getNotExistsMessage($cid),
                ]
            )->setStatusCode(404);
        }
    }

    /**
     * Marks a notification as read for the given user
     *
     * @param integer $cid
     * @param integer $notificationId
     * @return JsonResponse
     */
    public function markNotificationAsReadForUser(int $cid, int $notificationId)
    {
        try {
            $this->userService->markNotificationAsReadForUser($cid, $notificationId);
            return response()->json(['message' => 'ok']);
        } catch (ModelNotFoundException $e) {
            Log::error(
                'Unable to mark notification id ' . $notificationId
                . ' as read for CID ' . $cid
                . ', one does not exists'
            );
            return response()->json(
                [
                    'message' => 'Either the user or notification does not exist.',
                ]
            )->setStatusCode(404);
        }
    }

    private function getNotExistsMessage(int $userId): string
    {
        return sprintf('User with CID %d does not exist', $userId);
    }
}
