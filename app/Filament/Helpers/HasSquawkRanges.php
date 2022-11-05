<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use App\Rules\Squawk\SqauwkCode;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

trait HasSquawkRanges
{
    use TranslatesStrings;

    protected static function squawkRangeInputs(): array
    {
        return [
            static::singleSquawkInput('first', 'first'),
            static::singleSquawkInput('last', 'last')
                ->gte('first'),
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

    protected static function squawkRangeTableColumns(): array
    {
        return [
            self::singleSquawkColumn('first'),
            self::singleSquawkColumn('last'),
        ];
    }

    protected static function squawkCodeTableColumn(): TextColumn
    {
        return self::singleSquawkColumn('code');
    }

    protected static function singleSquawkColumn(string $name): TextColumn
    {
        return TextColumn::make($name)
            ->label(self::translateTablePath(sprintf('columns.%s', $name)));
    }
}
