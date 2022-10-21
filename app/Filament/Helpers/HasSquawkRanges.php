<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use App\Rules\Squawk\SqauwkCode;
use Filament\Forms\Components\TextInput;

trait HasSquawkRanges
{
    use TranslatesStrings;

    public static function squawkRangeInputs(): array
    {
        return [
            TextInput::make('first')
                ->required()
                ->rule(new SqauwkCode())
                ->label(self::translateFormPath('first.label'))
                ->helperText(self::translateFormPath('first.helper')),
            TextInput::make('last')
                ->required()
                ->rule(new SqauwkCode())
                ->label(self::translateFormPath('last.label'))
                ->helperText(self::translateFormPath('last.helper'))
        ];
    }
}
