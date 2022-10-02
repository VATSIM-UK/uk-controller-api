<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SrdRouteResource\Pages;
use App\Filament\Resources\SrdRouteResource\RelationManagers\NotesRelationManager;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SrdRouteResource extends Resource
{
    protected static ?string $model = SrdRoute::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'SRD Routes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('origin')
                    ->label(__('form.srd.origin.label')),
                TextInput::make('destination')
                    ->label(__('form.srd.destination.label')),
                TextInput::make('minimum_level')
                    ->label(__('form.srd.minimum_level.label')),
                TextInput::make('maximum_level')
                    ->label(__('form.srd.maximum_level.label')),
                TextInput::make('route_segment')
                    ->label(__('form.srd.route_segment.label')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin')
                    ->label(__('table.srd.columns.origin'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(__('table.srd.columns.destination'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('minimum_level')
                    ->label(__('table.srd.columns.minimum_level')),
                Tables\Columns\TextColumn::make('maximum_level')
                    ->label(__('table.srd.columns.maximum_level')),
                Tables\Columns\TextColumn::make('sid')
                    ->default('--')
                    ->label(__('table.srd.columns.sid')),
                Tables\Columns\TextColumn::make('star')
                    ->default('--')
                    ->label(__('table.srd.columns.star')),
                Tables\Columns\TextColumn::make('route_segment')
                    ->label(__('table.srd.columns.route_segment'))
                    ->formatStateUsing(fn (SrdRoute $record) => self::buildFullSrdRouteString($record)),
                Tables\Columns\TextColumn::make('notes')
                    ->label(__('table.srd.columns.notes'))
                    ->formatStateUsing(
                        fn (Collection $state): string => $state->map(fn (SrdNote $note) => $note->id)->join(',')
                    ),
            ])
            ->filters([
                Tables\Filters\Filter::make('origin')
                    ->formComponent(TextInput::class)
                    ->query(
                        fn (Builder $query, array $data) => isset($data['isActive'])
                            ? $query->where('origin', $data['isActive'])
                            : $query
                    ),
                Tables\Filters\Filter::make('destination')
                    ->formComponent(TextInput::class)
                    ->query(
                        fn (Builder $query, array $data) => isset($data['isActive'])
                            ? $query->where('destination', $data['isActive'])
                            : $query
                    ),
                Tables\Filters\Filter::make('level')
                    ->formComponent(TextInput::class)
                    ->query(
                        fn (Builder $query, array $data) => isset($data['isActive'])
                            ? $query->where(function (Builder $query) use ($data) {
                                return $query->where('minimum_level', '<=', $data['isActive'])
                                    ->orWhereNull('minimum_level');
                            })->where('maximum_level', '>=', $data['isActive'])
                            : $query
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSrdRoutes::route('/'),
            'view' => Pages\ViewSrdRoute::route('/{record}'),
        ];
    }

    public static function buildFullSrdRouteString(SrdRoute $route): string
    {
        return trim(sprintf('%s %s', $route->sid, $route->route_segment));
    }
}
