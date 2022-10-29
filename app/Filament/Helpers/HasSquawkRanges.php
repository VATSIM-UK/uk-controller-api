<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use App\Rules\Squawk\SqauwkCode;
use Filament\Forms\Components\TextInput;

trait HasSquawkRanges
{
    use TranslatesStrings;

    protected static function squawkRangeInputs(): array
    {
        return [
            static::singleSquawkInput('first', 'first'),
            static::singleSquawkInput('last', 'last'),
        ];
    }

    protected static function singleSquawkInput(string $name, string $labelName): TextInput
    {
        return TextInput::make($name)
            ->required()
            ->rule(new SqauwkCode())
            ->label(self::translateFormPath(sprintf('%s.label', $labelName)))
            ->helperText(self::translateFormPath(sprintf('%s.helper', $labelName)));
    }
}
