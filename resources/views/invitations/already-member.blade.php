@extends('layouts.guest')
@section('title', 'Déjà membre')

@section('content')
<div class="max-w-md mx-auto mt-20">
  <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
    <div class="text-6xl mb-6">🏠</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Déjà membre d\'une colocation</h1>
    
    <div class="text-xl font-bold text-orange-600 mb-2">{{ $invitation->colocation->name }}</div>
    <p class="text-gray-600 mb-6">Vous avez déjà une colocation active.</p>
    <p class="text-sm text-gray-500 mb-6">Pour rejoindre cette colocation, vous devez d\'abord quitter votre colocation actuelle.</p>
    
    <div class="bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 rounded-xl mb-6">
      ⚠️ Un utilisateur ne peut appartenir qu\'à une seule colocation active.
    </div>
    
    <div class="space-y-3">
      <a href="{{ route('dashboard') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold inline-block">
        🏠 Voir ma colocation
      </a>
      <a href="{{ route('colocations.show', Auth::user()->activeMembership->colocation_id) }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold inline-block">
        👥 Gérer ma colocation
      </a>
    </div>
  </div>
</div>
@endsection
