@extends('layouts.app')
@section('title', 'Modifier la colocation')

@section('content')
<div class="max-w-lg mx-auto">
  <div class="bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Modifier la colocation</h1>
    
    <form method="POST" action="{{ route('colocations.update', $colocation) }}" class="space-y-6">
      @csrf
      @method('PATCH')
      
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          required 
          maxlength="255"
          value="{{ old('name', $colocation->name) }}"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        >
        @error('name')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
      
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
        <input 
          type="text" 
          id="address" 
          name="address" 
          value="{{ old('address', $colocation->address) }}"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        >
      </div>
      
      <div class="flex justify-between items-center pt-4">
        <a href="{{ route('colocations.show', $colocation) }}" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50">
          ← Annuler
        </a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
