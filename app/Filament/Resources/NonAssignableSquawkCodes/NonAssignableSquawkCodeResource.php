<?php

namespace App\Filament\Resources\NonAssignableSquawkCodes;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\NonAssignableSquawkCodes\Pages\ManageNonAssignnableSquawkCodeRanges;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use App\Filament\Resources\TranslatesStrings;

class NonAssignableSquawkCodeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = NonAssignableSquawkCode::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Non Assignable';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wifi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::singleSquawkInput('code', 'code')
                    ->unique(ignoreRecord: true),
                TextInput::make('description')
                    ->required()
                    ->maxLength(255)
                    ->label(self::translateFormPath('description.label')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                self::squawkCodeTableColumn(),
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNonAssignnableSquawkCodeRanges::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'squawks.non_assignable';
    }
}
