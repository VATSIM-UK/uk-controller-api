<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use Filament\Forms\Components\TextInput;

trait HasCoordinates
{
    use TranslatesStrings;

    public static function latitudeInput(string $fieldName = 'latitude'): TextInput
    {
        return TextInput::make($fieldName)
            ->required()
            ->numeric('decimal:7')
            ->minValue(-90)
            ->maxValue(90)
            ->label(self::translateFormPath('latitude.label'))
            ->helperText(self::translateFormPath('latitude.helper'));
    }

    public static function longitudeInput(string $fieldName = 'longitude'): TextInput
    {
        return TextInput::make($fieldName)
            ->required()
            ->numeric('decimal:7')
            ->minValue(-180)
            ->maxValue(180)
            ->label(self::translateFormPath('longitude.label'))
            ->helperText(self::translateFormPath('longitude.helper'));
    }

    public static function coordinateInputs(
        string $latitudeFieldName = 'latitude',
        string $longitudeFieldName = 'longitude'
    ): array {
        return [self::latitudeInput($latitudeFieldName), self::longitudeInput($longitudeFieldName)];
    }
}
