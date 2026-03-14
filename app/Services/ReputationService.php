<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Payment;
use App\Models\User;

class ReputationService
{
    /**
     * Calcule la balance d'un user dans une colocation.
     * Intègre les dépenses ET les payments wallet.
     *
     * balance = paid - share - received + sent
     */
    public function getUserBalance(User $user, Colocation $colocation): float
    {
        $members       = $colocation->members()->count();
        $totalExpenses = (float) $colocation->expenses()->sum('amount');
        $share         = $members > 0 ? $totalExpenses / $members : 0;

        $paid = (float) $colocation->expenses()
            ->where('paid_by', $user->id)
            ->sum('amount');

        $sent = (float) Payment::where('from_user_id', $user->id)
            ->where('colocation_id', $colocation->id)
            ->sum('amount');

        $received = (float) Payment::where('to_user_id', $user->id)
            ->where('colocation_id', $colocation->id)
            ->sum('amount');

        return round($paid - $share - $received + $sent, 2);
    }

    /**
     * Applique la réputation quand un MEMBER quitte volontairement.
     *
     * R3 : balance < 0  → -1
     * R4 : balance >= 0 → +1
     * R1 : 0 dépenses   → 0 (rien à évaluer)
     */
    public function applyOnMemberLeave(User $user, Colocation $colocation): int
    {
        // R1 — Aucune dépense dans la coloc → pas d'évaluation
        $totalExpenses = (float) $colocation->expenses()->sum('amount');
        if ($totalExpenses <= 0) {
            return 0; // pas de changement
        }

        $balance = $this->getUserBalance($user, $colocation);

        if ($balance < 0) {
            // R3 — Il part avec une dette
            $user->decrement('reputation');
            return -1;
        } else {
            // R4 — Il part sans dette (balance = 0 ou positif)
            $user->increment('reputation');
            return +1;
        }
    }

    /**
     * Applique la réputation quand l'OWNER annule la colocation.
     * Appelée pour CHAQUE membre actif (R9).
     *
     * R7 : 0 dépenses        → 0 pour tout le monde
     * R2 : owner seul + 0 dép → 0
     * R5 : owner balance < 0  → -1
     * R6 : owner balance >= 0 ET dépenses > 0 → +1
     * R9 : appliquer R3/R4 pour chaque membre aussi
     */
    public function applyOnColocationCancel(Colocation $colocation): void
    {
        $totalExpenses = (float) $colocation->expenses()->sum('amount');
        $members       = $colocation->members()->with('user')->get();
        $memberCount   = $members->count();

        // R7 + R2 — 0 dépenses → pas d'évaluation pour personne
        if ($totalExpenses <= 0) {
            return; // rien ne change
        }

        // R9 — Appliquer la règle pour chaque membre actif
        foreach ($members as $membership) {
            $user    = $membership->user;
            $balance = $this->getUserBalance($user, $colocation);

            if ($membership->role === 'owner') {
                // R5 / R6 — Règles spécifiques à l'owner
                if ($balance < 0) {
                    $user->decrement('reputation'); // R5
                } else {
                    $user->increment('reputation');  // R6
                }
            } else {
                // R3 / R4 — Règles standard pour les membres
                if ($balance < 0) {
                    $user->decrement('reputation'); // R3
                } else {
                    $user->increment('reputation');  // R4
                }
            }
        }
    }

    /**
     * Applique la réputation quand l'OWNER RETIRE un membre.
     *
     * R8 — Si le membre a une dette :
     *       → dette imputée à l'owner (0 pénalité pour le membre retiré)
     *       → l'owner prend -1 de réputation pour avoir retiré un débiteur
     *      Si le membre n'a pas de dette :
     *       → +1 pour le membre retiré (il part propre)
     *       → pas de changement pour l'owner
     */
    public function applyOnMemberRemoved(User $member, User $owner, Colocation $colocation): int
    {
        // R1 — Pas de dépenses → pas d'évaluation
        $totalExpenses = (float) $colocation->expenses()->sum('amount');
        if ($totalExpenses <= 0) {
            return 0;
        }

        $memberBalance = $this->getUserBalance($member, $colocation);

        if ($memberBalance < 0) {
            // R8 — Membre retiré avec dette :
            // → pas de pénalité pour le membre (c'est l'owner qui l'a forcé)
            // → -1 pour l'owner (il a choisi de retirer quelqu'un qui devait)
            $owner->decrement('reputation');
            return -1; // pénalité owner
        } else {
            // Membre retiré sans dette → +1 pour le membre
            $member->increment('reputation');
            return +1;
        }
    }
}
