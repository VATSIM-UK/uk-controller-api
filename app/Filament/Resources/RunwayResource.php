<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\RunwayResource\Pages;
use App\Models\Runway\Runway;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class RunwayResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Runway::class;
    protected static ?string $recordTitleAttribute = 'identifier';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up';

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
                Tables\Columns\TextColumn::make('airfield.code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('heading'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('airfield')
                    ->label(self::translateFilterPath('airfield'))
                    ->options(SelectOptions::airfields())
                    ->searchable()
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['value'])) {
                                return $query;
                            }

                            return $query->whereHas(
                                'airfield',
                                function (Builder $airfield) use ($data) {
                                    return $airfield->where('id', $data['value']);
                                }
                            );
                        }
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
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
            'index' => Pages\ListRunways::route('/'),
            'create' => Pages\CreateRunway::route('/create'),
            'view' => Pages\ViewRunway::route('/{record}'),
            'edit' => Pages\EditRunway::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'runways';
    }
}
