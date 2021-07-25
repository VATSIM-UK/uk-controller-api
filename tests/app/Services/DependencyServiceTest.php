<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;
use LogicException;

class DependencyServiceTest extends BaseFunctionalTestCase
{
    private const GLOBAL_DEPENDENCY = 'DEPENDENCY_ONE';
    private const USER_DEPENDENCY = 'USER_DEPENDENCY_ONE';

    /**
     * This method has been defined so that it can be called in place of a controller
     * by one of the tests. We need this as we can't mockery up an object from scratch.
     */
    public function foo(): string
    {
        return 'foo';
    }

    public function testItDoesntTouchDependenciesIfNotFound()
    {
        DependencyService::touchDependencyByKey('NOT_DEPENDENCY_ONE', User::find(self::ACTIVE_USER_CID));
        $this->assertEquals(
            '2020-04-02 21:00:00',
            Dependency::where('key', self::GLOBAL_DEPENDENCY)->first()->updated_at
        );
    }

    public function testItDoesntTouchUserDependenciesIfNoUser()
    {
        DependencyService::touchDependencyByKey(self::USER_DEPENDENCY, null);

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', self::USER_DEPENDENCY)
            ->first()
            ->pivot
            ->updated_at;

        $this->assertGreaterThanOrEqual('2020-04-02 21:00:00', $timestamp);
    }

    public function testItTouchesGlobalDependenciesByKey()
    {
        $now = Carbon::now();
        Date::setTestNow($now);
        Cache::shouldReceive('forget')->with('DEPENDENCY_1_CACHE')->once();

        DependencyService::touchDependencyByKey(self::GLOBAL_DEPENDENCY, User::find(self::ACTIVE_USER_CID));
        $this->assertEquals(
            $now->timestamp,
            Dependency::where('key', self::GLOBAL_DEPENDENCY)->first()->updated_at->timestamp
        );
    }

    public function testItTouchesUserDependenciesByKey()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $now = Carbon::now();
        Date::setTestNow($now);

        Cache::shouldReceive('forget')->with('DEPENDENCY_3_CACHE_USER_1203533')->once();

        DependencyService::touchDependencyByKey(self::USER_DEPENDENCY, User::find(self::ACTIVE_USER_CID));

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', self::USER_DEPENDENCY)
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertEquals($now->timestamp, $timestamp);
    }

    public function testItTouchesGlobalDependencies()
    {
        $now = Carbon::now();
        Date::setTestNow($now);
        Cache::shouldReceive('forget')->with('DEPENDENCY_1_CACHE')->once();

        DependencyService::touchGlobalDependency(Dependency::where('key', self::GLOBAL_DEPENDENCY)->first());
        $this->assertEquals(
            $now->timestamp,
            Dependency::where('key', self::GLOBAL_DEPENDENCY)->first()->updated_at->timestamp
        );
    }

    public function testItThrowsExceptionIfNotActingAsUser()
    {
        $this->expectException(LogicException::class);
        DependencyService::touchUserDependency(Dependency::where('key', self::GLOBAL_DEPENDENCY)->first());
    }

    public function testItThrowsExceptionIfDependencyIsNotPerUser()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $this->expectException(LogicException::class);
        DependencyService::touchUserDependency(Dependency::where('key', self::GLOBAL_DEPENDENCY)->first());
    }

    public function testItTouchesUserDependencies()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $now = Carbon::now();
        Date::setTestNow($now);
        Cache::shouldReceive('forget')->with('DEPENDENCY_3_CACHE_USER_1203533')->once();

        DependencyService::touchUserDependency(Dependency::where('key', self::USER_DEPENDENCY)->first());

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', self::USER_DEPENDENCY)
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertEquals($now->timestamp, $timestamp);
    }

    public function testItReturnsCachedDependencies()
    {
        Cache::forever('DEPENDENCY_1_CACHE', ['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], DependencyService::fetchDependencyDataById(1));
    }

    public function testItFetchesAndCachesDependencies()
    {
        Cache::forget('DEPENDENCY_1_CACHE');
        $this->assertEquals(
            DependencyService::fetchDependencyDataById(1),
            Cache::get('DEPENDENCY_1_CACHE')
        );
    }

    public function testItThrowsExceptionIfFetchedDependencyIsNotJsonResponse()
    {
        $this->app->instance('App\\Http\\Controllers\\FooController', $this);
        $this->expectException(InvalidArgumentException::class);
        Cache::forget('DEPENDENCY_1_CACHE');
        Dependency::find(1)->update(['action' => 'FooController@foo']);
        DependencyService::fetchDependencyDataById(1);
    }

    public function testItDeletesADependency()
    {
        Cache::shouldReceive('forget')
            ->with(self::GLOBAL_DEPENDENCY)
            ->once();
        DependencyService::deleteDependency(self::GLOBAL_DEPENDENCY);

        $this->assertDatabaseMissing(
            'dependencies',
            [
                'key' => self::GLOBAL_DEPENDENCY,
            ]
        );
    }
}
