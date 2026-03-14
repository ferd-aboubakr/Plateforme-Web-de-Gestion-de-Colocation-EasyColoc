@extends('layouts.guest')
@section('title', 'Email incorrect')

@section('content')
<div class="max-w-md mx-auto mt-20">
  <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
    <div class="text-6xl mb-6">📧</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Email incorrect</h1>
    
    <div class="text-xl font-bold text-orange-600 mb-2">{{ $invitation->colocation->name }}</div>
    <p class="text-gray-600 mb-6">Cette invitation est destinée à : <strong>{{ $invitation->email }}</strong></p>
    <p class="text-sm text-gray-500 mb-6">Vous êtes connecté avec : <strong>{{ Auth::user()->email }}</strong></p>
    
    <div class="bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 rounded-xl mb-6">
      ⚠️ Cette invitation ne vous est pas destinée.
    </div>
    
    <div class="space-y-3">
      <a href="{{ route('dashboard') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold inline-block">
        🏠 Retour au dashboard
      </a>
      <a href="{{ route('logout') }}" class="w-full bg-gray-100 text-gray-600 px-6 py-3 rounded-lg font-semibold inline-block">
        🚪 Se déconnecter
      </a>
    </div>
  </div>
</div>
@endsection
