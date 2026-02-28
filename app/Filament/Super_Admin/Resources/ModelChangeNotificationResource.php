<?php

namespace App\Filament\Super_Admin\Resources;

use App\Enums\ModelChangeSource;
use App\Enums\ModelChangeType;
use App\Filament\Super_Admin\Resources\ModelChangeNotificationResource\Pages;
use App\Models\ModelChangeNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModelChangeNotificationResource extends Resource
{
    protected static ?string $model = ModelChangeNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'System management';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $modelLabel = 'Notification';

    protected static ?string $pluralModelLabel = 'Notifications';

    protected static ?int $navigationSort = 8;

    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('read_by_current_user')
                    ->label('')
                    ->getStateUsing(fn (ModelChangeNotification $record): bool => $record->isRead())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-bell-alert')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->wrap()
                    ->searchable()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (ModelChangeNotification $record): string => class_basename($record->subject_type) . ' #' . $record->subject_id),
                Tables\Columns\TextColumn::make('change_type')
                    ->label('Change type')
                    ->badge(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge(),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('Actor')
                    ->placeholder('—')
                    ->url(fn (ModelChangeNotification $record): ?string => match ($record->actor_type ?? null) {
                        \App\Models\Driver::class => DriverResource::getUrl('edit', ['record' => $record->actor_id]),
                        \App\Models\User::class => UserResource::getUrl('edit', ['record' => $record->actor_id]),
                        default => null,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('read_by_me')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Read by me')
                    ->falseLabel('Unread by me')
                    ->queries(
                        true: fn ($q) => $q->whereHas('reads', fn ($r) => $r->where('user_id', auth()->id())),
                        false: fn ($q) => $q->whereDoesntHave('reads', fn ($r) => $r->where('user_id', auth()->id())),
                    ),
                Tables\Filters\SelectFilter::make('source')
                    ->options(ModelChangeSource::class),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Subject type')
                    ->options([
                        \App\Models\Shipment::class => 'Shipment',
                        \App\Models\DriverAttendance::class => 'Driver attendance',
                        \App\Models\Driver::class => 'Driver',
                        \App\Models\User::class => 'User',
                        \App\Models\Mission::class => 'Mission',
                        \App\Models\AttendanceLocation::class => 'Attendance location',
                        \App\Models\TruckType::class => 'Truck type',
                        \App\Models\ContactRequests::class => 'Contact request',
                        \App\Models\Company::class => 'Company',
                        \App\Models\Area::class => 'Area',
                        \App\Models\Truck::class => 'Truck',
                        \App\Models\City::class => 'City',
                    ]),
                Tables\Filters\SelectFilter::make('change_type')
                    ->options(ModelChangeType::class),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')->label('From'),
                        \Filament\Forms\Components\DatePicker::make('created_until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'] ?? null, fn ($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'] ?? null, fn ($q) => $q->whereDate('created_at', '<=', $data['created_until']));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('view_resource')
                    ->label('View resource')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->action(function (ModelChangeNotification $record) {
                        $record->markAsRead();
                        $url = $record->getSubjectViewUrl();
                        if ($url) {
                            return redirect($url);
                        }
                    })
                    ->visible(fn (ModelChangeNotification $record): bool => $record->getSubjectViewUrl() !== null),
                Tables\Actions\Action::make('mark_as_read')
                    ->label('Mark as read')
                    ->icon('heroicon-o-check')
                    ->action(function (ModelChangeNotification $record): void {
                        $record->markAsRead();
                    })
                    ->visible(fn (ModelChangeNotification $record): bool => ! $record->isRead())
                    ->successNotificationTitle('Marked as read'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('mark_as_read')
                    ->label('Mark selected as read')
                    ->icon('heroicon-o-check')
                    ->action(fn ($records) => $records->each->markAsRead())
                    ->deselectRecordsAfterCompletion()
                    ->successNotificationTitle('Marked as read'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModelChangeNotifications::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
