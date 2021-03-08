<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class DataAdminCreate extends Command
{
    protected $signature = 'user:create-data-admin';

    protected $description = 'Command used to generate a user and return user with data admin scope';

    public function handle(UserService $userService)
    {
        $token = $userService->createDataAdminUser();
        $this->info("Data admin user created successfully");
        $this->line($token);
    }
}
