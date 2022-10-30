<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Pages\ManageUnitDiscreteSquawkRanges;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UnitDiscreteSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = UnitDiscreteSquawkRange::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Unit Discrete';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('unit')
                    ->label(self::translateFormPath('unit.label'))
                    ->helperText(self::translateFormPath('unit.helper'))
                    ->required()
                    ->maxLength(4),
                ...self::squawkRangeInputs(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->label(self::translateTablePath('columns.unit')),
                Tables\Columns\TextColumn::make('first')
                    ->label(self::translateTablePath('columns.first')),
                Tables\Columns\TextColumn::make('last')
                    ->label(self::translateTablePath('columns.last')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUnitDiscreteSquawkRanges::route('/'),
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
        return 'squawks.unit_discrete';
    }
}
