<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends Resource
{
    private const DATE_FORMAT = 'd M Y H:i';

    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('form.notifications.title.label'))
                    ->maxLength(255)
                    ->required(),
                TextInput::make('link')
                    ->label(__('form.notifications.link.label'))
                    ->helperText(__('form.notifications.link.helper'))
                    ->url(),
                DateTimePicker::make('valid_from')
                    ->label(__('form.notifications.valid_from.label'))
                    ->helperText(__('form.notifications.valid_from.helper'))
                    ->displayFormat(self::DATE_FORMAT)
                    ->withoutSeconds()
                    ->required(),
                DateTimePicker::make('valid_to')
                    ->label(__('form.notifications.valid_to.label'))
                    ->helperText(__('form.notifications.valid_to.helper'))
                    ->displayFormat(self::DATE_FORMAT)
                    ->withoutSeconds()
                    ->after('valid_from')
                    ->required(),
                Textarea::make('body')
                    ->label(__('form.notifications.body.label'))
                    ->maxLength(65535)
                    ->columnSpan('full')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('table.notifications.columns.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label(__('table.notifications.columns.valid_from'))
                    ->date(self::DATE_FORMAT)
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_to')
                    ->label(__('table.notifications.columns.valid_to'))
                    ->date(self::DATE_FORMAT)
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('read')
                    ->label(__('table.notifications.columns.read'))
                    ->getStateUsing(
                        fn (Notification $record) => $record->readBy()->where('user.id', Auth::id())->exists()
                    ),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('read')
                    ->label(__('filter.notifications.read'))
                    ->falseLabel(__('filter.notifications.read_false_label'))
                    ->trueLabel(__('filter.notifications.read_true_label'))
                    ->column('read')
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === null) {
                            return $query;
                        }

                        return $data['value']
                            ? $query->readBy(Auth::user())
                            : $query->unreadBy(Auth::user());
                    }),
                Tables\Filters\Filter::make('active')
                    ->label(__('filter.notifications.active'))
                    ->toggle()
                    ->query(fn (Builder $query) => $query->active()),
                Tables\Filters\MultiSelectFilter::make('controllers')
                    ->label(__('filter.notifications.controllers'))
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['values'])) {
                                return $query;
                            }

                            return $query->whereHas(
                                'controllers',
                                function (Builder $controllers) use ($data) {
                                    return $controllers->whereIn('controller_positions.id', $data['values']);
                                }
                            );
                        }
                    )
                    ->options(
                        ControllerPosition::all()
                            ->mapWithKeys(fn (ControllerPosition $position) => [$position->id => $position->callsign])
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('valid_to', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ControllersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'view' => Pages\ViewNotification::route('/{record}'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
