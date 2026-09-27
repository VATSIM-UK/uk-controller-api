<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Tables\ActivitiesTable as BaseActivitiesTable;

class ActivitiesTable extends BaseActivitiesTable
{
    public static function configure(Table $table): Table
    {
        $table = parent::configure($table);

        $filters = $table->getFilters(true);

        $filters['properties->old'] = Filter::make('properties->old')
            ->indicateUsing(
                fn (array $data): ?string => filled($data['old'] ?? null)
                    ? __('filament-logger::filament-logger.resource.label.old_attributes').$data['old']
                    : null,
            )
            ->schema([
                TextInput::make('old')
                    ->label(__('filament-logger::filament-logger.resource.label.old'))
                    ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
            ])
            ->query(
                fn (Builder $query, array $data): Builder => filled($data['old'] ?? null)
                    ? $query->where('properties->old', 'like', "%{$data['old']}%")
                    : $query,
            );

        $filters['properties->attributes'] = Filter::make('properties->attributes')
            ->indicateUsing(
                fn (array $data): ?string => filled($data['new'] ?? null)
                    ? __('filament-logger::filament-logger.resource.label.new_attributes').$data['new']
                    : null,
            )
            ->schema([
                TextInput::make('new')
                    ->label(__('filament-logger::filament-logger.resource.label.new'))
                    ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
            ])
            ->query(
                fn (Builder $query, array $data): Builder => filled($data['new'] ?? null)
                    ? $query->where('properties->attributes', 'like', "%{$data['new']}%")
                    : $query,
            );

        return $table->filters($filters);
    }
}
