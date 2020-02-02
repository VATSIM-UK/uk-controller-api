<?php

namespace App\Http\Middleware;

use App\BaseTestCase;
use Illuminate\Http\Request;
use Mockery;
use TestingUtils\Traits\WithSeedUsers;

class GithubAuthTest extends BaseTestCase
{
    /**
     * @var GithubAuth
     */
    private $middleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->middleware = $this->app->make(GithubAuth::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(GithubAuth::class, $this->middleware);
    }

    public function testItAllowsValidDataThrough()
    {
        $requestBody = json_encode(['test1' => 'test1', 'test2' => 'what']);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')
            ->with('X-Hub-Signature')
            ->andReturn(hash_hmac('sha1', $requestBody, config('github.secret')));
        $request->shouldReceive('getContent')->andReturn($requestBody);

        $this->assertEquals(
            418,
            $this->middleware->handle(
                $request,
                function (Request $request) {
                    return response('', 418);
                }
            )->getStatusCode()
        );
    }

    public function testItDisallowsInvalidData()
    {
        $requestBody = json_encode(['test1' => 'test1', 'test2' => 'what']);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')
            ->with('X-Hub-Signature')
            ->andReturn(hash_hmac('sha1', $requestBody, 'notgithubsecret'));
        $request->shouldReceive('getContent')->andReturn($requestBody);

        $this->assertEquals(
            403,
            $this->middleware->handle(
                $request,
                function (Request $request) {
                    return response('', 418);
                }
            )->getStatusCode()
        );
    }
}
