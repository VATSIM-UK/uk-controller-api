<?php

namespace App\Filament\Resources\Versions;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Versions\Pages\ListVersions;
use App\Filament\Resources\VersionResource\Pages;
use App\Models\Version\Version;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TranslatesStrings;

class VersionResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Version::class;
    protected static ?string $navigationLabel = 'Plugin Versions';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static string | \UnitEnum | null $navigationGroup = 'Plugin';

    public static function getEloquentQuery(): Builder
    {
        return Version::withTrashed()->orderByDesc('created_at');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version')
                    ->label(self::translateTablePath('columns.version'))
                    ->searchable(),
                TextColumn::make('pluginReleaseChannel.name')
                    ->label(self::translateTablePath('columns.release_channel'))
                    ->formatStateUsing(fn (string $state) => ucwords($state)),
                TextColumn::make('created_at')
                    ->label(self::translateTablePath('columns.released_at'))
                    ->getStateUsing(fn (Version $record) => $record->created_at->format('D d M Y, H:i:s'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->getStateUsing(fn (Version $record) => !$record->trashed())
                    ->label(self::translateTablePath('columns.is_active')),
            ])
            ->recordActions([
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
            'index' => ListVersions::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'versions';
    }
}
