<?php

namespace App\Filament\Resources\OrcamSquawkRanges;

use App\Filament\Helpers\HasSquawkRanges;
use App\Filament\Resources\OrcamSquawkRanges\Pages\ManageOrcamSquawkRanges;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Rules\Airfield\PartialAirfieldIcao;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrcamSquawkRangeResource extends Resource
{
    use HasSquawkRanges;
    use TranslatesStrings;

    protected static ?string $model = OrcamSquawkRange::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Squawk Ranges';

    protected static ?string $navigationLabel = 'ORCAM';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('origin')
                    ->label(self::translateTablePath('columns.origin'))
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
            'index' => ManageOrcamSquawkRanges::route('/'),
        ];
    }

    /**
     * Returns the root of the translation path for the relations manager, to build
     * labels etc.
     */
    protected static function translationPathRoot(): string
    {
        return 'squawks.orcam';
    }
}
