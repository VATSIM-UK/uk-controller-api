<?php
namespace App\Services;

use App\BaseUnitTestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Exception;

class ManifestServiceTest extends BaseUnitTestCase
{

    /**
     *
     * @var ManifestService
     */
    private $service;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(ManifestService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(ManifestService::class, $this->service);
    }

    public function testItReturnsAnEmptyArrayIfNoFiles()
    {
        Storage::shouldReceive('disk')
            ->once()
            ->with('local')
            ->andReturnSelf();

        Storage::shouldReceive('files')
            ->once()
            ->with('dependencies')
            ->andReturn([]);

        $this->assertEquals([], $this->service->getManifest('local', 'dependencies'));
    }
    
    public function testItGeneratesAFullManifestIfCachingOff()
    {
        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('local')
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

        $expected = [
            'test1.json' => [
                'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test1.json',
                'md5' => md5('test1'),
            ],
            'test2.json' => [
                'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test2.json',
                'md5' => md5('test2'),
            ],
        ];

        $this->assertEquals($expected, $this->service->getManifest('local', 'dependencies'));
    }

    public function testItGeneratesAFullManifestIfNothingCached()
    {
        Cache::shouldReceive('has')
            ->once()
            ->with('dependencies')
            ->andReturn(false);

        Cache::shouldReceive('forever')
            ->once();

        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('local')
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

        $expected = [
            'test1.json' => [
                'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test1.json',
                'md5' => md5('test1'),
            ],
            'test2.json' => [
                'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test2.json',
                'md5' => md5('test2'),
            ],
        ];

        $this->assertEquals($expected, $this->service->getManifest('local', 'dependencies', true));
    }

    public function testItCachesManifestIfEnabled()
    {
        $expected = [
            'test1.json' => [
                'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test1.json',
                'md5' => md5('test1'),
            ],
            'test2.json' => [
                'uri' => 'http://ukcp.vatsim.uk/storage/dependencies/test2.json',
                'md5' => md5('test2'),
            ],
        ];

        Cache::shouldReceive('has')
            ->once()
            ->with('dependencies')
            ->andReturn(false);

        Cache::shouldReceive('forever')
            ->once($expected);

        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('local')
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

        $this->service->getManifest('local', 'dependencies', true);
    }

    public function testItReturnsACachedManifestIfAvailable()
    {
        // Mock off the storage and cache facades, to fake filesystem interactions
        Cache::shouldReceive('has')
            ->once()
            ->with('dependencies')
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->once()
            ->with('dependencies')
            ->andReturn(['test']);

        $expected = ['test'];

        $this->assertEquals($expected, $this->service->getManifest('local', 'dependencies', true));
    }

    public function testItThrowsExceptionIfStorageFails()
    {
        $this->expectException(Exception::class);
        Storage::shouldReceive('disk')
        ->zeroOrMoreTimes()
        ->with('local')
        ->andThrow(new Exception);

        $this->service->getManifest('local', 'dependencies', true);
    }
}
