<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\Category;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = Colocation::with('owner')->get();
        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        return view('colocations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if (Auth::user()->activeMembership) {
            return back()->withErrors(['error' => 'Vous avez déjà une colocation active.']);
        }

        $coloc = Colocation::create([
            'name'     => $request->name,
            'address'  => $request->address,
            'owner_id' => Auth::id(),
            'status'   => 'active',
        ]);

        Membership::create([
            'user_id'       => Auth::id(),
            'colocation_id' => $coloc->id,
            'role'          => 'owner',
            'joined_at'     => now(),
        ]);

        return redirect()->route('colocations.show', $coloc)
            ->with('success', 'Colocation créée avec succès !');
    }

    public function show(Colocation $colocation, Request $request)
    {
        $selectedMonth = $request->get('month');

        $expensesQuery = $colocation->expenses()->with(['payer', 'category']);

        if ($selectedMonth) {
            $expensesQuery
                ->whereYear('expense_date', substr($selectedMonth, 0, 4))
                ->whereMonth('expense_date', substr($selectedMonth, 5, 2));
        }

        $expenses = $expensesQuery->orderBy('expense_date', 'desc')->get();

        $availableMonths = $colocation->expenses()
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        $balances    = $this->calculateBalances($colocation);
        $settlements = $this->calculateSettlements($balances);
        $categories  = Category::all();

        $colocation->load('members.user');

        return view('colocations.show', compact(
            'colocation', 'expenses', 'balances',
            'settlements', 'categories', 'selectedMonth', 'availableMonths'
        ));
    }

    public function edit(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut modifier.');
        }
        return view('colocations.edit', compact('colocation'));
    }

    public function update(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $colocation->update($request->only('name', 'address'));

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation mise à jour.');
    }

    public function destroy(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }
        $colocation->delete();
        return redirect()->route('dashboard')->with('success', 'Colocation supprimée.');
    }

    public function cancel(Request $request, Colocation $colocation)
{
    $user = Auth::user();

    if ($colocation->owner_id !== $user->id) {
        abort(403, 'Seul l\'owner peut annuler la colocation.');
    }

    if ($colocation->status === 'cancelled') {
        return back()->withErrors(['error' => 'Cette colocation est déjà annulée.']);
    }

    // Appliquer la réputation pour TOUS les membres actifs (R9)
    // AVANT de changer le statut (sinon les membres() renvoient vide)
    app(ReputationService::class)->applyOnColocationCancel($colocation);

    // Marquer tous les membres comme partis
    $colocation->members()->whereNull('left_at')->update(['left_at' => now()]);

    // Annuler la colocation
    $colocation->update([
        'status'       => 'cancelled',
        'cancelled_at' => now(),
    ]);

    return redirect()->route('dashboard')
        ->with('success', 'Colocation annulée. Les réputations ont été mises à jour.');
}

    public function leave(Request $request, Colocation $colocation)
{
    $user = Auth::user();

    // Vérifications de base
    $membership = $colocation->members()
        ->where('user_id', $user->id)
        ->first();

    if (! $membership) {
        return back()->withErrors(['error' => 'Vous n\'êtes pas membre de cette colocation.']);
    }

    if ($membership->role === 'owner') {
        return back()->withErrors(['error' => 'L\'owner ne peut pas quitter. Utilisez "Annuler la colocation".']);
    }

    // Appliquer la réputation AVANT de marquer left_at
    $reputation = app(ReputationService::class)->applyOnMemberLeave($user, $colocation);

    // Marquer le membre comme parti
    $membership->update(['left_at' => now()]);

    $messages = [
        +1 => 'Vous avez quitté la colocation. +1 réputation 🎉',
         0 => 'Vous avez quitté la colocation.',
        -1 => 'Vous avez quitté la colocation avec une dette. -1 réputation.',
    ];

    return redirect()->route('dashboard')
        ->with('success', $messages[$reputation] ?? 'Vous avez quitté la colocation.');
}

    public function removeMember(Request $request, Colocation $colocation, User $user)
{
    $owner = Auth::user();

    // Vérifications
    if ($colocation->owner_id !== $owner->id) {
        abort(403, 'Seul l\'owner peut retirer des membres.');
    }

    if ($user->id === $owner->id) {
        return back()->withErrors(['error' => 'L\'owner ne peut pas se retirer lui-même.']);
    }

    $membership = $colocation->members()
        ->where('user_id', $user->id)
        ->whereNull('left_at')
        ->first();

    if (! $membership) {
        return back()->withErrors(['error' => 'Ce membre n\'est pas actif dans cette colocation.']);
    }

    // Appliquer la réputation (R8)
    $result = app(ReputationService::class)->applyOnMemberRemoved($user, $owner, $colocation);

    // Marquer le membre comme parti
    $membership->update(['left_at' => now()]);

    $message = $result === -1
        ? $user->name . ' a été retiré. Sa dette est imputée à vous (-1 réputation).'
        : $user->name . ' a été retiré proprement (+1 réputation pour lui).';

    return back()->with('success', $message);
}

    public function calculateBalances(Colocation $colocation): array
{
    $members       = $colocation->members()->with('user')->get();
    $totalExpenses = (float) $colocation->expenses()->sum('amount');
    $count         = $members->count();
    $share         = $count > 0 ? $totalExpenses / $count : 0;

    $balances = [];

    foreach ($members as $membership) {
        $u = $membership->user;

        // Ce que ce membre a avancé pour les dépenses
        $paid = (float) $colocation->expenses()
            ->where('paid_by', $u->id)
            ->sum('amount');

        // Payments envoyés par ce membre → réduit sa DETTE
        $sent = (float) \App\Models\Payment
            ::where('from_user_id', $u->id)
            ->where('colocation_id', $colocation->id)
            ->sum('amount');

        // Payments reçus par ce membre → réduit son CRÉDIT
        $received = (float) \App\Models\Payment
            ::where('to_user_id', $u->id)
            ->where('colocation_id', $colocation->id)
            ->sum('amount');

        // ✅ FORMULE CORRECTE : paid - share - received + sent
        // ❌ MAUVAISE formule : paid - share + received - sent  ← ne pas utiliser
        $balance = round($paid - $share - $received + $sent, 2);

        $balances[$u->id] = [
            'user'    => $u,
            'paid'    => round($paid, 2),
            'share'   => round((float) $share, 2),
            'balance' => $balance,
        ];
    }

    return $balances;
}

    public function calculateSettlements(array $balances): array
{
    $debtors   = [];
    $creditors = [];

    foreach ($balances as $userId => $data) {
        if ($data['balance'] < -0.01) {
            $debtors[$userId] = abs($data['balance']);
        } elseif ($data['balance'] > 0.01) {
            $creditors[$userId] = $data['balance'];
        }
        // balance entre -0.01 et +0.01 = considéré comme 0
    }

    $settlements = [];

    while (!empty($debtors) && !empty($creditors)) {
        $debtorId   = array_key_first($debtors);
        $creditorId = array_key_first($creditors);

        $amount = round(min($debtors[$debtorId], $creditors[$creditorId]), 2);

        $settlements[] = [
            'from'   => $balances[$debtorId]['user'],
            'to'     => $balances[$creditorId]['user'],
            'amount' => $amount,
        ];

        $debtors[$debtorId]     -= $amount;
        $creditors[$creditorId] -= $amount;

        if ($debtors[$debtorId] <= 0.01)     unset($debtors[$debtorId]);
        if ($creditors[$creditorId] <= 0.01) unset($creditors[$creditorId]);
    }

    return $settlements;
}

    public function getUserBalance(Colocation $colocation, int $userId): float
    {
        $count = $colocation->members()->count();
        if ($count === 0) return 0;
        $total = $colocation->expenses()->sum('amount');
        $paid  = $colocation->expenses()->where('paid_by', $userId)->sum('amount');
        return round((float)$paid - ($total / $count), 2);
    }
}
