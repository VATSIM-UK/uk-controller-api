<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class CheckForKeyTableUpdatesTest extends BaseFunctionalTestCase
{
    public function testItCallsService()
    {
        $serviceMock = Mockery::mock(DatabaseService::class);
        $serviceMock->shouldReceive('updateTableStatus')->withNoArgs()->once();
        Artisan::call('database:check-table-updates');
    }
}
