<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\NonAssignableSquawkCodeResource\Pages\ManageNonAssignnableSquawkCodeRanges;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class NonAssignableSquawkCodeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = NonAssignableSquawkCode::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'Non Assignable';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
