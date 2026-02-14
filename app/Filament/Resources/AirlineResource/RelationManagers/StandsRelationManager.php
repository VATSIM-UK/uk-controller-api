<?php

namespace App\Filament\Resources\AirlineResource\RelationManagers;

use App\Filament\Helpers\PairsAirlinesWithStands;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Stand\Stand;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class StandsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithStands;
    use TranslatesStrings;

    private const DEFAULT_COLUMN_VALUE = '--';
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'stands';
    protected static ?string $inverseRelationship = 'airlines';
    protected static ?string $recordTitleAttribute = 'identifier';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stand_id')
                    ->formatStateUsing(fn (Stand $record) => $record->airfieldIdentifier)
                    ->label(self::translateTablePath('columns.stand'))
                    ->sortable()
                    ->searchable(),
                ...self::airlineStandPairingTableColumns(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-stand')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Stand $record):string => $record->airfieldIdentifier)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineStandPairingFormFields(),
                    ])
            ])
            ->actions([
                EditAction::make('edit-stand-pairing')
                    ->form(self::airlineStandPairingFormFields()),
                DetachAction::make('unpair-stand')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure()),
            ])
            ->bulkActions([
                BulkAction::make('bulk-unpair-stand')
                    ->label(self::translateFormPath('remove.label'))
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $unpair = self::unpairingClosure();

                        $records->each(function (Stand $record) use ($unpair) {
                            $action = DetachAction::make('bulk-unpair-terminal');
                            $action->record($record);

                            $unpair($action);
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines.stands';
    }
}
