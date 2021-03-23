<?php

namespace App\Observers;

use Mockery;
use Carbon\Carbon;
use App\BaseUnitTestCase;
use Mockery\MockInterface;
use App\Models\Stand\Stand;
use App\Services\StandService;
use App\Observers\StandObserver;
use App\Services\DependencyService;
use Illuminate\Support\Facades\App;
use App\Models\Dependency\Dependency;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class StandObserverTest extends BaseUnitTestCase
{
    use DatabaseTransactions;
    private Stand $stand;
    
    public function setUp() : void
    {
        parent::setUp();

        $this->observer = $this->app->make(StandObserver::class);

        $this->stand = Stand::factory()->create();

        Carbon::setTestNow(Carbon::now());
    }

    public function testCallsDependencyTouchMethodOnCreated()
    {
        $this->observer->created($this->stand);

        $this->assertDatabaseHas('dependencies', [
            'key' => StandService::STAND_DEPENDENCY_KEY,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function testCallsDependencyTouchMethodOnUpdated()
    {
        $this->observer->updated($this->stand);

        $this->assertDatabaseHas('dependencies', [
            'key' => StandService::STAND_DEPENDENCY_KEY,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function testCallsDependencyTouchMethodOnDeleted()
    {
        $this->observer->deleted($this->stand);

        $this->assertDatabaseHas('dependencies', [
            'key' => StandService::STAND_DEPENDENCY_KEY,
            'updated_at' => Carbon::now(),
        ]);
    }
}
