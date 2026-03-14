@extends('layouts.app')
@section('title', $colocation->name)

@section('content')
<div class="max-w-7xl mx-auto">
  <!-- SECTION 1 : EN-TÊTE -->
  <div class="flex justify-between items-start mb-8">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 mb-2">🏠 {{ $colocation->name }}</h1>
      @if($colocation->address)
        <p class="text-gray-600">{{ $colocation->address }}</p>
      @endif
      <div class="mt-2">
        @if($colocation->status === 'active')
          <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
        @else
          <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Annulée</span>
        @endif
      </div>
    </div>
    
    <div class="flex gap-2">
      @if(auth()->id() === $colocation->owner_id)
        <a href="#invite" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
          ✉️ Inviter
        </a>
        <form method="POST" action="{{ route('colocations.cancel', $colocation) }}" class="inline" 
              x-on:click="confirm('Annuler la colocation {{ $colocation->name }} ? Cette action est irréversible !') || $event.preventDefault()">
          @csrf
          <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
            Annuler la coloc
          </button>
        </form>
      @else
        <form method="POST" action="{{ route('colocations.leave', $colocation) }}" class="inline"
              x-on:click="confirm('Voulez-vous vraiment quitter la colocation {{ $colocation->name }} ?') || $event.preventDefault()">
          @csrf
          <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
            Quitter la colocation
          </button>
        </form>
      @endif
    </div>
  </div>
  
  <!-- SECTION 2 : MEMBRES -->
  <section class="mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">👥 Membres ({{ $colocation->members->count() }})</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      @foreach($colocation->members as $membership)
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="text-lg font-bold text-blue-600">{{ strtoupper(substr($membership->user->name, 0, 1)) }}</span>
          </div>
          <div class="font-medium">{{ $membership->user->name }}</div>
          <div class="mb-2">
            @if($membership->role === 'owner')
              <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">owner</span>
            @else
              <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">member</span>
            @endif
          </div>
          <div class="text-sm text-yellow-600 font-semibold">★ {{ $membership->user->reputation }}</div>
          
          @if(auth()->id() === $colocation->owner_id && $membership->user_id !== auth()->id())
            <form method="POST" action="{{ route('colocations.removeMember', [$colocation, $membership->user]) }}" class="mt-3"
                  x-on:click="confirm('Retirer {{ $membership->user->name }} de la colocation ?') || $event.preventDefault()">
              @csrf
              @method('DELETE')
              <button type="submit" class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                Retirer
              </button>
            </form>
          @endif
        </div>
      @endforeach
    </div>
  </section>
  
  <!-- SECTION 3 : AJOUTER DÉPENSE -->
  <section class="mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">➕ Ajouter une dépense</h2>
    <form method="POST" action="{{ route('colocations.expenses.store', $colocation) }}" class="bg-white rounded-xl p-6 border border-gray-200">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            required 
            value="{{ old('title') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
        </div>
        
        <div>
          <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Montant (€) *</label>
          <input 
            type="number" 
            id="amount" 
            name="amount" 
            step="0.01" 
            min="0.01"
            value="{{ old('amount') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
        </div>
        
        <div>
          <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
          <input 
            type="date" 
            id="expense_date" 
            name="expense_date" 
            max="{{ date('Y-m-d') }}" 
            value="{{ old('expense_date', date('Y-m-d')) }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
        </div>
        
        <div>
          <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
          <select 
            id="category_id" 
            name="category_id" 
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
            <option value="">— Aucune —</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
        </div>
        
        <div>
          <label for="paid_by" class="block text-sm font-medium text-gray-700 mb-2">Payé par *</label>
          <select 
            id="paid_by" 
            name="paid_by" 
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
            @foreach($colocation->members as $membership)
              <option value="{{ $membership->user_id }}" {{ old('paid_by') == $membership->user_id || ($membership->user_id == auth()->id() && !old('paid_by')) ? 'selected' : '' }}>
                {{ $membership->user->name }}{{ $membership->user_id == auth()->id() ? ' (moi)' : '' }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      
      <div class="mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg">
          Ajouter
        </button>
      </div>
    </form>
  </section>
  
  <!-- SECTION 4 : BALANCES -->
  <section class="mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Balances</h2>
    <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Membre</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Total payé</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Sa part</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Solde</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($balances as $b)
            <tr>
              <td class="px-4 py-3 border-b border-gray-100">
                {{ $b['user']->name }}
                @if($b['user']->id === auth()->id())
                  <span class="text-gray-400 text-sm ml-2">(moi)</span>
                @endif
              </td>
              <td class="px-4 py-3 border-b border-gray-100">{{ number_format($b['paid'], 2) }} €</td>
              <td class="px-4 py-3 border-b border-gray-100">{{ number_format($b['share'], 2) }} €</td>
              <td class="px-4 py-3 border-b border-gray-100">
                <span class="font-bold {{ $b['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                  {{ $b['balance'] >= 0 ? '+' : '' }}{{ number_format($b['balance'], 2) }} €
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
  
  <!-- SECTION 5 : SETTLEMENTS -->
  {{-- Section Settlements --}}
@forelse($settlements as $s)
<div class="flex flex-wrap items-center gap-3 bg-white rounded-xl border border-gray-200 p-4">

    {{-- Qui doit à qui --}}
    <span class="font-bold text-red-600">{{ $s['from']->name }}</span>
    <span class="text-gray-500 text-sm">doit</span>
    <span class="font-bold text-gray-800">{{ number_format($s['amount'], 2) }} €</span>
    <span class="text-gray-500 text-sm">à</span>
    <span class="font-bold text-green-600">{{ $s['to']->name }}</span>

    {{-- Bouton Marquer payé (si débiteur ou owner) --}}
    @if(auth()->id() === $s['from']->id || auth()->id() === $colocation->owner_id)

        {{-- Solde wallet du débiteur --}}
        @php $walletBalance = $s['from']->wallet?->balance ?? 0; @endphp

        <div class="ml-auto flex items-center gap-3">

            {{-- Badge solde wallet --}}
            <span class="text-xs px-3 py-1.5 rounded-full font-bold
                {{ $walletBalance >= $s['amount']
                    ? 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 shadow-sm'
                    : 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300 shadow-sm' }}">
                💰 <span class="font-mono">{{ number_format($walletBalance, 2) }}</span> €
            </span>

            @if($walletBalance >= $s['amount'])
                {{-- Solde suffisant → bouton actif --}}
                <form method="POST" action="{{ route('payments.store') }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="from_user_id" value="{{ $s['from']->id }}">
                    <input type="hidden" name="to_user_id"   value="{{ $s['to']->id }}">
                    <input type="hidden" name="amount"        value="{{ $s['amount'] }}">
                    <input type="hidden" name="colocation_id" value="{{ $colocation->id }}">
                    <button
                        x-on:click="confirm('Confirmer le paiement de {{ number_format($s['amount'], 2) }} € à {{ $s['to']->name }} ?\n\nVotre solde passera de {{ number_format($walletBalance, 2) }} € à {{ number_format($walletBalance - $s['amount'], 2) }} €') || $event.preventDefault()"
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-8-8a1 1 0 011.414-1.414l8 8a1 1 0 001.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Payer <span class="font-mono">{{ number_format($s['amount'], 2) }}</span> €
                    </button>
                </form>
            @else
                {{-- Solde insuffisant → lien vers wallet --}}
                <a href="{{ route('wallet.index') }}"
                   class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1a2 2 0 002 2h1a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v1a2 2 0 002 2h1a2 2 0 002-2V6a2 2 0 00-2-2H4z"/>
                        <path d="M9 9a1 1 0 012 1v6a1 1 0 11-2 0V9a1 1 0 112-1z"/>
                        <path d="M5 8a1 1 0 011-1v6a1 1 0 11-2 0V8a1 1 0 012-1z"/>
                    </svg>
                    Recharger <span class="font-mono">{{ number_format($s['amount'] - $walletBalance, 2) }}</span> €
                </a>
            @endif

        </div>
    @endif

</div>
@empty
<div class="text-center py-6">
    <p class="text-green-600 font-semibold">🎉 Tout le monde est quitte !</p>
</div>
@endforelse
  
  <!-- SECTION 6 : LISTE DÉPENSES -->
  <section class="mb-8">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold text-gray-800">🧾 Dépenses</h2>
      
      <!-- Filtre mois -->
      <select onchange="window.location='?month='+this.value" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Tous les mois</option>
        @foreach($availableMonths as $month)
          <option value="{{ $month }}" {{ $selectedMonth === $month ? 'selected' : '' }}>
            {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
          </option>
        @endforeach
      </select>
    </div>
    
    @forelse($expenses as $expense)
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 mb-2">
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              @if($expense->category)
                <span class="bg-gray-100 text-xs px-2 py-1 rounded">{{ $expense->category->name }}</span>
              @endif
              <span class="font-medium">{{ $expense->title }}</span>
            </div>
            <div class="text-sm text-gray-500">Payé par {{ $expense->payer->name }}</div>
            <div class="text-xs text-gray-400">{{ $expense->expense_date->format('d/m/Y') }}</div>
          </div>
          <div class="text-right">
            <div class="font-bold text-blue-600">{{ number_format($expense->amount, 2) }} €</div>
            @if(auth()->id() === $expense->paid_by || auth()->id() === $colocation->owner_id)
              <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="mt-2 inline"
                    x-on:click="confirm('Supprimer la dépense {{ $expense->title }} ?') || $event.preventDefault()">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                  🗑️
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    @empty
      <p class="text-gray-400 text-center py-8">Aucune dépense pour cette période.</p>
    @endforelse
  </section>
  
  <!-- SECTION 7 : INVITATION (owner only) -->
  @if(auth()->id() === $colocation->owner_id)
    <section id="invite" class="mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">✉️ Inviter un membre</h2>
      <form method="POST" action="{{ route('invitations.send', $colocation) }}" class="bg-white rounded-xl p-6 border border-gray-200">
        @csrf
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            placeholder="email@exemple.fr"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg">
          Envoyer l'invitation
        </button>
      </form>
    </section>
  @endif
</div>
@endsection
