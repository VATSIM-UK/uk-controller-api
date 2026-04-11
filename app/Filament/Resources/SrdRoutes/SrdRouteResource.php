<?php

namespace App\Filament\Resources\SrdRoutes;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use App\Filament\Resources\SrdRoutes\Pages\ListSrdRoutes;
use App\Filament\Resources\SrdRoutes\Pages\ViewSrdRoute;
use App\Filament\Resources\SrdRouteResource\Pages;
use App\Filament\Resources\SrdRoutes\RelationManagers\NotesRelationManager;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Filament\Resources\TranslatesStrings;

class SrdRouteResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = SrdRoute::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'SRD Routes';
    protected static string|\UnitEnum|null $navigationGroup = 'Enroute';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('origin')
                    ->label(self::translateTablePath('columns.origin'))
                    ->searchable(),
                TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination'))
                    ->searchable(),
                TextColumn::make('minimum_level')
                    ->label(self::translateTablePath('columns.minimum_level')),
                TextColumn::make('maximum_level')
                    ->label(self::translateTablePath('columns.maximum_level')),
                TextColumn::make('sid')
                    ->default('--')
                    ->label(self::translateTablePath('columns.sid')),
                TextColumn::make('star')
                    ->default('--')
                    ->label(self::translateTablePath('columns.star')),
                TextColumn::make('route_segment')
                    ->label(self::translateTablePath('columns.route_segment'))
                    ->formatStateUsing(fn (SrdRoute $record) => self::buildFullSrdRouteString($record))
                    ->limit(50),
                TextColumn::make('notes.id')
                    ->label(self::translateTablePath('columns.notes')),
            ])
            ->filters([
                Filter::make('origin')
                    ->formComponent(TextInput::class)
                    ->query(
                        fn (Builder $query, array $data) => isset($data['isActive'])
                        ? $query->where('origin', $data['isActive'])
                        : $query
                    ),
                Filter::make('destination')
                    ->formComponent(TextInput::class)
                    ->query(
                        fn (Builder $query, array $data) => isset($data['isActive'])
                        ? $query->where('destination', $data['isActive'])
                        : $query
                    ),
                Filter::make('level')
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
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListSrdRoutes::route('/'),
            'view' => ViewSrdRoute::route('/{record}'),
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
