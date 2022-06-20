<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use App\Models\Stand\Stand;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $attachAction = Tables\Actions\AttachAction::make();

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
                    $attachAction->getRecordSelect()
                        ->required()
                        ->options(
                            $attachAction->getRelationship()
                                ->getRelated()
                                ->newModelQuery()
                                ->where('airfield_id', $attachAction->getRelationship()->getParent()->airfield_id)
                                ->where('id', '<>', $attachAction->getRelationship()->getParent()->id)
                                ->get()
                                ->mapWithKeys(
                                    fn(Stand $stand) => [
                                        $stand->{$attachAction->getRelationship()->getRelatedKeyName(
                                        )} => $attachAction->getRecordTitle($stand),
                                    ]
                                )
                        )
                        ->label('Stand to Pair')
                        ->disableLabel(false)
                        ->helperText('Only stands at the same airfield may be paired.'),
                ])
                    ->using(function (array $data) use ($attachAction) {
                        DB::transaction(function () use ($attachAction, $data) {
                            $stand = $attachAction->getRelationship()->getParent();
                            $pairedStand = Stand::findOrFail($data['recordId']);
                            $stand->pairedStands()->attach($pairedStand, ['stand_id' => $stand->id]);
                            $pairedStand->pairedStands()->attach($stand, ['stand_id' => $pairedStand->id]);

                            return $data;
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
