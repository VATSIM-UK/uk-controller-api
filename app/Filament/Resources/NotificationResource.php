<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends Resource
{
    use TranslatesStrings;

    private const DATE_FORMAT = 'd M Y H:i';

    protected static ?string $model = Notification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationGroup = 'Controller';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(self::translateFormPath('title.label'))
                    ->maxLength(255)
                    ->required(),
                TextInput::make('link')
                    ->label(self::translateFormPath('link.label'))
                    ->helperText(self::translateFormPath('link.helper'))
                    ->url(),
                DateTimePicker::make('valid_from')
                    ->label(self::translateFormPath('valid_from.label'))
                    ->helperText(self::translateFormPath('valid_from.helper'))
                    ->displayFormat(self::DATE_FORMAT)
                    ->withoutSeconds()
                    ->required(),
                DateTimePicker::make('valid_to')
                    ->label(self::translateFormPath('valid_to.label'))
                    ->helperText(self::translateFormPath('valid_to.helper'))
                    ->displayFormat(self::DATE_FORMAT)
                    ->withoutSeconds()
                    ->after('valid_from')
                    ->required(),
                Textarea::make('body')
                    ->label(self::translateFormPath('body.label'))
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
                    ->label(self::translateTablePath('columns.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label(self::translateTablePath('columns.valid_from'))
                    ->date(self::DATE_FORMAT)
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_to')
                    ->label(self::translateTablePath('columns.valid_to'))
                    ->date(self::DATE_FORMAT)
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('read')
                    ->label(self::translateTablePath('columns.read'))
                    ->getStateUsing(
                        fn (Notification $record) => $record->readBy()->where('user.id', Auth::id())->exists()
                    ),
            ])
            ->filters([
                Tables\Filters\Filter::make('unread')
                    ->label(self::translateFilterPath('unread'))
                    ->query(fn (Builder $query) => $query->unreadBy(Auth::user()))
                    ->toggle(),
                Tables\Filters\Filter::make('active')
                    ->label(self::translateFilterPath('active'))
                    ->toggle()
                    ->query(fn (Builder $query) => $query->active()),
                Tables\Filters\MultiSelectFilter::make('controllers')
                    ->label(self::translateFilterPath('controllers'))
                    ->options(SelectOptions::controllers())
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

    protected static function translationPathRoot(): string
    {
        return 'notifications';
    }
}
