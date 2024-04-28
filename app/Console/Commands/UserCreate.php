<?php
namespace App\Console\Commands;

use App\Models\User\User;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Command to create a new user and generate their personal access token.
 *
 * Class UserCreate
 * @package App\Console\Commands
 */
class UserCreate extends Command
{
    const INVALID_CID_MESSAGE = 'Invalid VATSIM CID provided.';

    protected $signature = 'user:create {vatsim_cid}';

    protected $description = 'Create a user and generate a personal access token';

    /**
     * Handles the command
     * @param UserService $userService Service to do the user work.
     * @throws \App\Exceptions\UserAlreadyExistsException
     */
    public function handle(UserService $userService)
    {
        // Invalid VATSIM CID
        if (!ctype_digit($this->argument('vatsim_cid')) || $this->argument('vatsim_cid') < 800000) {
            throw new InvalidArgumentException(self::INVALID_CID_MESSAGE);
        }

        $userCid = $this->argument('vatsim_cid');
        $userConfig = json_encode(
            $userService->createUserWithConfig($userCid),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        Storage::disk('local')->put('access/api-settings-' . $userCid . '.txt', $userConfig);
        $this->info('User ' . $userCid . ' successfully created');
    }
}
