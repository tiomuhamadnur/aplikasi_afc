<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CheckPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $default_password = 'user123';
            if (Hash::check($default_password, $user->password) && !$request->is('profile*')) {
                return redirect()->route('profile.index')->withNotifyerror('Demi keamanan, Anda wajib mengganti password dan data diri terlebih dahulu!');
            }
        }
        return $next($request);
    }
}
