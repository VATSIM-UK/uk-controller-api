<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Filament\Resources\RunwayResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRunway extends EditRecord
{
    use SetsRunwayInverses;

    protected static string $resource = RunwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Set any inverse runways.
     */
    protected function afterSave()
    {
        $this->setInverse($this->record);
    }
}
