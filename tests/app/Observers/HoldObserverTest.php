<?php

namespace App\Observers;

use Mockery;
use Carbon\Carbon;
use App\BaseUnitTestCase;
use App\Models\Hold\Hold;
use App\Observers\HoldObserver;
use App\Services\DependencyService;
use Illuminate\Support\Facades\App;
use App\Models\Dependency\Dependency;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class HoldObserverTest extends BaseUnitTestCase
{
    use DatabaseTransactions;

    private Hold $hold;

    private string $dependencyKey;
    
    public function setUp() : void
    {
        parent::setUp();

        $this->observer = $this->app->make(HoldObserver::class);

        $this->dependencyKey = 'DEPENDENCY_HOLDS';

        $this->hold = Hold::factory()->create();

        Carbon::setTestNow(Carbon::now());

        $this->mockStandDependency = Dependency::create([
            'key' => $this->dependencyKey,
            'action' => 'HoldController@getAllHolds',
            'local_file' => 'holds.json',
            'per_user' => 0,
        ]);
    }

    public function testCallsDependencyTouchMethodOnCreated()
    {
        $this->observer->created($this->hold);

        $this->assertDatabaseHas('dependencies', [
            'key' => $this->dependencyKey,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function testCallsDependencyTouchMethodOnUpdated()
    {
        $this->observer->updated($this->hold);

        $this->assertDatabaseHas('dependencies', [
            'key' => $this->dependencyKey,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function testCallsDependencyTouchMethodOnDeleted()
    {
        $this->observer->deleted($this->hold);

        $this->assertDatabaseHas('dependencies', [
            'key' => $this->dependencyKey,
            'updated_at' => Carbon::now(),
        ]);
    }
}
