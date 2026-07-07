<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado. Se requiere rol Administrador.'], 403);
        }

        return $next($request);
    }
}
