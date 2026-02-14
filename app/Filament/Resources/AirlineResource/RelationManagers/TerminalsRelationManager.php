<?php

namespace App\Filament\Resources\AirlineResource\RelationManagers;

use App\Filament\Helpers\PairsAirlinesWithTerminals;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use Filament\Tables\Actions\BulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('terminal_id')
                    ->formatStateUsing(
                        fn (Terminal $record) => sprintf('%s / %s', $record->airfield->code, $record->description)
                    )
                    ->label(self::translateTablePath('columns.terminal'))
                    ->sortable()
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->where(
                            'description',
                            'like',
                            '%' . $search . '%'
                        )
                            ->orWhereIn(
                                'airfield_id',
                                Airfield::where('code', 'like', '%' . $search . '%')->pluck('id')
                            )
                    ),
                ...self::airlineTerminalPairingTableColumns(),
            ])
            ->headerActions([
                AttachAction::make('pair-terminal')
                    ->form(fn (AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Terminal $record): string => $record->airfieldDescription)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineTerminalPairingFormFields(),
                    ]),
            ])
            ->actions([
                EditAction::make('edit-terminal-pairing')
                    ->form(self::airlineTerminalPairingFormFields()),
                DetachAction::make('unpair-terminal')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure()),
                
            ])
            ->bulkActions([
                BulkAction::make('bulk-unpair-terminal')
                    ->label(self::translateFormPath('remove.label'))
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $unpair = self::unpairingClosure();

                        $records->each(function (Terminal $record) use ($unpair) {
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
        return 'airlines.terminals';
    }
}
