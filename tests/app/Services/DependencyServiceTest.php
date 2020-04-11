<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use LogicException;

class DependencyServiceTest extends BaseFunctionalTestCase
{
    public function testItTouchesDependencies()
    {
        $now = Carbon::now();

        DependencyService::touchDependency('DEPENDENCY_ONE');
        $this->assertGreaterThanOrEqual(
            $now->timestamp,
            Dependency::where('key', 'DEPENDENCY_ONE')->first()->updated_at->timestamp
        );
    }

    public function testItThrowsExceptionIfDependencyIsNotPerUser()
    {
        $this->expectException(LogicException::class);
        DependencyService::touchUserDependency('DEPENDENCY_ONE', User::find(self::ACTIVE_USER_CID));
    }

    public function testItTouchesUserDependencies()
    {
        $now = Carbon::now();

        DependencyService::touchUserDependency('USER_DEPENDENCY_ONE', User::find(self::ACTIVE_USER_CID));

        $timestamp = User::find(self::ACTIVE_USER_CID)
            ->dependencies()
            ->where('dependency_id', 3)
            ->first()
            ->pivot
            ->updated_at
            ->timestamp;

        $this->assertGreaterThanOrEqual($now->timestamp, $timestamp);
    }
}
