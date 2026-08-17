<?php

namespace App\Filament\Resources\CcamsSquawkRanges;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\CcamsSquawkRanges\Pages\ManageCcamsSquawkRange;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CcamsSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = CcamsSquawkRange::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Squawk Ranges';

    protected static ?string $navigationLabel = 'CCAMS';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...self::squawkRangeInputs(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first')
                    ->label(self::translateTablePath('columns.first')),
                TextColumn::make('last')
                    ->label(self::translateTablePath('columns.last'))
                    ->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('first', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCcamsSquawkRange::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'squawks.ccams';
    }
}
