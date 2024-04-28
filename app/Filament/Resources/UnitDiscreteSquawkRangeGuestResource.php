<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource\Pages\ManageUnitDiscreteSquawkRangeGuests;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class UnitDiscreteSquawkRangeGuestResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = UnitDiscreteSquawkRangeGuest::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Unit Discrete Guests';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('primary_unit')
                    ->label(self::translateFormPath('primary.label'))
                    ->helperText(self::translateFormPath('primary.helper'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('guest_unit')
                    ->label(self::translateFormPath('guest.label'))
                    ->helperText(self::translateFormPath('guest.helper'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('primary_unit')
                    ->label(self::translateTablePath('columns.primary_unit')),
                Tables\Columns\TextColumn::make('guest_unit')
                    ->label(self::translateTablePath('columns.guest_unit')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUnitDiscreteSquawkRangeGuests::route('/'),
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
        return 'squawks.unit_discrete_guests';
    }
}
