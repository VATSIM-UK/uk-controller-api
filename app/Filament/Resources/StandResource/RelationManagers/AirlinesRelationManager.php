<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use App\Filament\Helpers\PairsAirlinesWithStands;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class AirlinesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;
    use PairsAirlinesWithStands;
    
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'airlines';
    protected static ?string $inverseRelationship = 'stands';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')
                    ->label(self::translateTablePath('columns.icao'))
                    ->sortable()
                    ->searchable(),
                ...self::airlineStandPairingTableColumns(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-airline')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineStandPairingFormFields(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make('edit-airline-pairing')
                    ->form(self::airlineStandPairingFormFields()),
                Tables\Actions\DetachAction::make('unpair-airline')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure())
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'stands.airlines';
    }
}
