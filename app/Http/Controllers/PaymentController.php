<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'from_user_id'  => 'required|exists:users,id',
            'to_user_id'    => 'required|exists:users,id|different:from_user_id',
            'colocation_id' => 'required|exists:colocations,id',
            'amount'        => 'required|numeric|min:0.01',
        ]);

        $colocation = Colocation::findOrFail($request->colocation_id);
        $amount     = round((float) $request->amount, 2);
        $fromUser   = \App\Models\User::findOrFail($request->from_user_id);
        $toUser     = \App\Models\User::findOrFail($request->to_user_id);

        // ✅ VÉRIFICATION AUTORISATION
        if (Auth::id() !== $fromUser->id && Auth::id() !== $colocation->owner_id) {
            abort(403, 'Non autorisé.');
        }

        // ✅ VÉRIFICATION WALLET — solde suffisant
        $wallet = $fromUser->getOrCreateWallet();
        if (! $wallet->hasSufficientBalance($amount)) {
            return back()->withErrors([
                'wallet' => 'Solde insuffisant. Wallet : '
                    . number_format($wallet->balance, 2) . ' €'
                    . ' — Requis : ' . number_format($amount, 2) . ' €',
            ]);
        }

        // ✅ TRANSACTION ATOMIQUE — tout ou rien
        DB::transaction(function () use ($fromUser, $toUser, $colocation, $amount) {

            // 1. Créer le Payment (OBLIGATOIRE pour calculateBalances)
            $payment = Payment::create([
                'from_user_id'  => $fromUser->id,
                'to_user_id'    => $toUser->id,
                'colocation_id' => $colocation->id,
                'amount'        => $amount,
                'paid_at'       => now(),
            ]);

            // 2. Débiter le wallet du débiteur
            $walletFrom = $fromUser->getOrCreateWallet();
            $walletFrom->decrement('balance', $amount);
            $walletFrom->refresh();

            // 3. Créditer le wallet du créditeur
            $walletTo = $toUser->getOrCreateWallet();
            $walletTo->increment('balance', $amount);
            $walletTo->refresh();

            // 4. Enregistrer les WalletTransactions si la table existe
            if (\Illuminate\Support\Facades\Schema::hasTable('wallet_transactions')) {
                WalletTransaction::create([
                    'user_id'       => $fromUser->id,
                    'type'          => 'payment_sent',
                    'amount'        => $amount,
                    'balance_after' => $walletFrom->balance,
                    'description'   => 'Remboursement à ' . $toUser->name,
                    'payment_id'    => $payment->id,
                ]);

                WalletTransaction::create([
                    'user_id'       => $toUser->id,
                    'type'          => 'payment_received',
                    'amount'        => $amount,
                    'balance_after' => $walletTo->balance,
                    'description'   => 'Reçu de ' . $fromUser->name,
                    'payment_id'    => $payment->id,
                ]);
            }
        });

        return back()->with('success',
            number_format($amount, 2) . ' € envoyés à ' . $toUser->name . ' !'
        );
    }
}
