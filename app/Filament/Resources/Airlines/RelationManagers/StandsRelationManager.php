<?php

namespace App\Filament\Resources\Airlines\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\EditAction;
use Filament\Actions\DetachAction;
use Filament\Actions\BulkAction;
use App\Filament\Helpers\PairsAirlinesWithStands;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Stand\Stand;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;

class StandsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithStands;
    use TranslatesStrings;

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
                TextColumn::make('stand_id')
                    ->formatStateUsing(fn (Stand $record) => $record->airfieldIdentifier)
                    ->label(self::translateTablePath('columns.stand'))
                    ->sortable()
                    ->searchable(),
                ...self::airlineStandPairingTableColumns(),
            ])
            ->headerActions([
                AttachAction::make('pair-stand')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('attach'))
                    ->form(fn (AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Stand $record):string => $record->airfieldIdentifier)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineStandPairingFormFields(),
                    ])
            ])
            ->recordActions([
                EditAction::make('edit-stand-pairing')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('update'))
                    ->schema(self::airlineStandPairingFormFields()),
                DetachAction::make('unpair-stand')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('detach'))
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure()),
            ])
            ->toolbarActions([
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
