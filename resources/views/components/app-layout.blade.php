<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Entry') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-white">

    <div class="min-h-screen">

        {{-- NAVIGATION --}}
        <nav class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">

            <div class="mx-auto flex h-16 max-w-[1600px] items-center justify-between px-6 lg:px-8">

                {{-- LOGO --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">

                        <svg class="h-5 w-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5.5 20a6.5 6.5 0 0113 0"/>
                        </svg>

                    </div>

                    <div class="hidden sm:block">
                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                            Smart Entry
                        </div>

                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Access Control
                        </div>
                    </div>

                </a>


                {{-- NAVIGATION CENTRALE --}}
                <div class="hidden items-center gap-1 md:flex">

                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}
                              rounded-lg px-3 py-2 text-sm font-medium transition">

                        Dashboard

                    </a>


                    <a href="{{ route('my_attendance') }}"
                       class="{{ request()->routeIs('my_attendance*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}
                              rounded-lg px-3 py-2 text-sm font-medium transition">

                        Mon pointage

                    </a>


                    <a href="{{ route('employees.index') }}"
                       class="{{ request()->routeIs('employees.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}
                              rounded-lg px-3 py-2 text-sm font-medium transition">

                        Employés

                    </a>


                    <a href="{{ route('visitors.index') }}"
                       class="{{ request()->routeIs('visitors.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}
                              rounded-lg px-3 py-2 text-sm font-medium transition">

                        Visiteurs

                    </a>


                    <a href="{{ route('movements.index') }}"
                       class="{{ request()->routeIs('movements.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}
                              rounded-lg px-3 py-2 text-sm font-medium transition">

                        Mouvements

                    </a>


                    <a href="{{ route('qr_codes.index') }}"
                       class="{{ request()->routeIs('qr_codes.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}
                              rounded-lg px-3 py-2 text-sm font-medium transition">

                        QR Codes

                    </a>

                </div>


                {{-- UTILISATEUR --}}
                <div class="flex items-center gap-3">

                    <div class="hidden text-right sm:block">

                        <p class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ ucfirst(auth()->user()->role ?? 'Utilisateur') }}
                        </p>

                    </div>


                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>


                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button type="submit"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-red-50 hover:text-red-600 dark:text-slate-400 dark:hover:bg-red-500/10 dark:hover:text-red-400">

                            Déconnexion

                        </button>

                    </form>

                </div>

            </div>

        </nav>


        {{-- CONTENU DE LA PAGE --}}
        <main>
            {{ $slot }}
        </main>

    </div>

    @stack('scripts')

</body>
</html>