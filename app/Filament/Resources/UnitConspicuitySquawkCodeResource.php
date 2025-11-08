<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Helpers\HasUnitSquawkRangeRules;
use App\Filament\Resources\UnitConspicuitySquawkCodeResource\Pages\ManageUnitConspicuitySquawkCodes;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits\MutatesRuleData;
use App\Models\Squawk\UnitConspicuity\UnitConspicuitySquawkCode;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class UnitConspicuitySquawkCodeResource extends Resource
{
    use HasSquawkRanges;
    use HasUnitSquawkRangeRules;
    use MutatesRuleData;
    use TranslatesStrings;

    protected static ?string $model = UnitConspicuitySquawkCode::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Unit Conspicuity';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('unit')
                    ->label(self::translateFormPath('unit.label'))
                    ->helperText(self::translateFormPath('unit.helper'))
                    ->required()
                    ->maxLength(255),
                self::singleSquawkInput('code', 'code'),
                ...self::unitSquawkRangeRules(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->label(self::translateTablePath('columns.unit')),
                self::squawkCodeTableColumn(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(self::mutateFormData())
                    ->mutateRecordDataUsing(self::mutateRecordData()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('code', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUnitConspicuitySquawkCodes::route('/'),
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
        return 'squawks.unit_conspicuity';
    }
}
