@extends('layouts.app')
@section('title', 'Mon Wallet')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- SOLDE ACTUEL --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Solde disponible</p>
                <p class="text-4xl font-extrabold mt-1 {{ $wallet->balance > 0 ? 'text-green-600' : 'text-gray-400' }}">
                    {{ number_format($wallet->balance, 2) }} €
                </p>
            </div>
            <div class="text-5xl">💰</div>
        </div>
    </div>

    {{-- MESSAGES FLASH --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    {{-- FORMULAIRE DÉPÔT --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-800 mb-4">💳 Faire un dépôt</h2>
        <form method="POST" action="{{ route('wallet.deposit') }}" class="flex gap-3">
            @csrf
            <div class="flex-1">
                <input
                    type="number"
                    name="amount"
                    min="1"
                    max="10000"
                    step="0.01"
                    placeholder="Montant en €"
                    value="{{ old('amount') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                >
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors text-sm">
                Déposer
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-3">
            ⚠️ Simulation uniquement — aucun vrai paiement.
            Minimum 1 € · Maximum 10 000 €
        </p>
    </div>

    {{-- HISTORIQUE DES TRANSACTIONS --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">📋 Historique des transactions</h2>
        </div>

        @forelse($transactions as $tx)
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50">
            <div class="flex items-center gap-3">
                <span class="text-2xl">
                    @if($tx->type === 'deposit') 💳
                    @elseif($tx->type === 'payment_sent') 📤
                    @else 📥
                    @endif
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $tx->description }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $tx->created_at->format('d/m/Y à H:i') }}
                        · Solde après : {{ number_format($tx->balance_after, 2) }} €
                    </p>
                </div>
            </div>
            <span class="font-bold text-sm {{
                $tx->type === 'deposit' || $tx->type === 'payment_received'
                    ? 'text-green-600' : 'text-red-600'
            }}">
                {{ $tx->type === 'payment_sent' ? '-' : '+' }}{{ number_format($tx->amount, 2) }} €
            </span>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-gray-400">
            <p class="text-3xl mb-2">📭</p>
            <p class="text-sm">Aucune transaction pour le moment.</p>
        </div>
        @endforelse

        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
