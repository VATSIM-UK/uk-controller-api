<?php

namespace App\Services;

use App\Exceptions\SrdUpdateFailedException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SrdService
{
    private const SRD_CURRENT_FILE = 'current-srd.xlsx';
    private const SRD_VERSION_CACHE_KEY = 'SRD_VERSION';

    /**
     * @throws SrdUpdateFailedException
     */
    public function updateSrdData(): void
    {
        $this->downloadSrd();
        $this->updateLocalSrdData();
        $this->setSrdVersion();
    }

    public function srdNeedsUpdating(): bool
    {
        return Cache::get(self::SRD_VERSION_CACHE_KEY) !== AiracService::getCurrentAirac();
    }

    private function downloadSrd(): void
    {
        $srdContent = Http::get($this->srdDownloadUrl());

        if (!$srdContent->ok()) {
            Log::critical(
                sprintf("SRD download failed, status code %d", $srdContent->status()),
                [$srdContent->body()]
            );
            throw new SrdUpdateFailedException();
        }

        $this->getImportsFilesystem()->put(self::SRD_CURRENT_FILE, $srdContent->body());
    }

    private function srdDownloadUrl(): string
    {
        return sprintf(config('srd.download_url'), AiracService::getCurrentAirac());
    }

    /**
     * Run the SRD import command to import into the database.
     */
    private function updateLocalSrdData(): void
    {
        Artisan::call(sprintf('srd:import %s', self::SRD_CURRENT_FILE));
    }

    private function getImportsFilesystem(): Filesystem
    {
        return Storage::disk('imports');
    }

    private function setSrdVersion(): void
    {
        Cache::forever(self::SRD_VERSION_CACHE_KEY, AiracService::getCurrentAirac());
    }
}
