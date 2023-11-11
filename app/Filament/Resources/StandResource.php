<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\StandResource\Pages;
use App\Filament\Resources\StandResource\RelationManagers;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Rules\Airfield\PartialAirfieldIcao;
use App\Rules\Stand\StandIdentifierMustBeUniqueAtAirfield;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class StandResource extends Resource
{
    use TranslatesStrings;

    private const DEFAULT_COLUMN_VALUE = '--';

    protected static ?string $model = Stand::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationGroup = 'Airfield';

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['airlines']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Identifiers')->schema(
                    [
                        Select::make('airfield_id')
                            ->label(self::translateFormPath('airfield.label'))
                            ->helperText(__('Required'))
                            ->hintIcon('heroicon-o-folder')
                            ->options(SelectOptions::airfields())
                            ->reactive()
                            ->afterStateUpdated(function (Closure $get, Closure $set) {
                                $terminalId = $get('terminal_id');
                                if ($terminalId && Terminal::find($terminalId)->airfield_id === $get('airfield_id')) {
                                    return;
                                }

                                $set('terminal_id', null);
                            })
                            ->searchable(!App::runningUnitTests())
                            ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                            ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                            ->required(),
                        Select::make('terminal_id')
                            ->label(self::translateFormPath('terminal.label'))
                            ->helperText(self::translateFormPath('terminal.helper'))
                            ->hintIcon('heroicon-o-folder')
                            ->options(
                                fn (Closure $get) => Terminal::where('airfield_id', $get('airfield_id'))
                                    ->get()
                                    ->mapWithKeys(
                                        fn (Terminal $terminal) => [$terminal->id => $terminal->description]
                                    )
                            )
                            ->disabled(
                                fn (Page $livewire, Closure $get) => !Terminal::where(
                                    'airfield_id',
                                    $get('airfield_id')
                                )->exists()
                            )
                            ->dehydrated(
                                fn (Page $livewire, Closure $get) => Terminal::where(
                                    'airfield_id',
                                    $get('airfield_id')
                                )->exists()
                            ),
                        TextInput::make('identifier')
                            ->label(self::translateFormPath('identifier.label'))
                            ->maxLength(255)
                            ->helperText(self::translateFormPath('identifier.helper'))
                            ->required()
                            ->rule(
                                fn (Closure $get, ?Model $record) => new StandIdentifierMustBeUniqueAtAirfield(
                                    Airfield::findOrFail($get('airfield_id')),
                                    $record
                                ),
                                fn (Closure $get) => $get('airfield_id')
                            ),
                        Select::make('type_id')
                            ->label(self::translateFormPath('type.label'))
                            ->helperText(self::translateFormPath('type.helper'))
                            ->hintIcon('heroicon-o-folder')
                            ->options(
                                fn () => StandType::all()->mapWithKeys(
                                    fn (StandType $type) => [$type->id => ucfirst(strtolower($type->key))]
                                )
                            ),
                        TextInput::make('latitude')
                            ->label(self::translateFormPath('latitude.label'))
                            ->helperText(self::translateFormPath('latitude.helper'))
                            ->numeric('decimal')
                            ->required(),
                        TextInput::make('longitude')
                            ->label(self::translateFormPath('longitude.label'))
                            ->helperText(self::translateFormPath('longitude.helper'))
                            ->numeric('decimal')
                            ->required(),
                    ]
                ),
                Fieldset::make('Allocation')->schema(
                    [
                        Select::make('aerodrome_reference_code')
                            ->label(self::translateFormPath('aerodrome_reference_code.label'))
                            ->helperText(self::translateFormPath('aerodrome_reference_code.helper'))
                            ->hintIcon('heroicon-o-scale')
                            ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F'])
                            ->required(),
                        TextInput::make('max_aircraft_wingspan')
                            ->label(self::translateFormPath('aircraft_wingspan.label'))
                            ->helperText(self::translateFormPath('aircraft_wingspan.helper'))
                            ->hintIcon('heroicon-o-paper-airplane')
                            ->reactive()
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('max_aircraft_length')
                            ->label(self::translateFormPath('aircraft_length.label'))
                            ->helperText(self::translateFormPath('aircraft_length.helper'))
                            ->hintIcon('heroicon-o-paper-airplane')
                            ->reactive()
                            ->numeric()
                            ->minValue(1),
                        Toggle::make('closed_at')
                            ->label(self::translateFormPath('used_for_allocation.label'))
                            ->helperText(self::translateFormPath('used_for_allocation.helper'))
                            ->default(true)
                            ->afterStateHydrated(static function (Toggle $component, $state): void {
                                $component->state(is_null($state));
                            })
                            ->required(),
                        TextInput::make('assignment_priority')
                            ->label(self::translateFormPath('allocation_priority.label'))
                            ->helperText(self::translateFormPath('allocation_priority.helper'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->default(100)
                            ->required(),
                        TextInput::make('origin_slug')
                            ->label(self::translateFormPath('origin_slug.label'))
                            ->helperText(self::translateFormPath('origin_slug.helper'))
                            ->rule(new PartialAirfieldIcao()),
                    ]
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('terminal.description')
                    ->label(self::translateTablePath('columns.terminal'))
                    ->default(self::DEFAULT_COLUMN_VALUE),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('table.stands.columns.identifier'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('aerodrome_reference_code')
                    ->label(self::translateTablePath('columns.aerodrome_reference_code')),
                Tables\Columns\TextColumn::make('max_aircraft_wingspan')
                    ->label(self::translateTablePath('columns.max_size_wingspan'))
                    ->default(self::DEFAULT_COLUMN_VALUE),
                Tables\Columns\TextColumn::make('max_aircraft_length')
                    ->label(self::translateTablePath('columns.max_size_length'))
                    ->default(self::DEFAULT_COLUMN_VALUE),
                Tables\Columns\TagsColumn::make('uniqueAirlines.icao_code')
                    ->label(self::translateTablePath('columns.airlines'))
                    ->default([self::DEFAULT_COLUMN_VALUE]),
                Tables\Columns\BooleanColumn::make('closed_at')
                    ->label(self::translateTablePath('columns.used'))
                    ->getStateUsing(function (Tables\Columns\BooleanColumn $column) {
                        return $column->getRecord()->closed_at === null;
                    }),
                Tables\Columns\TextColumn::make('assignment_priority')
                    ->label(self::translateTablePath('columns.priority'))
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('airfield')
                    ->label(self::translateFilterPath('airfield'))
                    ->options(SelectOptions::airfields())
                    ->searchable()
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['value'])) {
                                return $query;
                            }

                            return $query->where('airfield_id', $data['value']);
                        }
                    ),
                Tables\Filters\MultiSelectFilter::make('airlines')
                    ->label(self::translateFilterPath('airlines'))
                    ->options(SelectOptions::airlines())
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['values'])) {
                                return $query;
                            }

                            return $query->whereHas(
                                'airlines',
                                function (Builder $query) use ($data) {
                                    return $query->whereIn('airlines.id', $data['values']);
                                }
                            );
                        }
                    ),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AirlinesRelationManager::class,
            RelationManagers\PairedStandsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStands::route('/'),
            'create' => Pages\CreateStand::route('/create'),
            'edit' => Pages\EditStand::route('/{record}/edit'),
            'view' => Pages\ViewStand::route('/{record}'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'stands';
    }
}
