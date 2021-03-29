<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Console\Commands\SrdImport;
use App\Exceptions\SrdUpdateFailedException;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use Storage;

class SrdServiceTest extends BaseFunctionalTestCase
{
    private const SRD_DOWNLOAD_FILE = 'downloaded-srd.xls';
    private const SRD_CURRENT_FILE = 'current-srd.xls';
    private const SRD_UPDATED_AT_CACHE_KEY = 'SRD_UPDATED_AT';

    private SrdService $service;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->service = $this->app->make(SrdService::class);
    }

    private function mockSrdUpdatedCache(Carbon $returnValue)
    {
        Cache::shouldReceive('get')
            ->with(
                'SRD_UPDATED_AT',
                Mockery::on(function (Carbon $value) {
                    return $value->isSameAs('Y-m-d H:i:s', AiracService::getBaseAiracDate());
                }))
            ->andReturn($returnValue);
    }

    public function testNewSrdIsAvailableIfSrdNeverUpdated()
    {
        $this->mockSrdUpdatedCache(AiracService::getBaseAiracDate());

        $this->assertTrue($this->service->newSrdShouldBeAvailable());
    }

    public function testNewSrdIsAvailableIfNewAiracIsAvailable()
    {
        $this->mockSrdUpdatedCache(AiracService::getBaseAiracDate()->addDays(15));

        $this->assertTrue($this->service->newSrdShouldBeAvailable());
    }

    public function testNewSrdIsNotAvailableIfUpdatedSinceLastAirac()
    {
        $this->mockSrdUpdatedCache(AiracService::getPreviousAiracDay()->addSecond());
        $this->assertFalse($this->service->newSrdShouldBeAvailable());
    }

    private function mockSrdHttpCall(int $statusCode, string $responseData)
    {
        Http::fake([
           'http://www.nats-uk.ead-it.com/aip/current/srd/SRD_Spreadsheet.xls' => Http::response(
               $responseData,
               $statusCode
           )
        ]);
    }

    public function testItThrowsExceptionWhenSrdDownloadFails()
    {
        $this->expectException(SrdUpdateFailedException::class);
        $this->mockSrdHttpCall(500, 'foo');
        $this->service->updateSrdData();
    }

    private function mockSrdFilesystem(
        string $responseData,
        bool $currentFileExists,
        bool $shouldRecieveMove,
        string $currentFileData = null
    ) {
        $mockFileSystem = Mockery::mock(Filesystem::class);
        $mockFileSystem->shouldReceive('put')->with(self::SRD_DOWNLOAD_FILE, $responseData)->once();
        $mockFileSystem->shouldReceive('exists')->with(self::SRD_CURRENT_FILE)->andReturn($currentFileExists);
        $mockFileSystem->shouldReceive('get')->with(self::SRD_DOWNLOAD_FILE)->andReturn($responseData);
        $mockFileSystem->shouldReceive('get')->with(self::SRD_CURRENT_FILE)->andReturn($currentFileData);

        if ($shouldRecieveMove) {
            $mockFileSystem->shouldReceive('move')->with(self::SRD_DOWNLOAD_FILE, self::SRD_CURRENT_FILE);
            Artisan::shouldReceive('call')->with('srd:import downloaded-srd.xls')->once();
        } else {
            $mockFileSystem->shouldNotReceive('move');
            Artisan::shouldReceive('call')->never();
        }

        Storage::shouldReceive('disk')->with('imports')->andReturn($mockFileSystem);
    }

    private function mockCacheTimestampWrite()
    {
        Cache::shouldReceive('forever')->with(
            self::SRD_UPDATED_AT_CACHE_KEY,
            Mockery::on(
                function (Carbon $value) {
                    return $value->isSameAs('Y-m-d H:i:s', Carbon::now());
                }
            )
        )->once();
    }

    public function testItUpdatesSrdIfNoCurrentFileToCompareTo()
    {
        $this->mockSrdHttpCall(200, 'foo');
        $this->mockSrdFilesystem(
            'foo',
            false,
            true,
        );
        $this->mockCacheTimestampWrite();

        $this->assertTrue($this->service->updateSrdData());
    }

    public function testItUpdatesSrdIfLocalFileDoesntMatchDownloaded()
    {
        $this->mockSrdHttpCall(200, 'foo');
        $this->mockSrdFilesystem(
            'foo',
            true,
            true,
            'bar'
        );
        $this->mockCacheTimestampWrite();

        $this->assertTrue($this->service->updateSrdData());
    }

    public function testItDoesntUpdateSrdIfLocalFileMatchesDownloaded()
    {
        $this->mockSrdHttpCall(200, 'foo');
        $this->mockSrdFilesystem(
            'foo',
            true,
            false,
            'foo'
        );
        $this->mockCacheTimestampWrite();

        $this->assertFalse($this->service->updateSrdData());
    }
}
