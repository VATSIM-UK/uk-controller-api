<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use Filament\Forms\Components\TextInput;

trait HasCoordinates
{
    use TranslatesStrings;

    public static function latitudeInput(): TextInput
    {
        return TextInput::make('latitude')
            ->required()
            ->numeric('decimal:7')
            ->minValue(-90)
            ->maxValue(90)
            ->label(self::translateFormPath('latitude.label'))
            ->helperText(self::translateFormPath('latitude.helper'));
    }

    public static function longitudeInput(): TextInput
    {
        return TextInput::make('longitude')
            ->required()
            ->numeric('decimal:7')
            ->minValue(-180)
            ->maxValue(180)
            ->label(self::translateFormPath('longitude.label'))
            ->helperText(self::translateFormPath('longitude.helper'));
    }
}
