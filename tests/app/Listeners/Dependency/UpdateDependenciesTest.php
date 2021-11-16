<?php

namespace App\Listeners\Dependency;

use App\BaseUnitTestCase;
use App\Events\Database\DatabaseTablesUpdated;
use App\Models\Database\DatabaseTable;
use App\Services\DependencyService;
use Mockery;

class UpdateDependenciesTest extends BaseUnitTestCase
{
    private UpdateDependencies $dependencies;
    private DependencyService $mockService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(DependencyService::class);
        $this->app->instance(DependencyService::class, $this->mockService);
        $this->dependencies = $this->app->make(UpdateDependencies::class);
    }

    public function testHandleCallsService()
    {
        $tables = collect([new DatabaseTable(['name' => 'foo']), new DatabaseTable(['name' => 'bar'])]);

        $this->mockService->expects('updateDependenciesFromDatabaseTables')->with($tables)->once();
        $this->dependencies->handle(new DatabaseTablesUpdated($tables));
    }
}
