<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Vérifier si l'utilisateur a le rôle requis
        if (!$user->hasRole($role)) {
            // Rediriger vers le bon dashboard selon le rôle de l'utilisateur
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            } elseif ($user->hasRole('student')) {
                return redirect()->route('student.dashboard');
            }
            
            abort(403, 'Accès non autorisé à cette section.');
        }

        return $next($request);
    }
}