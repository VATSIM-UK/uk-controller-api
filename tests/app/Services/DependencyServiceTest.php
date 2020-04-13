<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Carbon\Carbon;
use LogicException;

class DependencyServiceTest extends BaseFunctionalTestCase
{
    private const GLOBAL_DEPENDENCY = 'DEPENDENCY_ONE';
    private const USER_DEPENDENCY = 'USER_DEPENDENCY_ONE';

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

        DependencyService::touchDependencyByKey(self::GLOBAL_DEPENDENCY, User::find(self::ACTIVE_USER_CID));
        $this->assertGreaterThanOrEqual(
            $now->timestamp,
            Dependency::where('key', self::GLOBAL_DEPENDENCY)->first()->updated_at->timestamp
        );
    }

    public function testItTouchesUserDependenciesByKey()
    {
        $now = Carbon::now();

        DependencyService::touchDependencyByKey(self::USER_DEPENDENCY, User::find(self::ACTIVE_USER_CID));

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', self::USER_DEPENDENCY)
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertGreaterThanOrEqual($now->timestamp, $timestamp);
    }

    public function testItTouchesGlobalDependencies()
    {
        $now = Carbon::now();

        DependencyService::touchGlobalDependency(Dependency::where('key', self::GLOBAL_DEPENDENCY)->first());
        $this->assertGreaterThanOrEqual(
            $now->timestamp,
            Dependency::where('key', self::GLOBAL_DEPENDENCY)->first()->updated_at->timestamp
        );
    }

    public function testItThrowsExceptionIfDependencyIsNotPerUser()
    {
        $this->expectException(LogicException::class);
        DependencyService::touchUserDependency(
            Dependency::where('key', self::GLOBAL_DEPENDENCY)->first(),
            User::find(self::ACTIVE_USER_CID)
        );
    }

    public function testItTouchesUserDependencies()
    {
        $now = Carbon::now();

        DependencyService::touchUserDependency(
            Dependency::where('key', self::USER_DEPENDENCY)->first(),
            User::find(self::ACTIVE_USER_CID)
        );

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', self::USER_DEPENDENCY)
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertGreaterThanOrEqual($now->timestamp, $timestamp);
    }
}
