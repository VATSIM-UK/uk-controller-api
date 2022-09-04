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
                TextInput::make('origin'),
                TextInput::make('destination'),
                TextInput::make('minimum_level'),
                TextInput::make('maximum_level'),
                TextInput::make('route_segment'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->searchable(),
                Tables\Columns\TextColumn::make('minimum_level'),
                Tables\Columns\TextColumn::make('maximum_level'),
                Tables\Columns\TextColumn::make('route_segment'),
                Tables\Columns\TextColumn::make('notes')
                    ->formatStateUsing(
                        fn(Collection $state): string => $state->map(fn(SrdNote $note) => $note->id)->join(',')
                    ),
            ])
            ->filters([
                //
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
}
