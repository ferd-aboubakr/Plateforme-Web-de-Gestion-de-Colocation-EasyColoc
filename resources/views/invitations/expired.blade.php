@extends('layouts.guest')
@section('title', 'Invitation expirée')

@section('content')
<div class="max-w-md mx-auto mt-20">
  <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
    <div class="text-6xl mb-6">⏰</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Invitation expirée</h1>
    
    <div class="text-xl font-bold text-red-600 mb-2">{{ $invitation->colocation->name }}</div>
    <p class="text-gray-600 mb-6">Cette invitation a expiré le {{ $invitation->expires_at->format('d/m/Y à H:i') }}.</p>
    <p class="text-sm text-gray-500 mb-6">Veuillez contacter {{ $invitation->colocation->owner->name }} pour recevoir une nouvelle invitation.</p>
    
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
      ⚠️ Cette invitation n'est plus valide.
    </div>
    
    <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
      🏠 Retour au dashboard
    </a>
  </div>
</div>
@endsection
