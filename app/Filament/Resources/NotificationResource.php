<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification\Notification;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends Resource
{
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
                    ->displayFormat('d M Y H:i')
                    ->withoutSeconds()
                    ->required(),
                DateTimePicker::make('valid_to')
                    ->label(__('form.notifications.valid_to.label'))
                    ->helperText(__('form.notifications.valid_to.helper'))
                    ->displayFormat('d M Y H:i')
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
                    ->date('d M Y H:i'),
                Tables\Columns\TextColumn::make('valid_to')
                    ->label(__('table.notifications.columns.valid_to'))
                    ->date('d M Y H:i'),
                Tables\Columns\BooleanColumn::make('read')
                    ->label(__('table.notifications.columns.read'))
                    ->getStateUsing(
                        fn(Notification $record) => $record->readBy()->where('user.id', Auth::id())->exists()
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
