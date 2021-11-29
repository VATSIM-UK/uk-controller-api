<?php

namespace App\Listeners\Dependency;

use App\Events\Database\DatabaseTablesUpdated;
use App\Services\DependencyService;

class UpdateDependencies
{
    private DependencyService $dependencyService;

    public function __construct(DependencyService $dependencyService)
    {
        $this->dependencyService = $dependencyService;
    }

    public function handle(DatabaseTablesUpdated $databaseTablesUpdatedEvent)
    {
        $this->dependencyService->updateDependenciesFromDatabaseTables($databaseTablesUpdatedEvent->getTables());
    }
}
