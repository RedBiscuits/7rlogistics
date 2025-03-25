<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Http\Responses\Auth\LoginResponse as AuthLoginResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends AuthLoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Auth::user();
        $url = '/';

        if (! $user) {
            Auth::logout();

            return redirect()->route($url);
        }

        if ($user->hasRole('Super Admin')) {
            $url = route('filament.admin.pages.dashboard'); // Admin panel
        } elseif ($user->hasRole('Client') && $user->company_id) {
            $url = route('filament.client.pages.dashboard'); // Client panel
        } elseif ($user->hasRole('Finance')) {
            $url = route('filament.finance.pages.dashboard'); // Finance panel
        } elseif ($user->hasRole('Operations')) {
            $url = route('filament.operations.pages.dashboard'); // Operations panel
        } else {
            Auth::logout(); // Log out the user
        }

        return redirect()->intended($url);
    }
}
