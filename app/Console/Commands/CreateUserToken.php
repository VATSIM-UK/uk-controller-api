<?php
namespace App\Console\Commands;

use App\Models\User\User;
use App\Services\UserService;
use Illuminate\Console\Command;
use App\Services\UserTokenService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Command to generate the users access token
 *
 * Class CreateUserToken
 * @package App\Console\Commands
 */
class CreateUserToken extends Command
{
    protected $signature = 'token:create {vatsim_cid}';

    protected $description = 'Create an access token for the user';

    /**
     * Handles the command
     * @param UserService $userService Service to do the user work.
     * @throws \App\Exceptions\UserAlreadyExistsException
     */
    public function handle(UserTokenService $userTokenService)
    {
        // Invalid VATSIM CID
        if (!ctype_digit($this->argument('vatsim_cid'))) {
            $this->error('Invalid VATSIM CID');
            return 1;
        }

        try {
            $this->info($userTokenService->create($this->argument('vatsim_cid')));
            return 0;
        } catch (ModelNotFoundException $e) {
            // Nothing to catch
        }

        $this->error('User ' . $this->argument('vatsim_cid') . ' not found');
        return 2;
    }
}
