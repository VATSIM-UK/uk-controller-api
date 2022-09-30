<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Filament\Resources\RunwayResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRunway extends CreateRecord
{
    use SetsRunwayInverses;

    protected static string $resource = RunwayResource::class;

    public function afterCreate()
    {
        $this->setInverse($this->record);
    }
}
