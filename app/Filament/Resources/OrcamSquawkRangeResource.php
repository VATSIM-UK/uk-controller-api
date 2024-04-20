<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\OrcamSquawkRangeResource\Pages;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Rules\Airfield\PartialAirfieldIcao;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class OrcamSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = OrcamSquawkRange::class;
    protected static ?string $navigationGroup = 'Squawk Ranges';
    protected static ?string $navigationLabel = 'ORCAM';
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...self::squawkRangeInputs(),
                TextInput::make('origin')
                    ->label(self::translateFormPath('origin.label'))
                    ->helperText(self::translateFormPath('origin.helper'))
                    ->required()
                    ->rule(new PartialAirfieldIcao),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...self::squawkRangeTableColumns(),
                Tables\Columns\TextColumn::make('origin')
                    ->label(self::translateTablePath('columns.origin')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrcamSquawkRanges::route('/'),
        ];
    }

    /**
     * Returns the root of the translation path for the relations manager, to build
     * labels etc.
     *
     * @return string
     */
    protected static function translationPathRoot(): string
    {
        return 'squawks.orcam';
    }
}
