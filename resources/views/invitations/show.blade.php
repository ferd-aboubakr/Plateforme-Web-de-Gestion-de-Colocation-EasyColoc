@extends('layouts.guest')
@section('title', 'Invitation')

@section('content')
<div class="max-w-md mx-auto mt-20">
  <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Invitation à rejoindre</h1>
    
    <div class="text-2xl font-bold text-blue-600 mb-2">{{ $invitation->colocation->name }}</div>
    <p class="text-gray-600 mb-6">Vous avez été invité(e) par {{ $invitation->colocation->owner->name }}</p>
    <p class="text-sm text-gray-500 mb-6">Cette invitation est destinée à : {{ $invitation->email }}</p>
    
    @if($invitation->expires_at && $invitation->expires_at->isPast())
      <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
        Cette invitation a expiré.
      </div>
      <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
        Retour
      </a>
    @else
      @if($invitation->expires_at)
        <p class="text-sm text-gray-500 mb-6">
          Valable jusqu'au {{ $invitation->expires_at->format('d/m/Y') }}
        </p>
      @endif
      
      <div class="flex gap-3">
        @if(auth()->check())
            <a href="{{ route('invitations.accept', $invitation->token) }}"
               class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl text-center transition-colors">
                Accepter
            </a>
            <a href="{{ route('invitations.refuse', $invitation->token) }}"
               class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl text-center transition-colors">
                Refuser
            </a>
        @else
            <a href="{{ route('register') }}"
               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-center transition-colors">
                Créer un compte pour accepter
            </a>
            <a href="{{ route('login') }}"
               class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl text-center transition-colors">
                J'ai déjà un compte
            </a>
        @endif
      </div>
    @endif
  </div>
</div>
@endsection
