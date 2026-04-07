<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\AirfieldPairingSquawkRangeResource\Pages\ManageAirfieldPairingSquawkRange;
use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\AirfieldPairingSquawkRangeResource\Pages;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Rules\Airfield\PartialAirfieldIcao;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class AirfieldPairingSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = AirfieldPairingSquawkRange::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Airfield Pairs';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wifi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('origin')
                    ->label(self::translateTablePath('columns.origin')),
                TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('first', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAirfieldPairingSquawkRange::route('/'),
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
