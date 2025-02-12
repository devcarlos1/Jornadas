<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        // Verificar si el usuario es un usuario normal
        if (!$user || $user->role !== 'user') {
            return response()->json(['error' => 'Acceso no autorizado. Se requiere rol de usuario.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}


