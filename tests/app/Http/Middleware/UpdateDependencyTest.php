<?php

namespace App\Http\Middleware;

use App\BaseFunctionalTestCase;
use App\Models\Dependency\Dependency;
use App\Services\DependencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Mockery;

class UpdateDependencyTest extends BaseFunctionalTestCase
{
    /**
     * Middleware under test
     *
     * @var UpdateDependency
     */
    private $middleware;

    public function setUp() : void
    {
        parent::setUp();
        $this->middleware = $this->app->make(UpdateDependency::class);
    }

    public function testItPassesToTheNextMiddleware()
    {
        $request = Mockery::mock(Request::class);

        $expected = 418;
        $actual = $this->middleware->handle($request, function () {
            return new Response('', 418);
        })->getStatusCode();

        $this->assertEquals($expected, $actual);
    }

    public function testItUpdatesAllDependenciesSpecified()
    {
        $request = Mockery::mock(Request::class);
        $this->middleware->handle(
            $request,
            function () {
                return new Response('', 418);
            },
            'DEPENDENCY_ONE',
            'DEPENDENCY_TWO'
        );

        $now = Carbon::now();
        Date::setTestNow($now);

        DependencyService::touchGlobalDependency(Dependency::where('key', 'DEPENDENCY_ONE')->first());
        $this->assertEquals(
            $now->timestamp,
            Dependency::where('key', 'DEPENDENCY_ONE')->first()->updated_at->timestamp
        );
        $this->assertEquals(
            $now->timestamp,
            Dependency::where('key', 'DEPENDENCY_TWO')->first()->updated_at->timestamp
        );
    }
}
