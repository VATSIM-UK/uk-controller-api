<?php

namespace App\Listeners\Database;

use App\Services\DatabaseService;

class MigrationsFinished
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function handle()
    {
        $this->databaseService->updateTableStatus();
    }
}
