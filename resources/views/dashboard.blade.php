@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@role('admin')
  <div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-primary-700 mb-6">Tableau de bord Administration</h1>
    
    <div class="grid grid-cols-4 gap-6 mb-8">
      <x-stat-card title="Utilisateurs" value="{{ $data['user_count'] }}" icon="" color="blue" />
      <x-stat-card title="Colocations" value="{{ $data['colocation_count'] }}" icon="" color="green" />
      <x-stat-card title="Total dépenses" value="{{ $data['expense_total'] }} €" icon="" color="yellow" />
      <x-stat-card title="Bannis" value="{{ $data['banned_count'] }}" icon="" color="red" />
    </div>
    
    <div class="bg-white rounded-xl p-4 border border-light-blue-200">
      <a href="{{ route('admin.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
        → Voir le panel admin complet
      </a>
    </div>
  </div>
@else
  @if($colocation)
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-bold text-light-blue-800 mb-6">Bonjour, {{ auth()->user()->name }}</h1>
      
      <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-4 border border-light-blue-200">
          <div class="text-sm text-light-blue-600">Mon solde</div>
          <div class="text-xl font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ number_format($balance, 2) }}€
          </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-light-blue-200">
          <div class="text-sm text-light-blue-600">Membres</div>
          <div class="text-xl font-bold text-light-blue-800">{{ $colocation->members()->count() }}</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-light-blue-200">
          <div class="text-sm text-light-blue-600">Ma coloc</div>
          <div class="text-xl font-bold text-light-blue-800">{{ $colocation->name }}</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-light-blue-200">
          <div class="text-sm text-light-blue-600">Réputation</div>
          <div class="text-xl font-bold text-light-blue-800">{{ auth()->user()->reputation }}</div>
        </div>
      </div>
      
      @if($balance !== null && $balance < 0)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl mb-6">
          Attention: Vous devez {{ abs($balance) }}€. Réglez vos dettes !
          <a href="{{ route('colocations.show', $colocation) }}" class="underline ml-2">Voir ma coloc</a>
        </div>
      @endif
      
      <div class="bg-white rounded-xl p-6 border border-gray-200 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Dernières dépenses</h2>
        <div class="space-y-3">
          @foreach($recentExpenses->take(3) as $expense)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  @if($expense->category)
                    <span class="bg-gray-100 text-xs px-2 py-1 rounded">{{ $expense->category->name }}</span>
                  @endif
                  <span class="font-medium">{{ $expense->title }}</span>
                </div>
                <div class="text-sm text-gray-500">Payé par {{ $expense->payer->name }}</div>
              </div>
              <div class="text-right">
                <div class="font-bold text-blue-600">{{ number_format($expense->amount, 2) }}€</div>
                <div class="text-xs text-gray-400">{{ $expense->expense_date->format('d/m/Y') }}</div>
              </div>
            </div>
          @endforeach
        </div>
        <a href="{{ route('colocations.show', $colocation) }}" class="text-blue-600 hover:text-blue-800 font-medium mt-4 inline-block">
          Voir toutes les dépenses →
        </a>
      </div>
      
      <div class="bg-white rounded-xl p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Dettes en cours</h2>
        @forelse($settlements as $s)
          @if($s['from']->id === auth()->id())
            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg mb-3">
              <span>Vous devez {{ number_format($s['amount'], 2) }}€ à {{ $s['to']->name }}</span>
              <form method="POST" action="{{ route('payments.store') }}" class="inline">
                @csrf
                <input type="hidden" name="from_user_id" value="{{ $s['from']->id }}">
                <input type="hidden" name="to_user_id" value="{{ $s['to']->id }}">
                <input type="hidden" name="amount" value="{{ $s['amount'] }}">
                <input type="hidden" name="colocation_id" value="{{ $colocation->id }}">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                  ✅ Marquer payé
                </button>
              </form>
            </div>
          @endif
        @empty
          <p class="text-green-600 font-medium">🎉 Vous êtes à jour !</p>
        @endforelse
      </div>
      
      <div class="fixed bottom-6 right-6">
        <a href="{{ route('colocations.show', $colocation) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-lg font-medium">
          → Ma colocation
        </a>
      </div>
    </div>
  @else
    <div class="max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Bienvenue, {{ auth()->user()->name }} !</h1>
      <p class="text-gray-600 mb-8">Vous n'avez pas encore de colocation active.</p>
      
      <div class="bg-blue-50 rounded-xl p-8 border-2 border-dashed border-blue-300 text-center mb-8">
        <div class="text-6xl mb-4"></div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Créer votre première colocation</h2>
        <a href="{{ route('colocations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold text-lg inline-block">
          Créer une colocation
        </a>
        <p class="text-gray-600 mt-4">Ou attendez une invitation par email</p>
      </div>
      
      <div class="bg-white rounded-xl p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Votre réputation</h3>
        <div class="text-2xl font-bold text-yellow-600 mb-2">★ {{ auth()->user()->reputation }}</div>
        <p class="text-sm text-gray-600">+1 quand vous quittez sans dettes, -1 avec dettes</p>
      </div>
    </div>
  @endif
@endrole
@endsection
