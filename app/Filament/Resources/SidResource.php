<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SidResource\Pages;
use App\Filament\Resources\SidResource\RelationManagers;
use App\Models\Airfield\Airfield;
use App\Models\Controller\Handoff;
use App\Models\Runway\Runway;
use App\Models\Sid;
use App\Rules\Sid\SidIdentifiersMustBeUniqueForRunway;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SidResource extends Resource
{
    protected static ?string $model = Sid::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $recordRouteKeyName = 'sid.id';
    protected static ?string $recordTitleAttribute = 'identifier';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('runway_id')
                    ->label(__('form.sids.runway.label'))
                    ->helperText(__('form.sids.runway.helper'))
                    ->hintIcon('heroicon-o-chevron-double-up')
                    ->options(
                        fn() => Runway::with('airfield')
                            ->get()
                            ->mapWithKeys(
                                fn(Runway $runway) => [
                                    $runway->id => sprintf('%s - %s', $runway->airfield->code, $runway->identifier),
                                ]
                            ),
                    )
                    ->disabled(fn(Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn(Page $livewire) => $livewire instanceof CreateRecord)
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('identifier')
                    ->label(__('form.sids.identifier.label'))
                    ->helperText(__('form.sids.identifier.helper'))
                    ->rule(
                        fn(Closure $get, ?Model $record) => new SidIdentifiersMustBeUniqueForRunway(
                            Runway::findOrFail($get('runway_id')),
                            $record
                        ),
                        fn(Closure $get) => $get('runway_id')
                    )
                    ->required(),
                Forms\Components\TextInput::make('initial_altitude')
                    ->label(__('form.sids.initial_altitude.label'))
                    ->helperText(__('form.sids.initial_altitude.helper'))
                    ->hintIcon('heroicon-o-presentation-chart-line')
                    ->integer()
                    ->minValue(0)
                    ->maxValue(99999)
                    ->required(),
                Forms\Components\TextInput::make('initial_heading')
                    ->label(__('form.sids.initial_heading.label'))
                    ->helperText(__('form.sids.initial_heading.helper'))
                    ->hintIcon('heroicon-o-arrows-expand')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(360),
                Select::make('handoff_id')
                    ->label(__('form.sids.handoff.label'))
                    ->helperText(__('form.sids.handoff.helper'))
                    ->hintIcon('heroicon-o-clipboard-list')
                    ->options(
                        fn() => Handoff::all()
                            ->mapWithKeys(
                                fn(Handoff $handoff) => [
                                    $handoff->id => $handoff->description,
                                ]
                            ),
                    )
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('runway.airfield.code')
                    ->label(__('table.sids.columns.airfield'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('runway.identifier')
                    ->label(__('table.sids.columns.runway')),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('table.sids.columns.identifier')),
                Tables\Columns\TextColumn::make('initial_altitude')
                    ->label(__('table.sids.columns.initial_altitude')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->filters([
                Tables\Filters\SelectFilter::make('airfield')
                    ->label(__('filter.sids.airfield'))
                    ->options(Airfield::all()->mapWithKeys(fn (Airfield $airfield) => [$airfield->id => $airfield->code]))
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['value'])) {
                                return $query;
                            }

                            return $query->whereHas(
                                'runway.airfield',
                                function (Builder $airfield) use ($data) {
                                    return $airfield->where('id', $data['value']);
                                }
                            );
                        }
                    ),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PrenotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSids::route('/'),
            'create' => Pages\CreateSid::route('/create'),
            'view' => Pages\ViewSid::route('/{record}'),
            'edit' => Pages\EditSid::route('/{record}/edit'),
        ];
    }
}
