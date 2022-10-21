<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VersionResource\Pages;
use App\Models\Version\Version;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Builder;

class VersionResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Version::class;
    protected static ?string $navigationLabel = 'Plugin Versions';
    protected static ?string $navigationIcon = 'heroicon-o-puzzle';

    public static function getEloquentQuery(): Builder
    {
        return Version::withTrashed()->orderByDesc('created_at');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('version')
                    ->label(self::translateTablePath('columns.version'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('pluginReleaseChannel.name')
                    ->label(self::translateTablePath('columns.release_channel'))
                    ->formatStateUsing(fn (string $state) => ucwords($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(self::translateTablePath('columns.released_at'))
                    ->getStateUsing(fn (Version $record) => $record->created_at->format('D d M Y, H:i:s'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->getStateUsing(fn (Version $record) => !$record->trashed())
                    ->label(self::translateTablePath('columns.is_active')),
            ])
            ->actions([
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(self::translateTablePath('delete_modal.heading'))
                    ->modalSubheading(self::translateTablePath('delete_modal.sub_heading')),
                RestoreAction::make()
                    ->modalHeading(self::translateTablePath('restore_modal.heading'))
                    ->modalSubheading(self::translateTablePath('restore_modal.sub_heading'))
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersions::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'versions';
    }
}
