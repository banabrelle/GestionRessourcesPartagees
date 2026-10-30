<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (
                $user->must_change_password
                && !$request->routeIs('changer-mot-de-passe.show')
                && !$request->routeIs('changer-mot-de-passe.update')
                && !$request->routeIs('logout')
            ) {
                return redirect()->route('changer-mot-de-passe.show');
            }
        }

        return $next($request);
    }
}