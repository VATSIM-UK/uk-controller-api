<?php

namespace App\Filament\Resources\Stands\Pages;

use App\Filament\Resources\Stands\StandResource;
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
