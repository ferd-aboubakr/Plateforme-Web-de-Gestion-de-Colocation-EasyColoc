@extends('layouts.app')
@section('title', 'Administration')

@section('content')
<div class="max-w-7xl mx-auto">
  <h1 class="text-2xl font-bold text-red-700 mb-8">Administration</h1>
  
  @if($current_section === 'statistics')
    <!-- SECTION STATS -->
    <section class="mb-12">
      <div class="grid grid-cols-4 gap-6">
        <x-stat-card title="Total utilisateurs" value="{{ $stats['total_users'] }}" icon="" color="blue" />
        <x-stat-card title="Colocations actives" value="{{ $stats['active_colocs'] }}" icon="" color="green" />
        <x-stat-card title="Total dépenses" value="{{ $stats['expense_total'] }} €" icon="" color="yellow" />
        <x-stat-card title="Bannis" value="{{ $stats['banned_count'] }}" icon="" color="red" />
      </div>
    </section>
  @endif
  
  @if($current_section === 'users')
    <!-- SECTION UTILISATEURS -->
    <section class="mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-6">Utilisateurs ({{ $stats['total_users'] }})</h2>
      
      <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">#</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Nom</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Email</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Rôle</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Réputation</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Statut</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
              <tr>
                <td class="px-4 py-3 border-b border-gray-100">{{ $user->id }}</td>
                <td class="px-4 py-3 border-b border-gray-100 font-medium">{{ $user->name }}</td>
                <td class="px-4 py-3 border-b border-gray-100">{{ $user->email }}</td>
                <td class="px-4 py-3 border-b border-gray-100">
                  @if($user->hasRole('admin'))
                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">admin</span>
                  @else
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">user</span>
                  @endif
                </td>
                <td class="px-4 py-3 border-b border-gray-100">
                  <span class="text-yellow-600 font-semibold">★ {{ $user->reputation }}</span>
                </td>
                <td class="px-4 py-3 border-b border-gray-100">
                  @if($user->banned_at)
                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Banni</span>
                  @else
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Actif</span>
                  @endif
                </td>
                <td class="px-4 py-3 border-b border-gray-100">
                  @if($user->id === auth()->id())
                    <span class="text-gray-400 text-sm">(vous)</span>
                  @elseif($user->banned_at)
                    <form method="POST" action="{{ route('admin.unban', $user) }}" class="inline">
                      @csrf
                      <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                        Débannir
                      </button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.ban', $user) }}" class="inline" 
                          x-on:click="confirm('Bannir {{ $user->name }} ?') || $event.preventDefault()">
                      @csrf
                      <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium">
                        Bannir
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      {{ $users->links() }}
    </section>
  @endif
  
  @if($current_section === 'colocations')
    <!-- SECTION COLOCATIONS -->
    <section class="mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-6">Colocations</h2>
      
      <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">#</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Nom</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Owner</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Membres</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Total dépenses</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($colocations as $colocation)
              <tr>
                <td class="px-4 py-3 border-b border-gray-100">{{ $colocation->id }}</td>
                <td class="px-4 py-3 border-b border-gray-100 font-medium">{{ $colocation->name }}</td>
                <td class="px-4 py-3 border-b border-gray-100">{{ $colocation->owner->name }}</td>
                <td class="px-4 py-3 border-b border-gray-100">{{ $colocation->members->count() }}</td>
                <td class="px-4 py-3 border-b border-gray-100">{{ number_format($colocation->expenses->sum('amount'), 2) }} €</td>
                <td class="px-4 py-3 border-b border-gray-100">
                  @if($colocation->status === 'active')
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
                  @else
                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">Annulée</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      {{ $colocations->links() }}
    </section>
  @endif
  
  @if($current_section === 'banned')
    <!-- SECTION BANNIS -->
    <section class="mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-6">Utilisateurs bannis ({{ $stats['banned_count'] }})</h2>
      
      @if($banned_users->count() > 0)
        <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Nom</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Rôle</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Banni le</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($banned_users as $user)
                <tr>
                  <td class="px-4 py-3 border-b border-gray-100">{{ $user->id }}</td>
                  <td class="px-4 py-3 border-b border-gray-100 font-medium">{{ $user->name }}</td>
                  <td class="px-4 py-3 border-b border-gray-100">{{ $user->email }}</td>
                  <td class="px-4 py-3 border-b border-gray-100">
                    @if($user->hasRole('admin'))
                      <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">admin</span>
                    @else
                      <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">user</span>
                    @endif
                  </td>
                  <td class="px-4 py-3 border-b border-gray-100">{{ $user->banned_at->format('d/m/Y H:i') }}</td>
                  <td class="px-4 py-3 border-b border-gray-100">
                    <form method="POST" action="{{ route('admin.unban', $user) }}" class="inline">
                      @csrf
                      <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                        Débannir
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        {{ $banned_users->links() }}
      @else
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
          Aucun utilisateur banni pour le moment.
        </div>
      @endif
    </section>
  @endif
</div>
@endsection
