<?php

namespace App\Filament\Resources\CcamsSquawkRanges;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\CcamsSquawkRanges\Pages\ManageCcamsSquawkRange;
use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\CcamsSquawkRangeResource\Pages;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use App\Filament\Resources\TranslatesStrings;

class CcamsSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = CcamsSquawkRange::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'CCAMS';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wifi';

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
            ->columns(self::squawkRangeTableColumns())
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
