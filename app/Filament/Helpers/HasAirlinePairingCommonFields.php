<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use App\Models\Aircraft\Aircraft;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;

trait HasAirlinePairingCommonFields
{
    use TranslatesStrings;

    private static string $defaultColumnValue = '--';

    private static function commonPairingTableColumns(): array
    {
        return [
            TextColumn::make('aircraft_id')
                ->default(self::$defaultColumnValue)
                ->label(self::translateTablePath('columns.aircraft'))
                ->formatStateUsing(fn (int|string $state) => is_int($state) ? Aircraft::find($state)->code : '')
                ->sortable()
                ->searchable(),
            TextColumn::make('destination')
                ->label(self::translateTablePath('columns.destination'))
                ->default(self::$defaultColumnValue)
                ->sortable(),
            TextColumn::make('full_callsign')
                ->default(self::$defaultColumnValue)
                ->label(self::translateTablePath('columns.full_callsign')),
            TextColumn::make('callsign_slug')
                ->default(self::$defaultColumnValue)
                ->label(self::translateTablePath('columns.callsign_slug')),
            TextColumn::make('priority')
                ->default(self::$defaultColumnValue)
                ->label(self::translateTablePath('columns.priority')),
        ];
    }

    private static function commonPairingFormFields(): array
    {
        return [
            Select::make('aircraft_id')
                ->searchable()
                ->options(SelectOptions::aircraftTypes())
                ->label(self::translateFormPath('aircraft.label'))
                ->helperText(self::translateFormPath('aircraft.helper')),
            TextInput::make('destination')
                ->label(self::translateFormPath('destination.label'))
                ->helperText(self::translateFormPath('destination.helper'))
                ->maxLength(4),
            TextInput::make('full_callsign')
                ->label(self::translateFormPath('full_callsign.label'))
                ->helperText(self::translateFormPath('full_callsign.helper'))
                ->maxLength(4),
            TextInput::make('callsign_slug')
                ->label(self::translateFormPath('callsign_slug.label'))
                ->helperText(self::translateFormPath('callsign_slug.helper'))
                ->maxLength(4),
            TextInput::make('priority')
                ->label(self::translateFormPath('priority.label'))
                ->helperText(self::translateFormPath('priority.helper'))
                ->default(100)
                ->numeric()
                ->minValue(1)
                ->maxValue(9999)
                ->required(),
        ];
    }
}
