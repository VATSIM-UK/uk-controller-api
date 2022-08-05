<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavaidResource\Pages;
use App\Filament\Resources\NavaidResource\RelationManagers;
use App\Models\Navigation\Navaid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class NavaidResource extends Resource
{
    protected static ?string $model = Navaid::class;

    protected static ?string $navigationIcon = 'heroicon-o-location-marker';
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationLabel = 'Navaids and Holds';
    protected static ?string $label = 'Navaids and Holds';

    public static function getEloquentQuery(): Builder
    {
        return Navaid::with('holds');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('identifier')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(5)
                    ->label(__('form.navaids.identifier.label'))
                    ->helperText(__('form.navaids.identifier.helper'))
                    ->disabled(fn(Page $livewire) => !$livewire instanceof CreateRecord),
                TextInput::make('latitude')
                    ->required()
                    ->numeric('decimal:7')
                    ->minValue(-90)
                    ->maxValue(90)
                    ->label(__('form.navaids.latitude.label'))
                    ->helperText(__('form.navaids.latitude.helper')),
                TextInput::make('longitude')
                    ->required()
                    ->numeric('decimal:7')
                    ->minValue(-180)
                    ->maxValue(180)
                    ->label(__('form.navaids.longitude.label'))
                    ->helperText(__('form.navaids.longitude.helper')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('table.navaids.columns.id')),
                Tables\Columns\TextColumn::make('identifier')
                    ->sortable()
                    ->searchable()
                    ->label(__('table.navaids.columns.identifier')),
                Tables\Columns\BooleanColumn::make('has_published_holds')
                    ->getStateUsing(function (Navaid $record) {
                        return $record->holds->isNotEmpty();
                    })
                    ->label(__('table.navaids.columns.published_holds')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HoldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavaids::route('/'),
            'create' => Pages\CreateNavaid::route('/create'),
            'view' => Pages\ViewNavaid::route('/{record}'),
            'edit' => Pages\EditNavaid::route('/{record}/edit'),
        ];
    }
}
