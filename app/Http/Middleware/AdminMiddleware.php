<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        // Verificar si el usuario es admin
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Acceso no autorizado. Se requiere rol de administrador.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
