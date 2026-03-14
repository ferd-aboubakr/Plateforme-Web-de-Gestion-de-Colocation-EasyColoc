@extends('layouts.guest')
@section('title', 'Compte suspendu')

@section('content')
<div class="max-w-md mx-auto mt-20">
  <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
    <h1 class="text-2xl font-bold text-red-600 mb-4">Compte suspendu</h1>
    <p class="text-gray-600 mb-8">Votre compte a été suspendu par un administrateur.</p>
    <p class="text-gray-600 mb-8">Si vous pensez qu'il s'agit d'une erreur, contactez-nous.</p>
    
    <form method="POST" action="{{ route('logout') }}" class="inline">
      @csrf
      <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold">
        Se déconnecter
      </button>
    </form>
  </div>
</div>
@endsection
