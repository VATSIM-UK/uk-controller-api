<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Pages\ManageUnitDiscreteSquawkRanges;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits\MutatesRuleData;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UnitDiscreteSquawkRangeResource extends Resource
{
    use MutatesRuleData;

    // The types of rules
    public const RULE_TYPE_FLIGHT_RULES = 'FLIGHT_RULES';
    public const RULE_TYPE_UNIT_TYPE = 'UNIT_TYPE';

    // The types of flight rules
    private const FLIGHT_RULES_VFR = 'VFR';
    private const FLIGHT_RULES_IFR = 'IFR';

    // The unit types
    private const UNIT_TYPE_DEL = 'DEL';
    private const UNIT_TYPE_GND = 'GND';
    private const UNIT_TYPE_TWR = 'TWR';
    private const UNIT_TYPE_APP = 'APP';
    private const UNIT_TYPE_CTR = 'CTR';

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
                    ->maxLength(255),
                ...self::squawkRangeInputs(),
                Select::make('rule_type')
                    ->label(self::translateFormPath('rule_type.label'))
                    ->helperText(self::translateFormPath('rule_type.helper'))
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set): void {
                        $set('flight_rules', null);
                        $set('unit_type', null);
                    })
                    ->options([
                        self::RULE_TYPE_FLIGHT_RULES => 'Flight Rules',
                        self::RULE_TYPE_UNIT_TYPE => 'Unit Type',
                    ]),
                Select::make('flight_rules')
                    ->label(self::translateFormPath('rule_flight_rules.label'))
                    ->helperText(self::translateFormPath('rule_flight_rules.helper'))
                    ->required(fn(Closure $get) => $get('rule_type') === self::RULE_TYPE_FLIGHT_RULES)
                    ->visible(fn(Closure $get) => $get('rule_type') === self::RULE_TYPE_FLIGHT_RULES)
                    ->options([
                        self::FLIGHT_RULES_IFR => self::FLIGHT_RULES_IFR,
                        self::FLIGHT_RULES_VFR => self::FLIGHT_RULES_VFR,
                    ]),
                Select::make('unit_type')
                    ->label(self::translateFormPath('rule_unit_type.label'))
                    ->helperText(self::translateFormPath('rule_unit_type.helper'))
                    ->required(fn(Closure $get) => $get('rule_type') === self::RULE_TYPE_UNIT_TYPE)
                    ->visible(fn(Closure $get) => $get('rule_type') === self::RULE_TYPE_UNIT_TYPE)
                    ->options([
                        self::UNIT_TYPE_DEL => self::UNIT_TYPE_DEL,
                        self::UNIT_TYPE_GND => self::UNIT_TYPE_GND,
                        self::UNIT_TYPE_TWR => self::UNIT_TYPE_TWR,
                        self::UNIT_TYPE_APP => self::UNIT_TYPE_APP,
                        self::UNIT_TYPE_CTR => self::UNIT_TYPE_CTR,
                    ]),
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
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(self::mutateFormData())
                    ->mutateRecordDataUsing(function (array $data): array {
                        if (!$data['rule']) {
                            return $data;
                        }

                        $data['rule_type'] = $data['rule']['type'];
                        $data[$data['rule']['type'] === self::RULE_TYPE_UNIT_TYPE ? 'unit_type' : 'flight_rules'] = $data['rule']['rule'];

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
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
