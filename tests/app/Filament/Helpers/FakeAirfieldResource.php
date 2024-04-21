<?php

namespace App\Filament\Helpers;

use App\Models\Airfield\Airfield;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class FakeAirfieldResource extends Resource
{
    use HasCoordinates;

    protected static ?string $model = Airfield::class;
    protected static ?string $navigationIcon = 'heroicon-o-x-circle';
    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...self::coordinateInputs(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFakeAirfield::route('/'),
            'create' => CreateFakeAirfield::route('/create'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'airfields';
    }
}
