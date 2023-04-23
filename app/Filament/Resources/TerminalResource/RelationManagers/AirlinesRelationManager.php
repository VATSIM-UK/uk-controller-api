<?php

namespace App\Filament\Resources\TerminalResource\RelationManagers;

use App\Filament\Helpers\PairsAirlinesWithTerminals;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;

class AirlinesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithTerminals;
    use TranslatesStrings;

    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'airlines';
    protected static ?string $inverseRelationship = 'terminals';
    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')
                    ->label(self::translateTablePath('columns.icao'))
                    ->sortable()
                    ->searchable(),
                ...self::commonPairingTableColumns(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-airline')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::commonPairingFormFields(),
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make('unpair-airline')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure())
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'terminals.airlines';
    }
}
