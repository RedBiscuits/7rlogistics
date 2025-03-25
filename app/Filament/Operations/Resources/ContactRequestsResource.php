<?php

namespace App\Filament\Operations\Resources;

use App\Filament\Operations\Resources\ContactRequestsResource\Pages;
use App\Models\ContactRequests;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ContactRequestsResource extends Resource
{
    protected static ?string $model = ContactRequests::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'System management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_read')
                    ->label('Mark as Read')
                    ->default(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('message'),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d-m-Y H:i'),
            ])
            ->filters([
                Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['name'], fn ($query, $name) => $query->where('name', 'like', "%{$name}%"));
                    }),
                Filter::make('email')
                    ->form([
                        Forms\Components\TextInput::make('email'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['email'], fn ($query, $email) => $query->where('email', 'like', "%{$email}%"));
                    }),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_at'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['created_at'], fn ($query, $date) => $query->whereDate('created_at', $date));
                    }),
                Filter::make('is_read')
                    ->form([
                        Forms\Components\Select::make('is_read')
                            ->options([
                                '1' => 'Read',
                                '0' => 'Unread',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['is_read'] !== null, fn ($query, $is_read) => $query->where('is_read', $is_read));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (ContactRequests $record): void {
                        $record->update(['is_read' => true]);
                    })
                    ->visible(fn (ContactRequests $record): bool => ! $record->is_read),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListContactRequests::route('/'),
            'create' => Pages\CreateContactRequests::route('/create'),
            'edit' => Pages\EditContactRequests::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return ContactRequests::where('is_read', false)->count();
    }
}
