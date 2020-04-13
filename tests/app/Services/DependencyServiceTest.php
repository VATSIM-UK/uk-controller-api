<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Carbon\Carbon;
use LogicException;

class DependencyServiceTest extends BaseFunctionalTestCase
{
    public function testItDoesntTouchDependenciesIfNotFound()
    {
        DependencyService::touchDependencyByKey('NOT_DEPENDENCY_ONE', User::find(self::ACTIVE_USER_CID));
        $this->assertEquals(
            '2020-04-02 21:00:00',
            Dependency::where('key', 'DEPENDENCY_ONE')->first()->updated_at
        );
    }

    public function testItDoesntTouchUserDependenciesIfNoUser()
    {
        DependencyService::touchDependencyByKey('USER_DEPENDENCY_ONE', null);

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', 'USER_DEPENDENCY_ONE')
            ->first()
            ->pivot
            ->updated_at;

        $this->assertGreaterThanOrEqual('2020-04-02 21:00:00', $timestamp);
    }

    public function testItTouchesGlobalDependenciesByKey()
    {
        $now = Carbon::now();

        DependencyService::touchDependencyByKey('DEPENDENCY_ONE', User::find(self::ACTIVE_USER_CID));
        $this->assertGreaterThanOrEqual(
            $now->timestamp,
            Dependency::where('key', 'DEPENDENCY_ONE')->first()->updated_at->timestamp
        );
    }

    public function testItTouchesUserDependenciesByKey()
    {
        $now = Carbon::now();

        DependencyService::touchDependencyByKey('USER_DEPENDENCY_ONE', User::find(self::ACTIVE_USER_CID));

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', 'USER_DEPENDENCY_ONE')
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertGreaterThanOrEqual($now->timestamp, $timestamp);
    }

    public function testItTouchesGlobalDependencies()
    {
        $now = Carbon::now();

        DependencyService::touchGlobalDependency(Dependency::where('key', 'DEPENDENCY_ONE')->first());
        $this->assertGreaterThanOrEqual(
            $now->timestamp,
            Dependency::where('key', 'DEPENDENCY_ONE')->first()->updated_at->timestamp
        );
    }

    public function testItThrowsExceptionIfDependencyIsNotPerUser()
    {
        $this->expectException(LogicException::class);
        DependencyService::touchUserDependency(
            Dependency::where('key', 'DEPENDENCY_ONE')->first(),
            User::find(self::ACTIVE_USER_CID)
        );
    }

    public function testItTouchesUserDependencies()
    {
        $now = Carbon::now();

        DependencyService::touchUserDependency(
            Dependency::where('key', 'USER_DEPENDENCY_ONE')->first(),
            User::find(self::ACTIVE_USER_CID)
        );

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('key', 'USER_DEPENDENCY_ONE')
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertGreaterThanOrEqual($now->timestamp, $timestamp);
    }
}
