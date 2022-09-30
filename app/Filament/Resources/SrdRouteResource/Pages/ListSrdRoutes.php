<?php

namespace App\Filament\Resources\SrdRouteResource\Pages;

use App\Filament\Resources\SrdRouteResource;
use App\Services\AiracService;
use Filament\Resources\Pages\ListRecords;

class ListSrdRoutes extends ListRecords
{
    protected static string $resource = SrdRouteResource::class;

    public function getTitle(): string
    {
        return sprintf('SRD Routes: AIRAC %s', AiracService::getCurrentAirac());
    }
}
