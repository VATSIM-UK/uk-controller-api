<?php

namespace App\Services;

use App\Exceptions\SrdUpdateFailedException;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SrdService
{
    private const SRD_URL_FORMAT = 'https://nats-uk.ead-it.com/cms-nats/export/sites/default/en/Publications/' .
        'digital-datasets/srd/FAB-UK-and-Ireland-SRD-%s_EXCEL-and-NOTES.xls';
    private const SRD_DOWNLOAD_FILE = 'downloaded-srd.xls';
    private const SRD_CURRENT_FILE = 'current-srd.xls';
    private const SRD_UPDATED_AT_CACHE_KEY = 'SRD_UPDATED_AT';

    /**
     * @throws SrdUpdateFailedException
     */
    public function updateSrdData(): bool
    {
        $this->downloadSrd();

        // If it doesn't need updating, set the last updated at key for reference
        if (!$this->localSrdNeedsUpdating()) {
            $this->setSrdLastUpdatedDate();
            return false;
        }

        $this->updateLocalSrdData();
        return true;
    }

    public function newSrdShouldBeAvailable(): bool
    {
        return Cache::get(self::SRD_UPDATED_AT_CACHE_KEY, AiracService::getBaseAiracDate())
            < AiracService::getPreviousAiracDay();
    }

    private function downloadSrd()
    {
        $srdContent = Http::get(
            sprintf(self::SRD_URL_FORMAT, AiracService::getPreviousAiracDay()->format('d-F-Y'))
        );

        if (!$srdContent->ok()) {
            Log::critical(
                sprintf("SRD download failed, status code %d", $srdContent->status()),
                [$srdContent->body()]
            );
            throw new SrdUpdateFailedException();
        }

        $this->getImportsFilesystem()->put(self::SRD_DOWNLOAD_FILE, $srdContent->body());
    }

    /**
     * Run the SRD import command to import into the database and once run,
     * move the downloaded file to "current" and update the last updated date in cache.
     */
    private function updateLocalSrdData(): void
    {
        Artisan::call(sprintf('srd:import %s', self::SRD_DOWNLOAD_FILE));
        $filesystem = $this->getImportsFilesystem();
        if ($filesystem->exists(self::SRD_CURRENT_FILE)) {
            $filesystem->delete(self::SRD_CURRENT_FILE);
        }
        $filesystem->move(self::SRD_DOWNLOAD_FILE, self::SRD_CURRENT_FILE);
        $this->setSrdLastUpdatedDate();
    }

    /**
     * Checks whether the local SRD needs updating by checking if the downloaded file is different
     * to the one we currently have. This is required because we cannot predict the time at which the SRD
     * will be released each time.
     */
    private function localSrdNeedsUpdating(): bool
    {
        $imports = $this->getImportsFilesystem();

        return !$imports->exists(self::SRD_CURRENT_FILE) ||
            md5($imports->get(self::SRD_CURRENT_FILE)) !== md5($imports->get(self::SRD_DOWNLOAD_FILE));
    }

    private function getImportsFilesystem(): Filesystem
    {
        return Storage::disk('imports');
    }

    private function setSrdLastUpdatedDate(): void
    {
        Cache::forever(self::SRD_UPDATED_AT_CACHE_KEY, Carbon::now());
    }
}
