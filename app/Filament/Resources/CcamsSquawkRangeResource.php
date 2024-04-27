<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\CcamsSquawkRangeResource\Pages;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class CcamsSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = CcamsSquawkRange::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'CCAMS';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...self::squawkRangeInputs(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::squawkRangeTableColumns())
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCcamsSquawkRange::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'squawks.ccams';
    }
}
