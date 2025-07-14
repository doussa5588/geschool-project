<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Middleware de Sécurité - Développé par SADOU MBALLO
     * Contrôle d'accès basé sur les rôles
     */
    
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!in_array($user->role, $roles)) {
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}