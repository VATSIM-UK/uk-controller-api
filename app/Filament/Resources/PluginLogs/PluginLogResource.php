<?php

namespace App\Filament\Resources\PluginLogs;

use Filament\Schemas\Schema;
use App\Filament\Resources\PluginLogs\Pages\ListPluginLogs;
use App\Filament\Resources\PluginLogs\Pages\ViewPluginLog;
use App\Filament\Resources\PluginLogResource\Pages;
use App\Models\Plugin\PluginLog;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PluginLogResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = PluginLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rss';
    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->label(self::translateFormPath('id.label'))
                    ->required(),
                TextInput::make('type')
                    ->label(self::translateFormPath('type.label'))
                    ->required(),
                TextInput::make('message')
                    ->label(self::translateFormPath('message.label'))
                    ->required(),
                Textarea::make('metadata')
                    ->columnSpan('full')
                    ->formatStateUsing(fn (array|null $state) => json_encode($state, JSON_PRETTY_PRINT))
                    ->label(self::translateFormPath('metadata.label'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(self::translateTablePath('columns.id')),
                TextColumn::make('type')
                    ->label(self::translateTablePath('columns.type')),
                TextColumn::make('created_at')
                    ->label(self::translateTablePath('columns.created_at')),
            ])->defaultSort('id', 'desc');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => ListPluginLogs::route('/'),
            'view' => ViewPluginLog::route('/{record}'),
        ];
    }
    
    protected static function translationPathRoot(): string
    {
        return 'plugin';
    }
}
