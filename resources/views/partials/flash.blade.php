@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        ❌ {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        @foreach($errors->all() as $error)
            <p>❌ {{ $error }}</p>
        @endforeach
    </div>
@endif

@if(session('info'))
    <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
        ℹ️ {{ session('info') }}
    </div>
@endif

@if(session('warning'))
    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
        ⚠️ {{ session('warning') }}
    </div>
@endif
