<?php

namespace App\Filament\Resources\StandResource\Pages;

use App\Filament\Resources\StandResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateStand extends CreateRecord
{
    protected static string $resource = StandResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['closed_at'] = $data['closed_at'] ? null : Carbon::now();
        return $data;
    }
}
