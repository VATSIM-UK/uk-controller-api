<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrenoteResource\Pages;
use App\Filament\Resources\PrenoteResource\RelationManagers;
use App\Models\Controller\Prenote;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PrenoteResource extends Resource
{
    protected static ?string $model = Prenote::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrenotes::route('/'),
            'create' => Pages\CreatePrenote::route('/create'),
            'view' => Pages\ViewPrenote::route('/{record}'),
            'edit' => Pages\EditPrenote::route('/{record}/edit'),
        ];
    }    
}
