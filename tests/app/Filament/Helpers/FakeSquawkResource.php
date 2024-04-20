<?php

namespace App\Filament\Helpers;

use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class FakeSquawkResource extends Resource
{
    use HasSquawkRanges;

    protected static ?string $model = CcamsSquawkRange::class;
    protected static ?string $navigationIcon = 'heroicon-o-x-circle';
    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...self::squawkRangeInputs(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateFakeAirfield::route('/create'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'squawks.ccams';
    }
}
