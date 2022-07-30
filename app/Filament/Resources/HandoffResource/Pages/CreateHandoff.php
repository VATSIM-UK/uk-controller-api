<?php

namespace App\Filament\Resources\HandoffResource\Pages;

use App\Filament\Resources\HandoffResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateHandoff extends CreateRecord
{
    protected static string $resource = HandoffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = Str::replace(' ', '_', Str::upper($data['description']));
        return $data;
    }
}
