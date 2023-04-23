<?php

namespace App\Filament\Resources\AirlineResource\RelationManagers;

use App\Filament\Helpers\PairsAirlinesWithTerminals;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Airfield\Terminal;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;

class TerminalsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithTerminals;
    use TranslatesStrings;

    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'terminals';
    protected static ?string $inverseRelationship = 'airlines';
    protected static ?string $recordTitleAttribute = 'description';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('terminal')
                    ->formatStateUsing(
                        fn (Terminal $record) => sprintf('%s / %s', $record->airfield->code, $record->description)
                    )
                    ->label(self::translateTablePath('columns.terminal'))
                    ->sortable()
                    ->searchable(),
                ...self::commonPairingTableColumns(),
            ])
            ->headerActions([
                AttachAction::make('pair-terminal')
                    ->form(fn (AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Terminal $record):string => $record->airfieldDescription)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::commonPairingFormFields(),
                    ])
            ])
            ->actions([
                DetachAction::make('unpair-terminal')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure())
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines.terminals';
    }
}
