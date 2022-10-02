<?php

namespace App\Filament\Resources\SrdRouteResource\Pages;

use App\Filament\Resources\SrdRouteResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSrdRoute extends ViewRecord
{
    protected static string $resource = SrdRouteResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['route_segment'] = SrdRouteResource::buildFullSrdRouteString($this->record);

        return $data;
    }
}
