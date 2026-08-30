<nav class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
    <div class="mx-auto flex min-h-16 max-w-[1600px] items-center px-6 py-3">

        {{-- ============================================================
             LOGO
        ============================================================ --}}
        <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3">

            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z"
                    />
                </svg>
            </div>

            <div>
                <div class="text-sm font-bold text-slate-900 dark:text-white">
                    Smart Entry
                </div>

                <div class="text-[11px] text-slate-500">
                    Access Control
                </div>
            </div>

        </a>


        {{-- ============================================================
             NAVIGATION CENTRALE
        ============================================================ --}}
        <div class="ml-10 flex flex-1 items-center gap-2">

            {{-- ========================================================
                 ADMINISTRATEUR
            ========================================================= --}}
            @if(auth()->user()->isAdmin())

                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('dashboard')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Tableau de bord
                </a>

                <a
                    href="{{ route('employees.index') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('employees.*')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Employés
                </a>

                <a
                    href="{{ route('visitors.index') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('visitors.*')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Visiteurs
                </a>

                <a
                    href="{{ route('movements.index') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('movements.*')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Mouvements
                </a>

                <a
                    href="{{ route('departments.index') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('departments.*')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Départements
                </a>


            {{-- ========================================================
                 EMPLOYÉ
            ========================================================= --}}
            @else

                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('dashboard')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Accueil
                </a>

                <a
                    href="{{ route('my_attendance') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('my_attendance')
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    Mon pointage
                </a>

            @endif

        </div>


        {{-- ============================================================
             PARTIE DROITE
        ============================================================ --}}
        <div class="flex shrink-0 items-center gap-4">

            {{-- ========================================================
                 NOTIFICATIONS
            ========================================================= --}}
            <button
                type="button"
                class="relative text-slate-500 transition hover:text-slate-900 dark:hover:text-white"
                title="Notifications"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"
                    />
                </svg>

                <span class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-red-500"></span>

            </button>


            {{-- SÉPARATEUR --}}
            <div class="hidden h-6 w-px bg-slate-200 dark:bg-slate-800 sm:block"></div>


            {{-- ========================================================
                 UTILISATEUR CONNECTÉ
            ========================================================= --}}
            <div class="flex items-center gap-3">

                <div class="hidden text-right sm:block">

                    <p class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ auth()->user()->isAdmin() ? 'Administrateur' : 'Employé' }}
                    </p>

                </div>


                {{-- INITIALe --}}
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white"
                >
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </div>


            {{-- ========================================================
                 DÉCONNEXION
            ========================================================= --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-400 dark:hover:bg-red-950/50"
                >

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5m5 5H3"
                        />
                    </svg>

                    Déconnexion

                </button>
            </form>

        </div>

    </div>
</nav>

