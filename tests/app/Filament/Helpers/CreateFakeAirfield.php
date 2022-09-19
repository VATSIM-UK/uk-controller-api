<?php

namespace App\Filament\Helpers;

use App\Models\Airfield\Airfield;
use Filament\Resources\Pages\CreateRecord;

class CreateFakeAirfield extends CreateRecord
{
    protected static string $resource = FakeAirfieldResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge(
            Airfield::factory()->make()->toArray(),
            $data
        );
    }
}
