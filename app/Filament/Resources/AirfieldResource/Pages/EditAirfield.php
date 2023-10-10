<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAirfield extends EditRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->using(function (DeleteAction $action)
                {
                    DB::transaction(function () use ($action)
                    {
                        DB::table('msl_airfield')->where('airfield_id', $action->getRecord()->id)->delete();
                        $action->getRecord()->delete();
                    });
                    redirect(AirfieldResource::getUrl('index'));
                }),
        ];
    }
}
