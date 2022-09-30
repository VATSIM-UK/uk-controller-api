<?php

namespace App\Filament\Resources\SrdRouteResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class NotesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;

    protected static string $relationship = 'notes';
    protected static ?string $recordTitleAttribute = 'id';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('table.srd.notes.columns.number')),
                Tables\Columns\TextColumn::make('note_text')
                    ->label(__('table.srd.notes.columns.text')),
            ]);
    }
}
