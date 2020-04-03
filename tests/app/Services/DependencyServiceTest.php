<?php

namespace app\Services;

use App\BaseFunctionalTestCase;
use App\Models\Dependency\Dependency;
use Carbon\Carbon;

class DependencyServiceTest extends BaseFunctionalTestCase
{
    public function testItTouchesDependencies()
    {
        $touchTime = Carbon::now()->addDay();
        Carbon::setTestNow($touchTime);

        DependencyService::touchDependency('DEPENDENCY_ONE');
        $this->assertEquals($touchTime, Dependency::where('key', 'DEPENDENCY_ONE')->first()->updated_at);
    }
}
