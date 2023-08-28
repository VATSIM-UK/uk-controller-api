<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\StandAssignmentsHistoryResource\Pages;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignmentsHistory;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class StandAssignmentsHistoryResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = StandAssignmentsHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationLabel = 'Stand Assignment History';
    protected static ?string $label = 'Stand Assignment History';

    protected static ?string $navigationGroup = 'Airfield';

    public static function getEloquentQuery(): Builder
    {
        return StandAssignmentsHistory::with('stand', 'stand.airfield');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->disabled()
            ->schema([
                ViewField::make('context')
                    ->view('filament.forms.stand_assignment_history_context')
                    ->label(static::translateFormPath('columns.context')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('callsign')
                    ->label(static::translateTablePath('columns.callsign'))
                    ->searchable(),
                TextColumn::make('identifier')
                    ->getStateUsing(fn(StandAssignmentsHistory $record) => $record->stand->airfieldIdentifier)
                    ->label(static::translateTablePath('columns.identifier'))
                    ->searchable(),
                TextColumn::make('assigned_at')
                    ->label(static::translateTablePath('columns.assigned_at'))
                    ->dateTime(),
                TextColumn::make('deleted_at')
                    ->placeholder('--')
                    ->label(static::translateTablePath('columns.deleted_at'))
                    ->dateTime(),
                TextColumn::make('type')
                    ->label(static::translateTablePath('columns.type')),
            ])
            ->actions([
                ViewAction::make('view_context')
                    ->label('View Context'),
            ])
            ->filters([
                Filter::make('callsign')
                    ->formComponent(TextInput::class)
                    ->query(
                        fn(Builder $query, array $data) => isset($data['isActive'])
                        ? $query->where('callsign', $data['isActive'])
                        : $query
                    ),
                Filter::make('airfield_and_stand')
                    ->form([
                        Select::make('airfield')
                            ->options(SelectOptions::airfields())
                            ->reactive()
                            ->searchable()
                            ->label('Airfield'),
                        Select::make('stand')
                        ->options(fn (Closure $get) => SelectOptions::standsForAirfield(Airfield::find($get('airfield'))))
                            ->searchable()
                            ->label('Stand')
                            ->hidden(fn(Closure $get) => !$get('airfield')),
                    ])
                    ->indicateUsing(function (array $data) {
                        if (isset($data['stand'])) {
                            return 'Stand: ' . Stand::find($data['stand'])->airfieldIdentifier;
                        }

                        if (isset($data['airfield'])) {
                            return 'Airfield: ' . Airfield::find($data['airfield'])->code;
                        }

                        return null;
                    })
                    ->query(function (Builder $query, array $data)
                    {
                        if (isset($data['airfield'])) {
                            $query->whereHas('stand.airfield', fn(Builder $query) => $query->where('id', $data['airfield']));
                        }

                        if (isset($data['stand'])) {
                            $query->where('stand_id', $data['stand']);
                        }

                        return $query;
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStandAssignmentsHistories::route('/'),
            'view' => Pages\ViewAssignmentContext::route('/{record}'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'stand_assignments_history';
    }
}
