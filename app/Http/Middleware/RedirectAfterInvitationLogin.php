<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class RedirectAfterInvitationLogin
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
        $response = $next($request);
        
        // Si l'utilisateur vient de se connecter/s'inscrire et qu'il y a un token d'invitation en session
        if (Auth::check() && Session::has('invitation_token')) {
            $token = Session::pull('invitation_token'); // Récupérer et supprimer le token
            $email = Session::pull('invitation_email'); // Récupérer et supprimer l'email
            
            // Vérifier que l'email de l'utilisateur correspond à celui de l'invitation
            $user = Auth::user();
            if ($user->email === $email) {
                // Rediriger vers l'acceptation d'invitation
                return redirect()->route('invitations.accept', ['token' => $token]);
            } else {
                // Si les emails ne correspondent pas, rediriger vers le dashboard avec un message d'erreur
                return redirect()->route('dashboard')
                    ->withErrors(['error' => 'L\'invitation ne correspond pas à votre adresse email.']);
            }
        }
        
        return $response;
    }
}
