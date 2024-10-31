<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class organik
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->tipe_employee->id != 1){
            return redirect()->route('dashboard.index')->withNotifyerror('You are unauthorized to access this resources');
        }
        return $next($request);
    }
}
