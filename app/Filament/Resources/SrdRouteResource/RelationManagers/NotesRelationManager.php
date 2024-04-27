<?php

namespace App\Filament\Resources\SrdRouteResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class NotesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    protected static string $relationship = 'notes';
    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(self::translateTablePath('columns.number')),
                Tables\Columns\TextColumn::make('note_text')
                    ->label(self::translateTablePath('columns.text')),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'srd.notes';
    }
}
