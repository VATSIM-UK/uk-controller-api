<?php
namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

/**
 * Command to create a new admin user and generate their personal access token.
 *
 * Class UserAdminCreate
 * @package App\Console\Commands
 */
class UserAdminCreate extends Command
{
    protected $signature = 'user:create-admin';

    protected $description = 'Create a new admin user';

    /**
     * Handles the command
     * @param UserService $userService Service to do the user work.
     * @throws \App\Exceptions\UserAlreadyExistsException
     */
    public function handle(UserService $userService)
    {
        $token = $userService->createAdminUser();
        $this->info('New admin user succesfully created');
        $this->line($token);
    }
}
