<?php

namespace App\Filament\Resources\Stands\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Stands\StandResource;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStand extends EditRecord
{
    protected static string $resource = StandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        $data['closed_at'] = $data['closed_at'] ? null : Carbon::now();
        return $data;
    }
}
