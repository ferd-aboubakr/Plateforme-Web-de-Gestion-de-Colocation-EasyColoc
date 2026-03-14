<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // ── Afficher le wallet de l'user connecté ─────────────────
    public function index()
    {
        $user   = Auth::user();
        $wallet = $user->getOrCreateWallet();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('wallet.index', compact('wallet', 'transactions'));
    }

    // ── Faire un dépôt ────────────────────────────────────────
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);

        $user   = Auth::user();
        $amount = round((float) $request->amount, 2);

        DB::transaction(function () use ($user, $amount) {
            $wallet = $user->getOrCreateWallet();

            // Créditer le wallet
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            // Enregistrer la transaction
            WalletTransaction::create([
                'user_id'       => $user->id,
                'type'          => 'deposit',
                'amount'        => $amount,
                'balance_after' => $wallet->balance,
                'description'   => 'Dépôt manuel de ' . number_format($amount, 2) . ' €',
            ]);
        });

        return back()->with('success', number_format($amount, 2) . ' € crédités sur votre wallet !');
    }
}
