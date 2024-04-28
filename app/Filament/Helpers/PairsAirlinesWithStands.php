<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

trait PairsAirlinesWithStands
{
    use HasAirlinePairingCommonFields;
    use TranslatesStrings;

    private static function airlineStandPairingTableColumns(): array
    {
        return [
            ...self::commonPairingTableColumns(),
            TextColumn::make('not_before')
                ->label(self::translateTablePath('columns.not_before'))
                ->date('H:i')
        ];
    }

    private static function airlineStandPairingFormFields(): array
    {
        return [
            ...self::commonPairingFormFields(),
            TimePicker::make('not_before')
                ->label(self::translateFormPath('not_before.label'))
                ->helperText(self::translateFormPath('not_before.helper'))
                ->displayFormat('H:i')
                ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set) {
                    if ($get('not_before') !== null) {
                        $set(
                            'not_before',
                            Carbon::parse($get('not_before'))->startOfMinute()->toDateTimeString()
                        );
                    }
                }),
        ];
    }

    private static function unpairingClosure(): Closure
    {
        return function (DetachAction $action) {
            DB::table('airline_stand')
                ->where('id', $action->getRecord()->pivot_id)
                ->delete();
        };
    }
}
