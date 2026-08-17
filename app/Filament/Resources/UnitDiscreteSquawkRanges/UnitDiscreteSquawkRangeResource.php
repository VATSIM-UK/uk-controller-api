<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRanges;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Helpers\HasUnitSquawkRangeRules;
use App\Filament\Resources\TranslatesStrings;
use App\Filament\Resources\UnitDiscreteSquawkRanges\Pages\ManageUnitDiscreteSquawkRanges;
use App\Filament\Resources\UnitDiscreteSquawkRanges\Traits\MutatesRuleData;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitDiscreteSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use HasUnitSquawkRangeRules;
    use MutatesRuleData;
    use TranslatesStrings;

    protected static ?string $model = UnitDiscreteSquawkRange::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Squawk Ranges';

    protected static ?string $navigationLabel = 'Unit Discrete';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unit')
                    ->label(self::translateFormPath('unit.label'))
                    ->helperText(self::translateFormPath('unit.helper'))
                    ->required()
                    ->maxLength(255),
                ...self::squawkRangeInputs(),
                ...self::unitSquawkRangeRules(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit')
                    ->label(self::translateTablePath('columns.unit')),
                TextColumn::make('first')
                    ->label(self::translateTablePath('columns.first')),
                TextColumn::make('last')
                    ->label(self::translateTablePath('columns.last'))
                    ->toggleable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(self::mutateFormData())
                    ->mutateRecordDataUsing(self::mutateRecordData()),
                DeleteAction::make(),
            ])
            ->defaultSort('first', 'asc');
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
     */
    protected static function translationPathRoot(): string
    {
        return 'squawks.unit_discrete';
    }
}
