<?php

namespace App\Listeners\Database;

use App\BaseUnitTestCase;
use App\Services\DatabaseService;
use Mockery;

class MigrationsFinishedTest extends BaseUnitTestCase
{
    private MigrationsFinished $listener;
    private DatabaseService $mockService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(DatabaseService::class);
        $this->app->instance(DatabaseService::class, $this->mockService);
        $this->listener = $this->app->make(MigrationsFinished::class);
    }

    public function testHandleCallsService()
    {
        $this->mockService->expects('updateTableStatus')->withNoArgs()->once();
        $this->listener->handle();
    }
}
