<?php

namespace App\Filament\Resources\NavaidResource\RelationManagers;

use App\Models\Airfield\Airfield;
use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use App\Models\Runway\Runway;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class HoldsRelationManager extends RelationManager
{
    protected static string $relationship = 'holds';
    protected static ?string $recordTitleAttribute = 'description';
    protected static ?string $title = 'Published Holds';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Parameters')->schema([
                    Forms\Components\TextInput::make('description')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('inbound_heading')
                        ->required()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(360),
                    Forms\Components\TextInput::make('minimum_altitude')
                        ->required()
                        ->integer()
                        ->minValue(1000)
                        ->maxValue(60000),
                    Forms\Components\TextInput::make('maximum_altitude')
                        ->required()
                        ->integer()
                        ->minValue(1000)
                        ->maxValue(60000),
                    Forms\Components\Select::make('turn_direction')
                        ->required()
                        ->options([
                            'left' => 'Left',
                            'right' => 'Right',
                        ]),
                ]),
                Forms\Components\Fieldset::make('Restrictions')->schema([
                    Forms\Components\Builder::make('restrictions')
                        ->createItemButtonLabel(__('Add restriction'))
                        ->columnSpan('full')
                        ->inset()
                        ->blocks([
                            Forms\Components\Builder\Block::make('minimum-level')
                                ->label('Minimum Level')
                                ->schema([
                                    Forms\Components\Hidden::make('id'),
                                    Forms\Components\Select::make('level')
                                        ->required()
                                        ->options([
                                            'MSL' => 'MSL',
                                            'MSL+1' => 'MSL + 1',
                                            'MSL+2' => 'MSL + 2',
                                        ]),
                                    Forms\Components\Select::make('target')
                                        ->required()
                                        ->searchable()
                                        ->options(
                                            Airfield::all()
                                                ->mapWithKeys(
                                                    fn(Airfield $airfield) => [$airfield->code => $airfield->code]
                                                ),
                                        )
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function (Closure $get, Closure $set) {
                                            $target = $get('target');
                                            if (!$target || !$get('runway.designator')) {
                                                $set('runway.designator', null);
                                                return;
                                            }

                                            if (Runway::atAirfield($target)->where(
                                                'identifier',
                                                $get('runway.designator')
                                            )->exists()
                                            ) {
                                                return;
                                            }

                                            $set('runway.designator', null);
                                        }),
                                    Forms\Components\TextInput::make('override')
                                        ->integer()
                                        ->minValue(1000)
                                        ->maxValue(60000),
                                    Forms\Components\Select::make('runway.designator')
                                        ->options(
                                            fn(Closure $get) => $get('target')
                                                ? Runway::atAirfield($get('target'))->get()->mapWithKeys(
                                                    fn(Runway $runway) => [$runway->identifier => $runway->identifier]
                                                )
                                                : []
                                        ),
                                ]),
                            Forms\Components\Builder\Block::make('level-block')
                                ->label('Blocked Level')
                                ->schema([
                                    Forms\Components\Repeater::make('levels')
                                        ->schema([
                                            Forms\Components\TextInput::make('level')
                                                ->integer()
                                                ->minValue(1000)
                                                ->maxValue(60000)
                                                ->required(),
                                        ]),
                                ]),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('table.navaids.published_holds.columns.description')),
                Tables\Columns\TextColumn::make('inbound_heading')
                    ->label(__('table.navaids.published_holds.columns.heading')),
                Tables\Columns\TextColumn::make('minimum_altitude')
                    ->label(__('table.navaids.published_holds.columns.minimum_altitude')),
                Tables\Columns\TextColumn::make('maximum_altitude')
                    ->label(__('table.navaids.published_holds.columns.maximum_altitude')),
                Tables\Columns\TextColumn::make('turn_direction')
                    ->enum([
                        'left' => 'Left',
                        'right' => 'Right',
                    ])
                    ->label(__('table.navaids.published_holds.columns.turn_direction')),
                Tables\Columns\BooleanColumn::make('restrictions')
                    ->label(__('table.navaids.published_holds.columns.has_restrictions'))
                    ->getStateUsing(fn(Hold $record) => $record->restrictions->isNotEmpty()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, HoldsRelationManager $livewire) {
                        $hold = null;
                        DB::transaction(function () use (&$hold, $data, $livewire) {
                            $hold = $livewire->getOwnerRecord()->holds()->save(
                                new Hold([
                                    'description' => $data['description'],
                                    'inbound_heading' => $data['inbound_heading'],
                                    'minimum_altitude' => $data['minimum_altitude'],
                                    'maximum_altitude' => $data['maximum_altitude'],
                                    'turn_direction' => $data['turn_direction'],
                                ])
                            );
                            $hold->restrictions()->saveMany(
                                array_map(
                                    function (array $restriction) {
                                        return new HoldRestriction([
                                            'restriction' => self::formatRestrictionData($restriction),
                                        ]);
                                    },
                                    $data['restrictions']
                                )
                            );
                        });

                        return $hold;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (Hold $record, array $data) {
                        $data['restrictions'] = $record->restrictions->map(
                            fn(HoldRestriction $restriction) => match ($restriction->restriction['type']) {
                                'minimum-level' => [
                                    'type' => $restriction->restriction['type'],
                                    'data' => [
                                        ...$restriction->restriction,
                                        'id' => $restriction->id,
                                        'runway' => [
                                            'designator' => isset($restriction->restriction['runway']) ? $restriction->restriction['runway']['designator'] : null,
                                        ],
                                    ],
                                ],
                                'level-block' => [
                                    'id' => $restriction->id,
                                    'type' => $restriction->restriction['type'],
                                    'data' => [
                                        'levels' => collect($restriction->restriction['levels'])
                                            ->map(fn(int $level) => ['level' => $level])
                                            ->toArray(),
                                    ],
                                ]
                            }
                        )->toArray();
                        return $data;
                    })->using(function (array $data, Hold $record) {
                        DB::transaction(function () use ($data, $record) {
                            $restrictions = $data['restrictions'];
                            unset($data['restrictions']);

                            $record->update($data);

                            $restrictionIds = array_map(
                                fn(array $restriction) => $restriction['data']['id'],
                                array_filter(
                                    $restrictions,
                                    fn(array $restriction) => isset($restriction['data']['id'])
                                ),
                            );

                            // Remove restrictions we don't need
                            $restrictionsToRemove = $record->restrictions->filter(
                                function (HoldRestriction $restriction) use ($restrictionIds) {
                                    return array_search($restriction->id, $restrictionIds) === false;
                                }
                            );
                            foreach ($restrictionsToRemove as $restriction) {
                                $record->restrictions()->delete($restriction);
                            }

                            // Update existing restrictions
                            $restrictionsToUpdate = array_filter(
                                $restrictions,
                                fn(array $restriction) => isset($restriction['data']['id'])
                            );
                            foreach ($restrictionsToUpdate as $restriction) {
                                $model = HoldRestriction::findOrFail($restriction['data']['id']);
                                $model->restriction = self::formatRestrictionData($restriction);
                                $model->save();
                            }

                            // Save new restrictions
                            $restrictionsToSave = array_filter(
                                $restrictions,
                                fn(array $restriction) => !isset($restriction['data']['id'])
                            );
                            $record->restrictions()->saveMany(
                                array_map(
                                    fn(array $restriction) => new HoldRestriction(
                                        ['restriction' => self::formatRestrictionData($restriction)]
                                    ),
                                    $restrictionsToSave
                                )
                            );
                        });
                    }),
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
            'override' => is_null(
                $restriction['data']['override']
            ) ? null : (int)$restriction['data']['override'],
        ];

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
                fn(array $level) => (int)$level['level'],
                $restriction['data']['levels']
            ),
        ];
    }
}
