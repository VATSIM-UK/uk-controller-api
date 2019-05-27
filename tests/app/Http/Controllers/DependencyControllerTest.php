<?php
namespace App\Http\Controllers;

use App\BaseApiTestCase;
use Illuminate\Support\Facades\Storage;

class DependencyControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(DependencyController::class, $this->app->make(DependencyController::class));
    }

    public function testItRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertStatus(403);
    }

    public function testItReturnsAManifest()
    {
        // We have to mock off our disks...
        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('public')
            ->andReturnSelf();

        Storage::shouldReceive('files')
            ->once()
            ->with('dependencies')
            ->andReturn(['dependencies/test1.json', 'dependencies/test2.json']);

        Storage::shouldReceive('url')
            ->once()
            ->with('dependencies/test1.json')
            ->andReturn("http://ukcp.vatsim.uk/storage/dependencies/test1.json");

        Storage::shouldReceive('url')
            ->once()
            ->with('dependencies/test2.json')
            ->andReturn("http://ukcp.vatsim.uk/storage/dependencies/test2.json");

        Storage::shouldReceive('get')
            ->once()
            ->with('dependencies/test1.json')
            ->andReturn("test1");

        Storage::shouldReceive('get')
            ->once()
            ->with('dependencies/test2.json')
            ->andReturn("test2");

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertJson(
                [
                'manifest' => [
                'test1.json' => [
                    'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test1.json',
                    'md5' => md5('test1'),
                ],
                'test2.json' => [
                    'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test2.json',
                    'md5' => md5('test2'),
                ],
                ]
                ]
            )
            ->assertStatus(200);
    }
}
