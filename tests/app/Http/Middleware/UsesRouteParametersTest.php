<?php

namespace App\Http\Middleware;

use App\BaseUnitTestCase;
use Illuminate\Http\Request;
use Mockery;

class UsesRouteParametersTest extends BaseUnitTestCase
{
    public function testItReturnsAGivenRouteParameter()
    {
        $mockWithTrait = $this->getMockForTrait(UsesRouteParameters::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('route')->andReturn([2 => ['version' => '2.0.0', 'notVersion' => '4.0']]);
        $this->assertEquals('2.0.0', $mockWithTrait->getRouteParameter($request, 'version'));
    }
}
