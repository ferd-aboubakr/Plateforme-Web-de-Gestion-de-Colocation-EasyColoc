<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $data = [
                'user_count'       => User::count(),
                'colocation_count' => Colocation::count(),
                'expense_total'    => Expense::sum('amount'),
                'banned_count'     => User::whereNotNull('banned_at')->count(),
                'allUser'          => User::with('roles')->get(),
                'colocations'      => Colocation::with('owner')->withCount('members')->latest()->get(),
            ];
            return view('dashboard', compact('data'));
        }

        // User normal
        $membership = $user->activeMembership;
        $colocation = $membership?->colocation()->with(['members.user', 'expenses'])->first();
        $balance    = null;
        $recentExpenses = collect();
        $settlements = [];

        if ($colocation) {
            $balances    = $this->calculateBalances($colocation);
            $settlements = $this->calculateSettlements($balances);
            $balance     = $balances[$user->id]['balance'] ?? null;
            $recentExpenses = $colocation->expenses()
                ->with(['payer', 'category'])
                ->orderBy('expense_date', 'desc')
                ->limit(3)
                ->get();
        }

        return view('dashboard', compact('colocation', 'balance', 'recentExpenses', 'settlements'));
    }

    public function calculateBalances(Colocation $colocation): array
{
    $members       = $colocation->members()->with('user')->get();
    $totalExpenses = $colocation->expenses()->sum('amount');
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
}
