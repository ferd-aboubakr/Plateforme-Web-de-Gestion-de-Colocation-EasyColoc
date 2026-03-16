<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'EasyColoc'))</title>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="bg-light-blue-50 min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="h-14 bg-white border-b border-light-blue-200 shadow-sm flex items-center px-6">
            <div class="flex-1 flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-primary-600 text-xl font-bold hover:text-primary-700">
                    <span>EasyColoc</span>
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                @role('admin')
                    <a href="{{ route('admin.index') }}" class="bg-light-blue-100 text-primary-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-light-blue-200">
                        Admin
                    </a>
                @endrole
                
                <span class="text-light-blue-700">
                    {{ auth()->user()->name }} ({{ auth()->user()->reputation }})
                </span>
                
                <a href="{{ route('profile.edit') }}" class="text-light-blue-600 hover:text-primary-700">
                    Profil
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-light-blue-600 hover:text-primary-700">Déconnexion</button>
                </form>
            </div>
        </nav>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside class="w-60 bg-white border-r border-light-blue-200 min-h-screen sticky top-0">
                @role('admin')
                    <div class="p-4">
                        <h3 class="text-xs font-semibold text-light-blue-600 uppercase tracking-wider mb-3">ADMINISTRATION</h3>
                        <div class="space-y-1">
                            <a href="{{ route('admin.index', ['section' => 'statistics']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->get('section') == 'statistics' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Statistiques
                            </a>
                            <a href="{{ route('admin.index', ['section' => 'users']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->get('section') == 'users' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Utilisateurs
                            </a>
                            <a href="{{ route('admin.index', ['section' => 'colocations']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->get('section') == 'colocations' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Colocations
                            </a>
                            <a href="{{ route('admin.index', ['section' => 'banned']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->get('section') == 'banned' ? 'bg-red-50 text-red-600 border-l-2 border-red-600' : 'text-red-600 hover:bg-red-50' }}">
                                Bannis
                            </a>
                        </div>
                        
                        <h3 class="text-xs font-semibold text-light-blue-600 uppercase tracking-wider mb-3">MON ESPACE</h3>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('wallet.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('wallet.*') ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">€</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold">Mon Wallet</span>
                                        @php $adminWalletBalance = auth()->user()->wallet?->balance ?? 0; @endphp
                                        <span class="text-xs {{ $adminWalletBalance > 0 ? 'text-primary-600 font-bold' : 'text-light-blue-400' }}">
                                            Solde: <span class="font-mono">{{ number_format($adminWalletBalance, 2) }}</span> €
                                        </span>
                                    </div>
                                    <span class="ml-auto text-xs font-bold {{ $adminWalletBalance > 0 ? 'text-primary-600' : 'text-light-blue-400' }}">
                                        {{ number_format($adminWalletBalance, 2) }} €
                                    </span>
                                </div>
                            </a>
                            <a href="{{ route('profile.edit', ['section' => 'info']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'info' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Profil
                            </a>
                            @if(auth()->user()->activeMembership)
                                <a href="{{ route('colocations.show', auth()->user()->activeMembership->colocation_id) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('colocations.show') ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    Ma Colocation
                                </a>
                                <a href="{{ route('profile.edit', ['section' => 'colocations']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'colocations' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    Mes Colocations
                                </a>
                                <a href="{{ route('profile.edit', ['section' => 'expenses']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'expenses' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    € Mes Dépenses
                                </a>
                                <a href="{{ route('profile.edit', ['section' => 'activity']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'activity' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    Activité
                                </a>
                            @else
                                <h3 class="text-xs font-semibold text-light-blue-600 uppercase tracking-wider mb-3 mt-6">REJOINDRE</h3>
                                <div class="space-y-1">
                                    <a href="{{ route('colocations.create') }}" class="flex items-center px-3 py-2 text-sm rounded-lg text-green-600 hover:bg-green-50">
                                        Créer une colocation
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-4">
                        <h3 class="text-xs font-semibold text-light-blue-600 uppercase tracking-wider mb-3">MON ESPACE</h3>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('wallet.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('wallet.*') ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">€</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold">Mon Wallet</span>
                                        @php $userWalletBalance = auth()->user()->wallet?->balance ?? 0; @endphp
                                        <span class="text-xs {{ $userWalletBalance > 0 ? 'text-primary-600 font-bold' : 'text-light-blue-400' }}">
                                            Solde: <span class="font-mono">{{ number_format($userWalletBalance, 2) }}</span> €
                                        </span>
                                    </div>
                                    <span class="ml-auto text-xs font-bold {{ $userWalletBalance > 0 ? 'text-primary-600' : 'text-light-blue-400' }}">
                                        {{ number_format($userWalletBalance, 2) }} €
                                    </span>
                                </div>
                            </a>
                            <a href="{{ route('profile.edit', ['section' => 'info']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'info' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                Profil
                            </a>
                        </div>
                        
                        @if(auth()->user()->activeMembership)
                            <h3 class="text-xs font-semibold text-light-blue-600 uppercase tracking-wider mb-3 mt-6">MA COLOCATION</h3>
                            <div class="space-y-1">
                                <a href="{{ route('colocations.show', auth()->user()->activeMembership->colocation_id) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('colocations.show') ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    Voir ma coloc
                                </a>
                                <a href="{{ route('profile.edit', ['section' => 'colocations']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'colocations' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    Mes Colocations
                                </a>
                                <a href="{{ route('profile.edit', ['section' => 'expenses']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'expenses' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    € Mes Dépenses
                                </a>
                                <a href="{{ route('profile.edit', ['section' => 'activity']) }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') && request()->get('section') == 'activity' ? 'bg-primary-50 text-primary-600 border-l-2 border-primary-600' : 'text-light-blue-700 hover:bg-light-blue-50' }}">
                                    Activité
                                </a>
                            </div>
                        @else
                            <h3 class="text-xs font-semibold text-light-blue-600 uppercase tracking-wider mb-3 mt-6">REJOINDRE</h3>
                            <div class="space-y-1">
                                <a href="{{ route('colocations.create') }}" class="flex items-center px-3 py-2 text-sm rounded-lg text-green-600 hover:bg-green-50">
                                    Créer une colocation
                                </a>
                            </div>
                        @endif
                    </div>
                @endrole
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <!-- Flash Messages -->
                @if(session('invitation_link'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl mb-4">
                        <div class="font-semibold mb-3 text-lg">Lien d'invitation généré :</div>
                        <div class="bg-white p-4 rounded-lg border border-green-300">
                            <input type="text" value="{{ session('invitation_link') }}" readonly 
                                   class="w-full text-sm font-mono bg-transparent outline-none py-2 px-3" 
                                   onclick="this.select()">
                        </div>
                        <p class="text-sm mt-3">Copiez ce lien et envoyez-le directement à la personne invitée !</p>
                    </div>
                    {{ session()->forget('invitation_link') }}
                @endif
                
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
                        <span>{{ session('success') }}</span>
                    </div>
                    {{ session()->forget('success') }}
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
                        <span>{{ session('error') }}</span>
                    </div>
                    {{ session()->forget('error') }}
                @endif
                
                @if(session('warning'))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                        <span class="text-sm">{{ session('warning') }}</span>
                    </div>
                    {{ session()->forget('warning') }}
                @endif
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-light-blue-200 text-center text-sm text-light-blue-600 py-4">
            © 2026 EasyColoc
        </footer>
    </body>
</html>
