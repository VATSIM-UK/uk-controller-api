<?php

namespace App\Filament\Resources\Stands\Pages;

use App\Filament\Resources\Stands\StandResource;
use Filament\Resources\Pages\ViewRecord;

class ViewStand extends ViewRecord
{
    protected static string $resource = StandResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['closed_at'] = $data['closed_at'] === null;
        return $data;
    }
}
