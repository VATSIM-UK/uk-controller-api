<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\UnitConspicuitySquawkCodeResource\Pages\ManageUnitConspicuitySquawkCodes;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits\MutatesRuleData;
use App\Models\Squawk\UnitConspicuity\UnitConspicuitySquawkCode;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UnitConspicuitySquawkCodeResource extends Resource
{
    use MutatesRuleData;

    // Service types
    private const RULE_TYPE_UNIT_TYPE = 'UNIT_TYPE';
    private const RULE_TYPE_FLIGHT_RULES = 'FLIGHT_RULES';
    private const RULE_TYPE_SERVICE_TYPE = 'SERVICE';

    // The types of flight rule
    private const FLIGHT_RULES_VFR = 'VFR';
    private const FLIGHT_RULES_IFR = 'IFR';

    // The unit types
    private const UNIT_TYPE_DEL = 'DEL';
    private const UNIT_TYPE_GND = 'GND';
    private const UNIT_TYPE_TWR = 'TWR';
    private const UNIT_TYPE_APP = 'APP';
    private const UNIT_TYPE_CTR = 'CTR';

    // The rules
    private const SERVICE_TYPE_BASIC = 'BASIC';
    private const SERVICE_TYPE_TRAFFIC = 'TRAFFIC';
    private const SERVICE_TYPE_DECONFLICTION = 'DECONFLICTION';
    private const SERVICE_TYPE_PROCEDURAL = 'PROCEDURAL';

    use HasSquawkRanges;
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
                Select::make('flight_rules')
                    ->label(self::translateFormPath('rule_flight_rules.label'))
                    ->helperText(self::translateFormPath('rule_flight_rules.helper'))
                    ->options([
                        self::FLIGHT_RULES_IFR => self::FLIGHT_RULES_IFR,
                        self::FLIGHT_RULES_VFR => self::FLIGHT_RULES_VFR,
                    ]),
                Select::make('unit_type')
                    ->label(self::translateFormPath('rule_unit_type.label'))
                    ->helperText(self::translateFormPath('rule_unit_type.helper'))
                    ->options([
                        self::UNIT_TYPE_DEL => self::UNIT_TYPE_DEL,
                        self::UNIT_TYPE_GND => self::UNIT_TYPE_GND,
                        self::UNIT_TYPE_TWR => self::UNIT_TYPE_TWR,
                        self::UNIT_TYPE_APP => self::UNIT_TYPE_APP,
                        self::UNIT_TYPE_CTR => self::UNIT_TYPE_CTR,
                    ]),
                Select::make('service')
                    ->label(self::translateFormPath('rule_service.label'))
                    ->helperText(self::translateFormPath('rule_service.helper'))
                    ->options([
                        self::SERVICE_TYPE_BASIC => 'Basic',
                        self::SERVICE_TYPE_TRAFFIC => 'Traffic',
                        self::SERVICE_TYPE_DECONFLICTION => 'Deconfliction',
                        self::SERVICE_TYPE_PROCEDURAL => 'Procedural',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->label(self::translateTablePath('columns.unit')),
                Tables\Columns\TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(self::mutateFormData())
                    ->mutateRecordDataUsing(function (array $data): array {
                        if (!$data['rules']) {
                            return $data;
                        }

                        foreach ($data['rules'] as $rule) {
                            $dataKey = match ($rule['type']) {
                                self::RULE_TYPE_UNIT_TYPE => 'unit_type',
                                self::RULE_TYPE_FLIGHT_RULES => 'flight_rules',
                                self::RULE_TYPE_SERVICE_TYPE => 'service',
                            };
                            $data[$dataKey] = $rule['rule'];
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
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
