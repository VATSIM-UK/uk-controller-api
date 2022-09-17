<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirfieldResource\Pages;
use App\Models\Airfield\Airfield;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class AirfieldResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Airfield::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $recordTitleAttribute = 'code';

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
                Tables\Columns\TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transition_altitude')
                    ->label(self::translateTablePath('columns.transition')),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.top_down'))
                    ->default(['--'])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('code');
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
            'index' => Pages\ListAirfields::route('/'),
            'create' => Pages\CreateAirfield::route('/create'),
            'view' => Pages\ViewAirfield::route('/{record}'),
            'edit' => Pages\EditAirfield::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'airfields';
    }
}
