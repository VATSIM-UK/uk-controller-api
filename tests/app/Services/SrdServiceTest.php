<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Exceptions\SrdUpdateFailedException;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use Illuminate\Support\Facades\Storage;

class SrdServiceTest extends BaseFunctionalTestCase
{
    private const SRD_CURRENT_FILE = 'current-srd.xlsx';
    private const SRD_VERSION_CACHE_KEY = 'SRD_VERSION';

    private SrdService $service;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->service = $this->app->make(SrdService::class);
    }

    public function testItThrowsExceptionWhenSrdDownloadFails()
    {
        $this->expectException(SrdUpdateFailedException::class);
        $this->mockSrdHttpCall(500, 'foo');
        $this->service->updateSrdData();
    }

    public function testItUpdatesTheSrdData()
    {
        $this->mockSrdHttpCall(200, 'foo');
        $this->mockSrdFilesystem('foo');
        $this->mockCacheSrdVersionWrite();

        $this->service->updateSrdData();
    }

    public function testSrdNeedsUpdatingIfSrdNeverUpdated()
    {
        $this->mockSrdUpdatedCache(null);
        $this->assertTrue($this->service->srdNeedsUpdating());
    }

    public function testSrdNeedsUpdatingIfSrdNotCurrentVersion()
    {
        $this->mockSrdUpdatedCache('2201');
        $this->assertTrue($this->service->srdNeedsUpdating());
    }

    public function testSrdDoesNotNeedUpdatingIfSrdCurrentVersion()
    {
        $this->mockSrdUpdatedCache(AiracService::getCurrentAirac());
        $this->assertFalse($this->service->srdNeedsUpdating());
    }

    private function mockSrdUpdatedCache(?string $returnValue): void
    {
        Cache::shouldReceive('get')->with(self::SRD_VERSION_CACHE_KEY)->andReturn($returnValue);
    }

    private function mockSrdHttpCall(int $statusCode, string $responseData): void
    {
        $expectedUrl = sprintf(config('srd.download_url'), AiracService::getCurrentAirac());
        Http::fake([
            $expectedUrl => Http::response(
                $responseData,
                $statusCode
            ),
        ])->preventStrayRequests();
    }

    private function mockSrdFilesystem(string $responseData): void
    {
        $mockFileSystem = Mockery::mock(Filesystem::class);
        $mockFileSystem->shouldReceive('put')->with(self::SRD_CURRENT_FILE, $responseData)->once();

        Storage::shouldReceive('disk')->with('imports')->andReturn($mockFileSystem);
        Artisan::shouldReceive('call')->with('srd:import current-srd.xlsx')->once();
    }

    private function mockCacheSrdVersionWrite(): void
    {
        Cache::shouldReceive('forever')->with(
            self::SRD_VERSION_CACHE_KEY,
            Mockery::on(
                fn(string $value) => $value === AiracService::getCurrentAirac()
            )
        )->once();
    }
}
