<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use App\Models\Stand\Stand;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PairedStandsRelationManager extends RelationManager
{
    protected static string $relationship = 'pairedStands';
    protected static ?string $inverseRelationship = 'pairedStands';

    protected static ?string $recordTitleAttribute = 'identifier';

    protected function getTableDescription(): ?string
    {
        return 'Stands that are paired cannot be simultaneously assigned to aircraft. ' .
            'Note, this does not prevent aircraft from spawning up on a stand!';
    }

    public static function table(Table $table): Table
    {
        $attachAction = Tables\Actions\AttachAction::make();
        $detachAction = Tables\Actions\DetachAction::make();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('Id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(__('Airfield'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('Identifier'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                $attachAction->form(fn(Tables\Actions\AttachAction $action): array => [
                    Select::make('recordId')
                        ->required()
                        ->options(
                            $attachAction->getRelationship()
                                ->getRelated()
                                ->newModelQuery()
                                ->where('airfield_id', $attachAction->getRelationship()->getParent()->airfield_id)
                                ->where('id', '<>', $attachAction->getRelationship()->getParent()->id)
                                ->whereDoesntHave('pairedStands', function (Builder $pairedStand) use ($attachAction) {
                                    $pairedStand->where('stand_pairs.paired_stand_id', $attachAction->getRelationship()->getParent()->id);
                                })
                                ->get()
                                ->mapWithKeys(
                                    fn(Stand $stand) => [
                                        $stand->{$attachAction->getRelationship()->getRelatedKeyName(
                                        )} => $attachAction->getRecordTitle($stand),
                                    ]
                                )
                        )
                        ->searchable()
                        ->label('Stand to Pair')
                        ->disableLabel(false)
                        ->helperText('Only stands at the same airfield may be paired.')
                ])
                    ->using(function (array $data) use ($attachAction) {
                        DB::transaction(function () use ($attachAction, $data) {
                            $stand = $attachAction->getRelationship()->getParent();
                            $pairedStand = Stand::findOrFail($data['recordId']);
                            $stand->pairedStands()->attach($pairedStand, ['stand_id' => $stand->id]);
                            $pairedStand->pairedStands()->attach($stand, ['stand_id' => $pairedStand->id]);

                            return $data;
                        });
                    })
                    ->label('Add paired stand'),
            ])
            ->actions([
                $detachAction->using(
                    function (Stand $record) use ($detachAction): void {
                        DB::transaction(function () use ($record, $detachAction) {
                            $detachAction->getRelationship()->detach($record);
                            $record->pairedStands()->detach($detachAction->getRelationship()->getParent());
                        });
                    }
                )
                    ->label('Unpair'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
