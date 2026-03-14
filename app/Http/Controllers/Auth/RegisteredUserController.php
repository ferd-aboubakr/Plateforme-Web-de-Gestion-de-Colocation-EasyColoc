<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if (User::count() === 1) {
            $user->assignRole('admin');
        } else {
            $user->assignRole('user');
        }

        // Créer le wallet pour le nouvel utilisateur
        \App\Models\Wallet::create([
            'user_id' => $user->id,
            'balance' => 0.00,
        ]);

        event(new Registered($user));

        Auth::login(user: $user);

        // ── Vérifier si une invitation est en attente en session ──────
        if (session()->has('invitation_token')) {
            $token = session()->pull('invitation_token'); // pull = get + delete

            $invitation = \App\Models\Invitation::where('token', $token)
                ->where('status', 'pending')
                ->with('colocation.owner')
                ->first();

            // Invitation valide + email correspond + pas expirée
            if ($invitation
                && $invitation->email === $user->email
                && (! $invitation->expires_at || ! $invitation->expires_at->isPast())
            ) {
                // Créer le membership
                \App\Models\Membership::create([
                    'user_id'       => $user->id,
                    'colocation_id' => $invitation->colocation_id,
                    'role'          => 'member',
                    'joined_at'     => now(),
                ]);

                $invitation->update(['status' => 'accepted']);

                return redirect()
                    ->route('colocations.show', $invitation->colocation_id)
                    ->with('success',
                        '🎉 Compte créé et bienvenue dans "' . $invitation->colocation->name . '" !');
            }

            // Si l'email ne correspond pas → message d'avertissement
            if ($invitation && $invitation->email !== $user->email) {
                return redirect()
                    ->route('dashboard')
                    ->with('warning',
                        "⚠️ L'invitation était pour {$invitation->email} "
                        . "mais vous vous êtes inscrit avec {$user->email}. "
                        . "Contactez l'owner de la colocation pour une nouvelle invitation.");
            }
        }

        // Redirect normal si pas d'invitation
        return redirect()->route('dashboard');
    }
}
