<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StandResource\Pages;
use App\Filament\Resources\StandResource\RelationManagers;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Rules\Stand\StandIdentifierMustBeUniqueAtAirfield;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class StandResource extends Resource
{
    protected static ?string $model = Stand::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $recordTitleAttribute = 'identifier';

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
                            ->label(__('form.stands.airfield.label'))
                            ->helperText(__('Required'))
                            ->hintIcon('heroicon-o-folder')
                            ->options(
                                fn () => Airfield::all()
                                    ->sortBy('code', SORT_NATURAL)
                                    ->mapWithKeys(fn (Airfield $airfield) => [$airfield->id => $airfield->code])
                            )
                            ->reactive()
                            ->afterStateUpdated(function (Closure $get, Closure $set) {
                                $terminalId = $get('terminal_id');
                                if ($terminalId && Terminal::find($terminalId)->airfield_id === $get('airfield_id')) {
                                    return;
                                }

                                $set('terminal_id', null);
                            })
                            ->preload()
                            ->searchable(!App::runningUnitTests())
                            ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                            ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                            ->required(),
                        Select::make('terminal_id')
                            ->label(__('form.stands.terminal.label'))
                            ->helperText(__('form.stands.terminal.helper'))
                            ->hintIcon('heroicon-o-folder')
                            ->options(
                                fn (Closure $get) => Terminal::where('airfield_id', $get('airfield_id'))
                                    ->get()
                                    ->mapWithKeys(
                                        fn (Terminal $terminal) => [$terminal->id => $terminal->description]
                                    )
                            )
                            ->disabled(
                                fn (Page $livewire, Closure $get) => !$livewire instanceof CreateRecord ||
                                    !Terminal::where('airfield_id', $get('airfield_id'))->exists()
                            )
                            ->dehydrated(
                                fn (Page $livewire, Closure $get) => !$livewire instanceof CreateRecord ||
                                    !Terminal::where('airfield_id', $get('airfield_id'))->exists()
                            ),
                        TextInput::make('identifier')
                            ->label(__('form.stands.identifier.label'))
                            ->maxLength(255)
                            ->helperText(__('form.stands.identifier.helper'))
                            ->required()
                            ->rule(
                                fn (Closure $get, ?Model $record) => new StandIdentifierMustBeUniqueAtAirfield(
                                    Airfield::findOrFail($get('airfield_id')),
                                    $record
                                ),
                                fn (Closure $get) => $get('airfield_id')
                            ),
                        Select::make('type_id')
                            ->label(__('form.stands.type.label'))
                            ->helperText(__('form.stands.type.helper'))
                            ->hintIcon('heroicon-o-folder')
                            ->options(
                                fn () => StandType::all()->mapWithKeys(
                                    fn (StandType $type) => [$type->id => ucfirst(strtolower($type->key))]
                                )
                            ),
                        TextInput::make('latitude')
                            ->label(__('form.stands.latitude.label'))
                            ->helperText(__('form.stands.latitude.helper'))
                            ->numeric('decimal')
                            ->required(),
                        TextInput::make('longitude')
                            ->label(__('form.stands.longitude.label'))
                            ->helperText(__('form.stands.longitude.helper'))
                            ->numeric('decimal')
                            ->required(),
                    ]
                ),
                Fieldset::make('Allocation')->schema(
                    [
                        Select::make('wake_category_id')
                            ->label(__('form.stands.wake_category.label'))
                            ->helperText(__('form.stands.wake_category.helper'))
                            ->hintIcon('heroicon-o-scale')
                            ->options(
                                fn () => WakeCategoryScheme::with('categories')
                                    ->uk()
                                    ->firstOrFail()
                                    ->categories
                                    ->sortBy('relative_weighting')
                                    ->mapWithKeys(
                                        fn (WakeCategory $category) => [
                                            $category->id => sprintf(
                                                '%s (%s)',
                                                $category->description,
                                                $category->code
                                            ),
                                        ]
                                    )
                            )
                            ->required(),
                        Select::make('max_aircraft_id')
                            ->label(__('form.stands.aircraft_type.label'))
                            ->helperText(__('form.stands.aircraft_type.helper'))
                            ->hintIcon('heroicon-o-paper-airplane')
                            ->options(
                                fn () => Aircraft::all()->mapWithKeys(
                                    fn (Aircraft $aircraft) => [$aircraft->id => $aircraft->code]
                                )
                            )
                            ->searchable(!App::runningUnitTests()),
                        Toggle::make('closed_at')
                            ->label(__('form.stands.used_for_allocation.label'))
                            ->helperText(__('form.stands.used_for_allocation.helper'))
                            ->default(true)
                            ->afterStateHydrated(static function (Toggle $component, $state): void {
                                $component->state(is_null($state));
                            })
                            ->required(),
                        TextInput::make('assignment_priority')
                            ->label(__('form.stands.allocation_priority.label'))
                            ->helperText(__('form.stands.allocation_priority.helper'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->default(100)
                            ->required(),
                    ]
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('table.stands.columns.id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(__('table.stands.columns.airfield'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('terminal.description')
                    ->label(__('table.stands.columns.terminal'))
                    ->default('--')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('table.stands.columns.identifier'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TagsColumn::make('uniqueAirlines.icao_code')
                    ->label(__('table.stands.columns.airlines'))
                    ->default(['--'])
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('closed_at')
                    ->label(__('table.stands.columns.airfield'))
                    ->getStateUsing(function (Tables\Columns\BooleanColumn $column) {
                        return $column->getRecord()->closed_at === null;
                    }),
                Tables\Columns\TextColumn::make('assignment_priority')
                    ->label(__('table.stands.columns.priority'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignedCallsign')
                    ->label(__('table.stands.columns.allocation'))
                    ->default('--'),
            ])->defaultSort('airfield.code')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('airfield.code');
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
}
