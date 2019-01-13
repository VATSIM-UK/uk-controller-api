<?php

namespace App\Http\Middleware;

use App\BaseUnitTestCase;
use Illuminate\Http\Request;
use Mockery;
use App\Helpers\Vatsim\VatsimCidValidator;

class VatsimCidTest extends BaseUnitTestCase
{
    /**
     * Middleware under test
     *
     * @var VatsimCid
     */
    private $middleware;
    
    public function setUp()
    {
        parent::setUp();
        $this->middleware = $this->app->make(VatsimCid::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(VatsimCid::class, $this->middleware);
    }

    public function testItRejectsInvalidCids()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('route')->andReturn([2 => ['cid' => VatsimCidValidator::MINIMUM_CID - 1]]);

        $expected = 400;
        $actual = $this->middleware->handle($request, function () {
            return false;
        })->getStatusCode();

        $this->assertEquals($expected, $actual);
    }

    public function testItAllowsValidCids()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('route')->andReturn([2 => ['cid' => VatsimCidValidator::MINIMUM_MEMBER_CID]]);

        $expected = 418;
        $actual = $this->middleware->handle($request, function () {
            return response('', 418);
        })->getStatusCode();

        $this->assertEquals($expected, $actual);
    }
}
