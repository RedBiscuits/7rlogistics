<?php

namespace App\Filament\Client\Pages;

use App\Http\Responses\LoginResponse as ResponsesLoginResponse;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as AuthLogin;

class Login extends AuthLogin
{
    public function authenticate(): ?LoginResponse
    {
        parent::authenticate();

        return new ResponsesLoginResponse;
    }
}
