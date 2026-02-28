<?php

namespace App\Filament\Operations\Resources\UserResource\Pages;

use App\Filament\Operations\Resources\UserResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $operationsRole = Role::where('name', 'Operations')->first();
        if ($operationsRole) {
            $data['roles'] = [$operationsRole->id];
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('Change Password')->form([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->label('New Password')
                    ->columnSpan(1),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->same('password')
                    ->rule(Password::default())
                    ->label('Confirm New Password')
                    ->columnSpan(1),

            ])->action(function (array $data) {
                $this->record->update([
                    'password' => $data['password'],
                ]);
                Session::getHandler()->destroy($this->record->session_id);

                // $this->notify('success', 'Password Changed Successfully');
            }),
        ];
    }
}
