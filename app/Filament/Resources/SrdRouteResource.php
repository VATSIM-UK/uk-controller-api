<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SrdRouteResource\Pages;
use App\Filament\Resources\SrdRouteResource\RelationManagers\NotesRelationManager;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SrdRouteResource extends Resource
{
    use TranslatesStrings;
    
    protected static ?string $model = SrdRoute::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'SRD Routes';
    protected static ?string $navigationGroup = 'Enroute';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('origin')
                    ->label(self::translateFormPath('origin.label')),
                TextInput::make('destination')
                    ->label(self::translateFormPath('destination.label')),
                TextInput::make('minimum_level')
                    ->label(self::translateFormPath('minimum_level.label')),
                TextInput::make('maximum_level')
                    ->label(self::translateFormPath('maximum_level.label')),
                TextInput::make('route_segment')
                    ->label(self::translateFormPath('route_segment.label')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin')
                    ->label(self::translateTablePath('columns.origin'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('minimum_level')
                    ->label(self::translateTablePath('columns.minimum_level')),
                Tables\Columns\TextColumn::make('maximum_level')
                    ->label(self::translateTablePath('columns.maximum_level')),
                Tables\Columns\TextColumn::make('sid')
                    ->default('--')
                    ->label(self::translateTablePath('columns.sid')),
                Tables\Columns\TextColumn::make('star')
                    ->default('--')
                    ->label(self::translateTablePath('columns.star')),
                Tables\Columns\TextColumn::make('route_segment')
                    ->label(self::translateTablePath('columns.route_segment'))
                    ->formatStateUsing(fn (SrdRoute $record) => self::buildFullSrdRouteString($record)),
                Tables\Columns\TextColumn::make('notes.id')
                    ->label(self::translateTablePath('columns.notes'))
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

    protected static function translationPathRoot(): string
    {
        return 'srd';
    }
}
