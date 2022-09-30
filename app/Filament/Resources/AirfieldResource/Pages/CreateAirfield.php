<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use App\Models\Controller\Handoff;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateAirfield extends CreateRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function afterCreate(): void
    {
        DB::transaction(function () {
            $handoff = Handoff::create(
                ['description' => sprintf('Default departure handoff for %s', $this->record->code)]
            );
            $this->record->update(['handoff_id' => $handoff->id]);
        });
    }
}
