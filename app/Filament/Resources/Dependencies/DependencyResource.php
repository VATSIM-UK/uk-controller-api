<?php

namespace App\Filament\Resources\Dependencies;

use Filament\Actions\Action;
use App\Filament\Resources\Dependencies\Pages\ListDependencies;
use App\Filament\Resources\DependencyResource\Pages;
use App\Models\Dependency\Dependency;
use App\Services\DependencyService;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\TranslatesStrings;

class DependencyResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Dependency::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string | \UnitEnum | null $navigationGroup = 'Plugin';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(self::translateTablePath('columns.key')),
                TextColumn::make('local_file')
                    ->label(self::translateTablePath('columns.local_file')),
                TextColumn::make('updated_at')
                    ->label(self::translateTablePath('columns.updated_at')),

            ])
            ->recordActions([
                Action::make('download-dependency')
                    ->label(self::translateTablePath('actions.download.label'))
                    ->action(
                        fn(Dependency $record) => response()
                            ->streamDownload(
                                function () use ($record) {
                                    echo json_encode(DependencyService::fetchDependencyDataById($record->id));
                                },
                                $record->local_file,
                                ['Content-Type' => 'application/json']
                            )
                    )
                    ->tooltip(self::translateTablePath('actions.download.tooltip'))
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDependencies::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'dependencies';
    }
}
