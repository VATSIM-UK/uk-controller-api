<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\AirfieldPairingSquawkRangeResource\Pages;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Rules\Airfield\PartialAirfieldIcao;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class AirfieldPairingSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = AirfieldPairingSquawkRange::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Airfield Pairs';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...self::squawkRangeInputs(),
                TextInput::make('origin')
                    ->label(self::translateFormPath('origin.label'))
                    ->helperText(self::translateFormPath('origin.helper'))
                    ->required()
                    ->rule(new PartialAirfieldIcao),
                TextInput::make('destination')
                    ->label(self::translateFormPath('destination.label'))
                    ->helperText(self::translateFormPath('destination.helper'))
                    ->required()
                    ->rule(new PartialAirfieldIcao),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...self::squawkRangeTableColumns(),
                Tables\Columns\TextColumn::make('origin')
                    ->label(self::translateTablePath('columns.origin')),
                Tables\Columns\TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAirfieldPairingSquawkRange::route('/'),
        ];
    }

    /**
     * Returns the root of the translation path for the relations manager, to build
     * labels etc.
     *
     * @return string
     */
    protected static function translationPathRoot(): string
    {
        return 'squawks.airfield_pairs';
    }
}
