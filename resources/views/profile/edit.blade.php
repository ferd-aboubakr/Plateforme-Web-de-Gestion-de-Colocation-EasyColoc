@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="max-w-7xl mx-auto">
  <h1 class="text-2xl font-bold text-gray-800 mb-8">👤 Mon Profil</h1>
  
  @if($current_section === 'info')
    <!-- SECTION INFO -->
    <section class="mb-12">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Informations personnelles</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                <div class="text-lg font-medium text-gray-900">{{ $user->name }}</div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <div class="text-lg font-medium text-gray-900">{{ $user->email }}</div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Réputation</label>
                <div class="text-lg font-medium text-yellow-600">★ {{ $user->reputation }}</div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <div>
                  @if($user->banned_at)
                    <span class="bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full">Banni</span>
                  @else
                    <span class="bg-green-100 text-green-700 text-sm font-semibold px-3 py-1 rounded-full">Actif</span>
                  @endif
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date d'inscription</label>
                <div class="text-lg font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</div>
              </div>
            </div>
          </div>
          
          <div>
            <div class="space-y-6">
              <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">📊 Statistiques rapides</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Colocations rejointes</span>
                    <span class="font-medium">{{ $user->memberships()->count() }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Colocation active</span>
                    <span class="font-medium">{{ $user->activeMembership ? 'Oui' : 'Non' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Total dépenses payées</span>
                    <span class="font-medium">{{ number_format($user->expenses()->sum('amount'), 2) }} €</span>
                  </div>
                </div>
              </div>
              
              <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">⚙️ Actions rapides</h3>
                <div class="space-y-3">
                  <a href="{{ route('profile.edit', ['section' => 'colocations']) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    🏠 Voir mes colocations
                  </a>
                  <a href="{{ route('profile.edit', ['section' => 'expenses']) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    💰 Voir mes dépenses
                  </a>
                  <a href="{{ route('profile.edit', ['section' => 'activity']) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    📈 Voir mon activité
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif
  
  @if($current_section === 'colocations')
    <!-- SECTION COLOCATIONS -->
    <section class="mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-6">🏠 Mes Colocations</h2>
      
      @if($memberships->count() > 0)
        <div class="space-y-6">
          @foreach($memberships as $membership)
            <div class="bg-white rounded-xl shadow-lg p-6 border {{ $membership->left_at ? 'border-red-200' : 'border-gray-200' }}">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">{{ $membership->colocation->name }}</h3>
                  @if($membership->colocation->address)
                    <p class="text-gray-600">{{ $membership->colocation->address }}</p>
                  @endif
                </div>
                <div>
                  @if($membership->left_at)
                    <span class="bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full">Quittée</span>
                  @else
                    <span class="bg-green-100 text-green-700 text-sm font-semibold px-3 py-1 rounded-full">Active</span>
                  @endif
                </div>
              </div>
              
              <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                  <span class="text-gray-500">Rôle</span>
                  <div class="font-medium">{{ $membership->role === 'owner' ? 'Propriétaire' : 'Membre' }}</div>
                </div>
                <div>
                  <span class="text-gray-500">Rejoint le</span>
                  <div class="font-medium">{{ $membership->joined_at->format('d/m/Y') }}</div>
                </div>
                @if($membership->left_at)
                  <div>
                    <span class="text-gray-500">Quitté le</span>
                    <div class="font-medium">{{ $membership->left_at->format('d/m/Y') }}</div>
                  </div>
                @endif
                <div>
                  <span class="text-gray-500">Owner</span>
                  <div class="font-medium">{{ $membership->colocation->owner->name }}</div>
                </div>
              </div>
              
              @if(!$membership->left_at)
                <div class="mt-4 pt-4 border-t border-gray-200">
                  <a href="{{ route('colocations.show', $membership->colocation_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Voir la colocation
                  </a>
                </div>
              @endif
            </div>
          @endforeach
        </div>
      @else
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
          <div class="text-6xl mb-4">🏠</div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune colocation</h3>
          <p class="text-gray-600 mb-6">Vous n'avez pas encore rejoint de colocation.</p>
          <a href="{{ route('colocations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
            ➕ Créer une colocation
          </a>
        </div>
      @endif
    </section>
  @endif
  
  @if($current_section === 'expenses')
    <!-- SECTION EXPENSES -->
    <section class="mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-6">💰 Mes Dépenses</h2>
      
      @if(isset($expenses))
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Titre</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Montant</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Catégorie</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Colocation</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($expenses as $expense)
                <tr>
                  <td class="px-4 py-3 border-b border-gray-100">{{ $expense->expense_date->format('d/m/Y') }}</td>
                  <td class="px-4 py-3 border-b border-gray-100 font-medium">{{ $expense->title }}</td>
                  <td class="px-4 py-3 border-b border-gray-100 font-bold text-blue-600">{{ number_format($expense->amount, 2) }} €</td>
                  <td class="px-4 py-3 border-b border-gray-100">
                    @if($expense->category)
                      <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $expense->category->name }}</span>
                    @else
                      <span class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded">Autre</span>
                    @endif
                  </td>
                  <td class="px-4 py-3 border-b border-gray-100">{{ $expense->colocation->name }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="mt-6">
          @if(method_exists($expenses, 'links'))
            {{ $expenses->links() }}
          @endif
        </div>
        
        <div class="mt-4 text-center text-gray-500">
          Total: {{ number_format(method_exists($expenses, 'total') ? $expenses->total() : $expenses->sum('amount'), 2) }} € en {{ method_exists($expenses, 'count') ? $expenses->count() : $expenses->count() }} dépenses
        </div>
      @else
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
          <div class="text-6xl mb-4">💰</div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune dépense</h3>
          <p class="text-gray-600 mb-6">Vous n'avez pas encore enregistré de dépense.</p>
          <p class="text-sm text-gray-500">Rejoignez une colocation pour commencer à ajouter des dépenses.</p>
        </div>
      @endif
    </section>
  @endif
  
  @if($current_section === 'activity')
    <!-- SECTION ACTIVITY -->
    <section class="mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-6">📈 Mon Activité</h2>
      
      @if($recentActivities)
        <div class="space-y-4">
          @foreach($recentActivities as $activity)
            <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200">
              <div class="flex items-start space-x-4">
                <div class="text-2xl">{{ $activity['icon'] }}</div>
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ $activity['description'] }}</div>
                  <div class="text-sm text-gray-500 mt-1">
                    @if(isset($activity['colocation']))
                      <span class="font-medium">{{ $activity['colocation'] }}</span> • 
                    @endif
                    {{ $activity['date'] }}
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
          <div class="text-6xl mb-4">📈</div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune activité</h3>
          <p class="text-gray-600">Votre historique d'activité apparaîtra ici.</p>
        </div>
      @endif
    </section>
  @endif
</div>
@endsection
