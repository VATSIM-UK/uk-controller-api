<?php

namespace App\Filament\Resources\AircraftResource\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Filament\Actions\DetachAction;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class WakeCategoriesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    protected static string $relationship = 'wakeCategories';
    protected static ?string $inverseRelationship = 'aircraft';
    protected static ?string $recordTitleAttribute = 'description';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheme.name')
                    ->label(self::translateTablePath('columns.scheme_name')),
                TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code')),
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(
                        fn (RelationManager $livewire) => [
                            Select::make('recordId')
                                ->label(self::translateFormPath('category.label'))
                                ->required()
                                ->options(
                                    function () use ($livewire) {
                                        $schemesInUse = WakeCategoryScheme::whereHas(
                                            'categories',
                                            function (Builder $category) use ($livewire) {
                                                $category->whereHas(
                                                    'aircraft',
                                                    function (Builder $aircraft) use ($livewire) {
                                                        $aircraft->where('aircraft.id', $livewire->ownerRecord->id);
                                                    }
                                                );
                                            }
                                        )->get()->pluck('id');

                                        return WakeCategory::with('scheme')
                                            ->whereNotIn('wake_category_scheme_id', $schemesInUse)
                                            ->orderBy('wake_category_scheme_id')
                                            ->orderBy('relative_weighting')
                                            ->get()
                                            ->mapWithKeys(function (WakeCategory $category) {
                                                return [
                                                    $category->id => sprintf(
                                                        '%s: %s',
                                                        $category->scheme->name,
                                                        $category->description
                                                    ),
                                                ];
                                            });
                                    }
                                ),
                        ]
                    ),
            ])
            ->recordActions([
                DetachAction::make(),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'aircraft.wake_categories';
    }
}
