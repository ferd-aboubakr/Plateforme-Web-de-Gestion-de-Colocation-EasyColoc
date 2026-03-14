@extends('layouts.guest')
@section('title', 'Register')

@section('content')
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    {{-- Bannière invitation si vient d'une invitation --}}
    @if(session('invitation_info'))
        @php $info = session('invitation_info'); @endphp
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <span class="text-2xl">🏠</span>
                <div>
                    <p class="font-bold text-blue-800 text-sm">
                        Vous avez été invité(e) à rejoindre une colocation !
                    </p>
                    <p class="text-blue-600 text-sm mt-1">
                        <strong>{{ $info['colocation'] }}</strong>
                        — par <strong>{{ $info['owner'] }}</strong>
                    </p>
                    <p class="text-blue-500 text-xs mt-2">
                        ⚠️ Inscrivez-vous avec l'adresse
                        <strong>{{ $info['email'] }}</strong>
                        pour rejoindre automatiquement la colocation.
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Invitation Message -->
    @if(session('info'))
        <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
            {{ session('info') }}
        </div>
    @endif
    
    <!-- Pending Invitation Notice -->
    @if(session('invitation_token'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <strong>📧 Créez votre compte pour rejoindre :</strong> 
            @if(session('invitation_email'))
                Une invitation pour <strong>{{ session('invitation_email') }}</strong> vous attend.
            @else
                Une invitation vous attend.
            @endif
            Après avoir créé votre compte, vous serez redirigé automatiquement pour l'accepter.
        </div>
        
        <!-- Pré-remplir l'email si disponible -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const emailInput = document.getElementById('email');
                @if(session('invitation_email'))
                    emailInput.value = '{{ session('invitation_email') }}';
                    emailInput.readOnly = true;
                @endif
            });
        </script>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
@endsection
