<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Membership;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function send(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut inviter.');
        }

        $request->validate(['email' => 'required|email']);

        // Pas de doublon en attente
        $existing = $colocation->invitations()
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'Une invitation est déjà en attente pour cet email.']);
        }

        $invitation = $colocation->invitations()->create([
            'email'      => $request->email,
            'token'      => Str::uuid(),
            'status'     => 'pending',
            'expires_at' => now()->addDays(7),
        ]);


        $invitation->load('colocation.owner');

        // Envoyer l'email (commenté pour le moment)
        // Mail::to($request->email)
        //     ->send(new InvitationMail($invitation));

        $invitationLink = route('invitations.show', $invitation->token);

        return back()->with('success', 'Invitation envoyée avec succès à ' . $request->email . ' !')
                     ->with('invitation_link', $invitationLink);
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->with('colocation.owner')
            ->firstOrFail();

        // Si expiré → message d'erreur
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return view('invitations.show', compact('invitation'));
        }

        // Si non connecté → stocker le token pour après register/login
        if (! auth()->check()) {
            session(['invitation_token' => $token]);
        }

        return view('invitations.show', compact('invitation'));
    }

    public function accept(string $token)
    {
        // Vérifier que le token existe et est en attente
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->with('colocation.owner')
            ->firstOrFail();

        // Vérifier l'expiration
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => "Cette invitation a expiré."]);
        }

        // ── CAS 1 : User NON connecté ──────────────────────────────
        // Stocker le token en session et rediriger vers register
        if (! auth()->check()) {
            session(['invitation_token' => $token]);

            return redirect()->route('register')
                ->with('invitation_info', [
                    'colocation' => $invitation->colocation->name,
                    'owner'      => $invitation->colocation->owner->name,
                    'email'      => $invitation->email,
                ]);
        }

        // ── CAS 2 : User connecté ──────────────────────────────────
        $user = auth()->user();

        // Vérifier que l'email correspond
        if ($user->email !== $invitation->email) {
            return redirect()->route('dashboard')
                ->withErrors([
                    'error' => "Cette invitation est destinée à {$invitation->email}. "
                             . "Vous êtes connecté avec {$user->email}.",
                ]);
        }

        // Vérifier qu'il n'a pas déjà une colocation active
        if ($user->activeMembership) {
            return redirect()->route('dashboard')
                ->withErrors([
                    'error' => 'Vous avez déjà une colocation active. '
                             . 'Quittez-la avant d\'en rejoindre une autre.',
                ]);
        }

        // Créer le membership
        return $this->createMembership($user, $invitation);
    }

    // ── Méthode privée réutilisée dans les 2 cas ──────────────────
    private function createMembership($user, Invitation $invitation)
    {
        Membership::create([
            'user_id'       => $user->id,
            'colocation_id' => $invitation->colocation_id,
            'role'          => 'member',
            'joined_at'     => now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()
            ->route('colocations.show', $invitation->colocation_id)
            ->with('success',
                '🎉 Bienvenue dans la colocation "' . $invitation->colocation->name . '" !');
    }

    public function refuse(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')
            ->with('success', "Vous avez refusé l'invitation.");
    }
}
