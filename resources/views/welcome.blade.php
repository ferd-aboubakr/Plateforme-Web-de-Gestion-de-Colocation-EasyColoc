<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>EasyColoc - Partagez vos dépenses</title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'light-blue': {
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                200: '#bae6fd',
                                300: '#7dd3fc',
                                400: '#38bdf8',
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                                800: '#075985',
                                900: '#0c4a6e',
                            },
                            primary: {
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                200: '#bae6fd',
                                300: '#7dd3fc',
                                400: '#38bdf8',
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                                800: '#075985',
                                900: '#0c4a6e',
                            }
                        },
                    },
                }
            }
        </script>
    </head>
    <body class="bg-light-blue-50 min-h-screen">
        <!-- Navigation -->
        <header class="bg-white shadow-sm">
            <nav class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-primary-600">EasyColoc</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                                    Tableau de bord
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-light-blue-700 hover:text-primary-600">
                                    Connexion
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                                        S'inscrire
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-light-blue-900 mb-4">
                    Partagez vos dépenses
                    <span class="text-primary-600">Facilement</span>
                </h1>
                <p class="text-xl text-light-blue-700 mb-8">
                    Suivez les factures, partagez les coûts et gérez vos colocataires dans une application simple.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('register') }}" class="bg-primary-600 text-white px-6 py-3 rounded hover:bg-primary-700">
                        Commencer
                    </a>
                    <a href="{{ route('login') }}" class="border border-primary-600 text-primary-600 px-6 py-3 rounded hover:bg-light-blue-50">
                        Connexion
                    </a>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-center text-light-blue-900 mb-8">
                    Ce que vous pouvez faire
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-light-blue-200">
                        <h3 class="font-semibold text-light-blue-900 mb-2">Suivre les dépenses</h3>
                        <p class="text-light-blue-700">Ajoutez des factures et voyez qui doit quoi</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-light-blue-200">
                        <h3 class="font-semibold text-light-blue-900 mb-2">Gérer les colocataires</h3>
                        <p class="text-light-blue-700">Invitez des amis et gérez l'accès</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-light-blue-200">
                        <h3 class="font-semibold text-light-blue-900 mb-2">Voir le tableau de bord</h3>
                        <p class="text-light-blue-700">Consultez les soldes et les tendances de dépenses</p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="mt-16 text-center">
                <div class="bg-primary-600 rounded-lg p-8 text-white">
                    <h2 class="text-2xl font-bold mb-4">Prêt à commencer ?</h2>
                    <p class="mb-6">Rejoignez des milliers de colocataires qui gèrent leurs dépenses ensemble.</p>
                    <a href="{{ route('register') }}" class="bg-white text-primary-600 px-6 py-3 rounded hover:bg-light-blue-50">
                        Créer un compte
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-light-blue-200 mt-16">
            <div class="max-w-4xl mx-auto px-4 py-8 text-center">
                <p class="text-light-blue-600">© 2026 EasyColoc. Partage simple des dépenses.</p>
            </div>
        </footer>
    </body>
</html>
