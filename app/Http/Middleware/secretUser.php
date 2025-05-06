<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class secretUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $ids = explode(',', env('SECRET_USER_IDS'));
        $userId = auth()->id();

        if (!in_array($userId, $ids)) {
            return redirect()->route('dashboard.index')->withNotifyerror('You are unauthorized to access this resources');
        }

        return $next($request);
    }
}
