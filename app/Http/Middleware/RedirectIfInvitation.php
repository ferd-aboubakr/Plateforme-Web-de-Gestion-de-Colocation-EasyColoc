<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectIfInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si l'utilisateur n'est pas connecté et qu'il y a un token d'invitation
        if (!auth()->check() && $request->route('token')) {
            // Stocker le token en session pour après le login
            Session::put('invitation_token', $request->route('token'));
            
            // Rediriger vers login avec un message spécifique
            return redirect()->route('login')
                ->with('info', 'Veuillez vous connecter pour accepter l\'invitation.');
        }
        
        return $next($request);
    }
}
