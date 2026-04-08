<?php

namespace App\Filament\Resources\SrdRoutes\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SrdRoutes\SrdRouteResource;
use App\Services\AiracService;
use Filament\Resources\Pages\ListRecords;

class ListSrdRoutes extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = SrdRouteResource::class;

    public function getTitle(): string
    {
        return sprintf('SRD Routes: AIRAC %s', AiracService::getCurrentAirac());
    }
}
