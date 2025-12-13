<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-xl font-bold text-indigo-600">BóvedaDocs</span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                    class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('documents.index') }}"
                                    class="{{ request()->routeIs('documents.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Documentos
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('categories.index') }}"
                                        class="{{ request()->routeIs('categories.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Categorías
                                    </a>
                                @endif
                                @if(Auth::user()->isAdmin() || Auth::user()->isDependency())
                                    <a href="{{ route('web.access-requests.index') }}"
                                        class="{{ request()->routeIs('web.access-requests.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Solicitudes
                                    </a>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ url('/api/documentation') }}" target="_blank"
                                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        API Docs
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="flex items-center">
                        @auth
                            <div class="ml-3 relative">
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Salir</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Iniciar
                                Sesión</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">&copy; {{ date('Y') }} Bóveda de Documentos. Proyecto
                    Universitario.</p>
            </div>
        </footer>
    </div>
</body>

</html>