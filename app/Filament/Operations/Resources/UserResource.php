<?php

namespace App\Filament\Operations\Resources;

use App\Filament\Operations\Resources\UserResource\Pages;
use App\Filament\Operations\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'System management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')->required()->minLength(3)->maxLength(255),
                Forms\Components\Select::make('company_id')->label('Company')
                    ->relationship('company', 'name'),
                Forms\Components\TextInput::make('position')->minLength(3)->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->unique(ignoreRecord: true)->required()->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->visibleOn('create')
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->label('New Password')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('password_confirmation')
                    ->visibleOn('create')
                    ->password()
                    ->required()
                    ->same('password')
                    ->dehydrated(false) // Prevents saving to database
                    ->label('Confirm New Password')
                    ->columnSpan(1),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name', fn ($query) => $query->where('name', 'Operations'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->maxItems(1)
                    ->helperText('Operations panel can only assign the Operations role.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('company.name')->sortable()->searchable()->label('Company')
                    ->getStateUsing(fn (User $record) => $record->company->name ?? '7R Logistics'),
                Tables\Columns\TextColumn::make('position')->sortable()->searchable()
                    ->getStateUsing(fn (User $record) => $record->position ?? 'N/A'),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->sortable()->label('Role'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s a'),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime(),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(fn () => Role::where('name', 'Operations')->pluck('name', 'name'))
                    ->relationship('roles', 'name', fn ($query) => $query->where('name', 'Operations'))
                    ->label('Role'),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\ShipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
