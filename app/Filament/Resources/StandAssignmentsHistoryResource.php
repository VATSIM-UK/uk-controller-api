<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StandAssignmentsHistoryResource\Pages;
use App\Models\Stand\StandAssignmentsHistory;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
                //
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
