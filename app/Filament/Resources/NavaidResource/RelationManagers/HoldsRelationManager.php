<?php

namespace App\Filament\Resources\NavaidResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Airfield\Airfield;
use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use App\Models\Measurement\MeasurementUnit;
use App\Models\Runway\Runway;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HoldsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;

    use TranslatesStrings;

    protected static string $relationship = 'holds';
    protected static ?string $recordTitleAttribute = 'description';
    protected static ?string $title = 'Published Holds';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Parameters')
                    ->label(self::translateFormPath('parameters.label'))
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->label(self::translateFormPath('description.label'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('inbound_heading')
                            ->label(self::translateFormPath('inbound_heading.label'))
                            ->helperText(self::translateFormPath('inbound_heading.helper'))
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(360),
                        Forms\Components\TextInput::make('minimum_altitude')
                            ->label(self::translateFormPath('minimum_altitude.label'))
                            ->helperText(self::translateFormPath('minimum_altitude.helper'))
                            ->required()
                            ->integer()
                            ->step(100)
                            ->minValue(1000)
                            ->maxValue(60000),
                        Forms\Components\TextInput::make('maximum_altitude')
                            ->label(self::translateFormPath('maximum_altitude.label'))
                            ->helperText(self::translateFormPath('maximum_altitude.helper'))
                            ->required()
                            ->integer()
                            ->step(100)
                            ->minValue(2000)
                            ->maxValue(60000)
                            ->gte('minimum_altitude'),
                        Forms\Components\Select::make('turn_direction')
                            ->label(self::translateFormPath('turn_direction.label'))
                            ->required()
                            ->options([
                                'left' => 'Left',
                                'right' => 'Right',
                            ]),
                        Forms\Components\TextInput::make('outbound_leg_value')
                            ->label(self::translateFormPath('outbound_leg_value.label'))
                            ->helperText(self::translateFormPath('outbound_leg_value.helper'))
                            ->numeric()
                            ->reactive()
                            ->required(fn (\Filament\Forms\Get $get): bool => (bool) $get('outbound_leg_unit'))
                            ->minValue(0.5)
                            ->maxValue(100),
                        Forms\Components\Select::make('outbound_leg_unit')
                            ->label(self::translateFormPath('outbound_leg_unit.label'))
                            ->helperText(self::translateFormPath('outbound_leg_unit.helper'))
                            ->required(fn (\Filament\Forms\Get $get): bool => (bool) $get('outbound_leg_value'))
                            ->reactive()
                            ->options(
                                MeasurementUnit::whereIn('unit', ['nm', 'min'])
                                    ->get()
                                    ->mapWithKeys(
                                        fn (MeasurementUnit $unit): array => [$unit->id => $unit->description]
                                    )
                            ),
                    ]),
                Forms\Components\Fieldset::make('Restrictions')
                    ->label(self::translateFormPath('restrictions.label'))
                    ->schema([
                        Forms\Components\Builder::make('restrictions')
                            ->createItemButtonLabel(self::translateFormPath('add_restriction.label'))
                            ->columnSpan('full')
                            ->inset()
                            ->blocks([
                                Forms\Components\Builder\Block::make('minimum-level')
                                    ->label(self::translateFormPath('minimum_level.label'))
                                    ->schema([
                                        Forms\Components\Hidden::make('id'),
                                        Forms\Components\Select::make('level')
                                            ->label(self::translateFormPath('minimum_level_level.label'))
                                            ->required()
                                            ->options([
                                                'MSL' => 'MSL',
                                                'MSL+1' => 'MSL + 1',
                                                'MSL+2' => 'MSL + 2',
                                            ]),
                                        Forms\Components\Select::make('target')
                                            ->label(self::translateFormPath('minimum_level_target.label'))
                                            ->helperText(self::translateFormPath('minimum_level_target.helper'))
                                            ->required()
                                            ->searchable()
                                            ->options(
                                                Airfield::all()
                                                    ->mapWithKeys(
                                                        fn (Airfield $airfield) => [$airfield->code => $airfield->code]
                                                    ),
                                            )
                                            ->preload()
                                            ->reactive()
                                            ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set) {
                                                $target = $get('target');
                                                if (!$target || !$get('runway.designator')) {
                                                    $set('runway.designator', null);
                                                    return;
                                                }

                                                if (
                                                    Runway::atAirfield($target)->where(
                                                        'identifier',
                                                        $get('runway.designator')
                                                    )->exists()
                                                ) {
                                                    return;
                                                }

                                                $set('runway.designator', null);
                                            }),
                                        Forms\Components\TextInput::make('override')
                                            ->label(self::translateFormPath('minimum_level_override.label'))
                                            ->helperText(self::translateFormPath('minimum_level_override.helper'))
                                            ->integer()
                                            ->minValue(1000)
                                            ->maxValue(60000),
                                        Forms\Components\Select::make('runway.designator')
                                            ->label(self::translateFormPath('minimum_level_runway.label'))
                                            ->helperText(self::translateFormPath('minimum_level_runway.helper'))
                                            ->options(
                                                fn (\Filament\Forms\Get $get) => $get('target')
                                                ? Runway::atAirfield($get('target'))->get()->mapWithKeys(
                                                    fn (Runway $runway) => [$runway->identifier => $runway->identifier]
                                                )
                                                : []
                                            ),
                                    ]),
                                Forms\Components\Builder\Block::make('level-block')
                                    ->label(self::translateFormPath('level_block.label'))
                                    ->schema([
                                        Forms\Components\Repeater::make('levels')
                                            ->label(self::translateFormPath('level_block_levels.label'))
                                            ->helperText(self::translateFormPath('level_block_levels.helper'))
                                            ->schema([
                                                Forms\Components\TextInput::make('level')
                                                    ->integer()
                                                    ->step(100)
                                                    ->minValue(1000)
                                                    ->maxValue(60000)
                                                    ->required(),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
                Tables\Columns\TextColumn::make('inbound_heading')
                    ->label(self::translateTablePath('columns.heading')),
                Tables\Columns\TextColumn::make('minimum_altitude')
                    ->label(self::translateTablePath('columns.minimum_altitude')),
                Tables\Columns\TextColumn::make('maximum_altitude')
                    ->label(self::translateTablePath('columns.maximum_altitude')),
                Tables\Columns\TextColumn::make('turn_direction')
                    ->formatStateUsing(
                        fn (string $state) => Str::ucfirst($state))
                    ->label(self::translateTablePath('columns.turn_direction')),
                TextColumn::make('outbound_leg')
                    ->label(self::translateTablePath('columns.outbound_leg'))
                    ->formatStateUsing(
                        fn (Hold $record) => $record->outbound_leg_unit
                        ? sprintf('%s %s', $record->outbound_leg_value, $record->outboundLegUnit->description)
                        : '--'
                    ),
                Tables\Columns\BooleanColumn::make('restrictions')
                    ->label(self::translateTablePath('columns.has_restrictions'))
                    ->getStateUsing(fn (Hold $record) => $record->restrictions->isNotEmpty()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(
                        fn (array $data, HoldsRelationManager $livewire): Hold => self::saveNewHold($data, $livewire)
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->mutateRecordDataUsing(fn (Hold $record, array $data) => self::mutateRecordData($record, $data)),
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(fn (Hold $record, array $data) => self::mutateRecordData($record, $data))
                    ->using(fn (Hold $record, array $data) => self::saveUpdatedHold($data, $record)),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    private static function formatRestrictionData(array $formData): array
    {
        return match ($formData['type']) {
            'minimum-level' => self::formatMinimumLevelRestriction($formData),
            'level-block' => self::formatBlockedLevelRestriction($formData),
        };
    }

    private static function formatMinimumLevelRestriction(array $restriction): array
    {
        $data = [
            'type' => $restriction['type'],
            'level' => $restriction['data']['level'],
            'target' => $restriction['data']['target'],
        ];

        if (isset($restriction['data']['override'])) {
            $data['override'] = (int) $restriction['data']['override'];
        }

        if (isset($restriction['data']['runway']['designator'])) {
            $data['runway'] = [
                'designator' => $restriction['data']['runway']['designator'],
                'type' => 'any',
            ];
        }

        return $data;
    }

    private static function formatBlockedLevelRestriction(array $restriction): array
    {
        return [
            'type' => $restriction['type'],
            'levels' => array_map(
                fn (array $level) => (int) $level['level'],
                $restriction['data']['levels']
            ),
        ];
    }

    private static function mutateRecordData(Hold $record, array $data): array
    {
        $data['restrictions'] = $record->restrictions->map(
            fn (HoldRestriction $restriction) => match ($restriction->restriction['type']) {
                'minimum-level' => [
                    'type' => $restriction->restriction['type'],
                    'data' => [
                        ...$restriction->restriction,
                        'id' => $restriction->id,
                        'runway' => [
                            'designator' => isset($restriction->restriction['runway'])
                            ? $restriction->restriction['runway']['designator']
                            : null,
                        ],
                    ],
                ],
                'level-block' => [
                    'type' => $restriction->restriction['type'],
                    'data' => [
                        'id' => $restriction->id,
                        'levels' => collect($restriction->restriction['levels'])
                            ->map(fn (int $level) => ['level' => $level])
                            ->toArray(),
                    ],
                ]
            }
        )->toArray();

        return $data;
    }

    private static function saveUpdatedHold(array $data, Hold $record): void
    {
        DB::transaction(function () use ($data, $record) {
            $restrictions = $data['restrictions'];
            unset($data['restrictions']);

            $record->update($data);

            $restrictionIds = array_map(
                fn (array $restriction) => $restriction['data']['id'],
                array_filter(
                    $restrictions,
                    fn (array $restriction) => isset($restriction['data']['id'])
                ),
            );

            // Remove restrictions we don't need
            HoldRestriction::whereIn(
                'id',
                $record->restrictions->filter(
                    function (HoldRestriction $restriction) use ($restrictionIds) {
                        return array_search($restriction->id, $restrictionIds) === false;
                    }
                )->pluck('id')
            )->delete();

            // Update existing restrictions
            $restrictionsToUpdate = array_filter(
                $restrictions,
                fn (array $restriction) => isset($restriction['data']['id'])
            );
            foreach ($restrictionsToUpdate as $restriction) {
                $model = HoldRestriction::findOrFail($restriction['data']['id']);
                $model->restriction = self::formatRestrictionData($restriction);
                $model->save();
            }

            // Save new restrictions
            static::saveNewRestrictions(
                $record,
                array_filter(
                    $restrictions,
                    fn (array $restriction) => !isset($restriction['data']['id'])
                )
            );
        });
    }

    private static function saveNewHold(array $data, HoldsRelationManager $livewire): Hold
    {
        $hold = null;
        DB::transaction(function () use (&$hold, $data, $livewire) {
            $hold = $livewire->getOwnerRecord()->holds()->save(
                new Hold([
                    'description' => $data['description'],
                    'inbound_heading' => $data['inbound_heading'],
                    'minimum_altitude' => $data['minimum_altitude'],
                    'maximum_altitude' => $data['maximum_altitude'],
                    'turn_direction' => $data['turn_direction'],
                    'outbound_leg_value' => $data['outbound_leg_value'],
                    'outbound_leg_unit' => $data['outbound_leg_unit'],
                ])
            );

            static::saveNewRestrictions($hold, $data['restrictions']);
        });

        return $hold;
    }

    private static function saveNewRestrictions(Hold $hold, array $restrictions): void
    {
        $hold->restrictions()->saveMany(
            array_map(
                function (array $restriction) {
                    return new HoldRestriction([
                        'restriction' => self::formatRestrictionData($restriction),
                    ]);
                },
                $restrictions
            )
        );
    }

    protected static function translationPathRoot(): string
    {
        return 'holds';
    }
}
