@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50 dark:bg-slate-950">

    @if($employeeDashboard)

        {{-- ========================================================= --}}
        {{-- ESPACE EMPLOYÉ --}}
        {{-- ========================================================= --}}

        <div class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">

            <div class="mx-auto max-w-7xl px-6 py-6 lg:px-8">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <p class="text-sm font-medium text-blue-600">
                            Smart Entry
                        </p>

                        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                            Accueil
                        </h1>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Bonjour {{ auth()->user()->name }} 👋
                        </p>

                    </div>


                    {{-- BOUTON MON POINTAGE --}}
                    <a
                        href="{{ route('my_attendance') }}"
                        class="inline-flex items-center justify-center rounded-lg
                               bg-blue-600 px-5 py-3 text-sm font-semibold
                               text-white shadow-sm hover:bg-blue-700"
                    >
                        🕐 Mon pointage
                    </a>

                </div>

            </div>

        </div>


        {{-- CONTENU EMPLOYÉ --}}
        <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

            {{-- MESSAGE --}}
            <div class="mb-8 rounded-xl border border-blue-200
                        bg-blue-50 p-6
                        dark:border-blue-900/50
                        dark:bg-blue-500/10">

                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center
                                justify-center rounded-xl
                                bg-blue-600 text-white text-xl">
                        👋
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900 dark:text-white">
                            Bienvenue sur votre espace personnel
                        </h2>

                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                            Consultez votre pointage et vos dernières activités.
                        </p>

                    </div>

                </div>

            </div>


            {{-- ACTIVITÉS RÉCENTES --}}
            <div class="rounded-xl border border-slate-200
                        bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-900">

                <div class="flex items-center justify-between
                            border-b border-slate-200
                            px-6 py-5
                            dark:border-slate-800">

                    <div>

                        <h2 class="font-semibold text-slate-900 dark:text-white">
                            Mes activités récentes
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Vos derniers pointages
                        </p>

                    </div>

                    <a
                        href="{{ route('my_attendance') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        Voir mon pointage
                    </a>

                </div>


                @if($recentMovements->count())

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        @foreach($recentMovements as $movement)

                            <div class="flex items-center gap-4 px-6 py-5">

                                {{-- ICÔNE --}}
                                <div
                                    class="flex h-11 w-11 shrink-0
                                           items-center justify-center
                                           rounded-full

                                           {{ $movement->type === 'entry'
                                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10'
                                                : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10' }}"
                                >

                                    @if($movement->type === 'entry')
                                        ↓
                                    @else
                                        ↑
                                    @endif

                                </div>


                                {{-- INFORMATIONS --}}
                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold
                                              text-slate-900 dark:text-white">

                                        @if($movement->type === 'entry')
                                            Entrée enregistrée
                                        @else
                                            Sortie enregistrée
                                        @endif

                                    </p>


                                    <p class="mt-1 text-xs text-slate-500">

                                        @if($movement->accessPoint)
                                            {{ $movement->accessPoint->name }}
                                        @else
                                            Point d'accès
                                        @endif

                                        · {{ strtoupper($movement->method) }}

                                    </p>

                                </div>


                                {{-- DATE ET HEURE --}}
                                <div class="text-right">

                                    <p class="text-sm font-semibold
                                              text-slate-700 dark:text-slate-300">

                                        {{ $movement->occurred_at->format('H:i') }}

                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">

                                        {{ $movement->occurred_at->format('d/m/Y') }}

                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="px-6 py-14 text-center">

                        <div
                            class="mx-auto flex h-14 w-14
                                   items-center justify-center
                                   rounded-full
                                   bg-slate-100 text-slate-400
                                   dark:bg-slate-800"
                        >

                            <svg
                                class="h-7 w-7"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3"
                                />
                            </svg>

                        </div>


                        <h3 class="mt-4 text-sm font-semibold
                                   text-slate-900 dark:text-white">

                            Aucune activité

                        </h3>


                        <p class="mt-1 text-sm text-slate-500">

                            Vous n'avez encore enregistré aucun pointage.

                        </p>


                        <a
                            href="{{ route('my_attendance') }}"
                            class="mt-5 inline-flex items-center
                                   rounded-lg bg-blue-600
                                   px-4 py-2.5 text-sm
                                   font-semibold text-white
                                   hover:bg-blue-700"
                        >
                            Pointer maintenant
                        </a>

                    </div>

                @endif

            </div>


            {{-- PETITE CARTE POINTAGE --}}
            <div class="mt-6 rounded-xl border border-slate-200
                        bg-white p-6 shadow-sm
                        dark:border-slate-800 dark:bg-slate-900">

                <div class="flex flex-col gap-4 sm:flex-row
                            sm:items-center sm:justify-between">

                    <div>

                        <h2 class="font-semibold text-slate-900 dark:text-white">
                            Besoin de pointer ?
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Enregistrez votre entrée ou votre sortie.
                        </p>

                    </div>


                    <a
                        href="{{ route('my_attendance') }}"
                        class="inline-flex items-center justify-center
                               rounded-lg bg-blue-600
                               px-5 py-3 text-sm font-semibold
                               text-white hover:bg-blue-700"
                    >
                        Accéder au pointage →
                    </a>

                </div>

            </div>

        </main>


    @else

        {{-- ========================================================= --}}
        {{-- ESPACE ADMINISTRATEUR --}}
        {{-- ========================================================= --}}

        <div class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">

            <div class="mx-auto max-w-7xl px-6 py-6 lg:px-8">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <p class="text-sm font-medium text-blue-600">
                            Smart Entry
                        </p>

                        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                            Tableau de bord
                        </h1>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Bonjour {{ auth()->user()->name }} 👋
                        </p>

                    </div>

                    <div class="flex gap-3">

                        <a
                            href="{{ route('my_attendance') }}"
                            class="inline-flex items-center rounded-lg
                                   bg-blue-600 px-4 py-2.5 text-sm
                                   font-semibold text-white
                                   shadow-sm hover:bg-blue-700"
                        >
                            Mon pointage
                        </a>

                        <a
                            href="{{ route('visitors.create') }}"
                            class="inline-flex items-center rounded-lg
                                   border border-slate-300 bg-white
                                   px-4 py-2.5 text-sm font-semibold
                                   text-slate-700 hover:bg-slate-50
                                   dark:border-slate-700
                                   dark:bg-slate-900
                                   dark:text-slate-200
                                   dark:hover:bg-slate-800"
                        >
                            Nouveau visiteur
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

            {{-- STATISTIQUES ADMIN --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                {{-- EMPLOYÉS --}}
                <a
                    href="{{ route('employees.index') }}"
                    class="group rounded-xl border border-slate-200
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-0.5 hover:shadow-md
                           dark:border-slate-800 dark:bg-slate-900"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Employés actifs
                            </p>

                            <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ $stats['employees'] }}
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-blue-50 text-blue-600
                                    dark:bg-blue-500/10">

                            👥

                        </div>

                    </div>

                    <p class="mt-4 text-xs text-slate-400">
                        Personnel actif
                    </p>

                </a>


                {{-- VISITEURS --}}
                <a
                    href="{{ route('visitors.index') }}"
                    class="group rounded-xl border border-slate-200
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-0.5 hover:shadow-md
                           dark:border-slate-800 dark:bg-slate-900"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Visiteurs
                            </p>

                            <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ $stats['visitors'] }}
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-violet-50 text-violet-600
                                    dark:bg-violet-500/10">

                            👤

                        </div>

                    </div>

                    <p class="mt-4 text-xs text-slate-400">
                        Visiteurs enregistrés
                    </p>

                </a>


                {{-- MOUVEMENTS --}}
                <a
                    href="{{ route('movements.index') }}"
                    class="group rounded-xl border border-slate-200
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-0.5 hover:shadow-md
                           dark:border-slate-800 dark:bg-slate-900"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Mouvements
                            </p>

                            <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ $stats['movements'] }}
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-emerald-50 text-emerald-600
                                    dark:bg-emerald-500/10">

                            ↔

                        </div>

                    </div>

                    <p class="mt-4 text-xs text-slate-400">
                        Entrées et sorties
                    </p>

                </a>


                {{-- ANOMALIES --}}
                <div
                    class="rounded-xl border border-slate-200
                           bg-white p-6 shadow-sm
                           dark:border-slate-800 dark:bg-slate-900"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Anomalies
                            </p>

                            <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ $stats['anomalies'] }}
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-red-50 text-red-600
                                    dark:bg-red-500/10">

                            ⚠

                        </div>

                    </div>

                    <p class="mt-4 text-xs text-red-500">
                        Nécessite une attention
                    </p>

                </div>

            </div>


            {{-- ACTIVITÉS ADMIN --}}
            <div class="mt-8 rounded-xl border border-slate-200
                        bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-900">

                <div class="flex items-center justify-between
                            border-b border-slate-200 px-6 py-5
                            dark:border-slate-800">

                    <div>

                        <h2 class="font-semibold text-slate-900 dark:text-white">
                            Activité récente
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Derniers mouvements enregistrés
                        </p>

                    </div>

                    <a
                        href="{{ route('movements.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        Tout voir
                    </a>

                </div>


                @if($recentMovements->count())

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        @foreach($recentMovements as $movement)

                            <div class="flex items-center gap-4 px-6 py-4">

                                <div
                                    class="flex h-10 w-10 shrink-0
                                           items-center justify-center
                                           rounded-full
                                           {{ $movement->type === 'entry'
                                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10'
                                                : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10' }}"
                                >

                                    @if($movement->type === 'entry')
                                        ↓
                                    @else
                                        ↑
                                    @endif

                                </div>


                                <div class="min-w-0 flex-1">

                                    @php
                                        $person = $movement->employee
                                            ? $movement->employee->first_name . ' ' . $movement->employee->last_name
                                            : ($movement->visitor
                                                ? $movement->visitor->first_name . ' ' . $movement->visitor->last_name
                                                : 'Personne inconnue');
                                    @endphp

                                    <p class="truncate text-sm font-medium
                                              text-slate-900 dark:text-white">
                                        {{ $person }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">

                                        @if($movement->type === 'entry')
                                            Entrée
                                        @else
                                            Sortie
                                        @endif

                                        @if($movement->accessPoint)
                                            · {{ $movement->accessPoint->name }}
                                        @endif

                                        · {{ strtoupper($movement->method) }}

                                    </p>

                                </div>


                                <div class="text-right">

                                    <p class="text-xs font-medium
                                              text-slate-700 dark:text-slate-300">

                                        {{ $movement->occurred_at->format('H:i') }}

                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $movement->occurred_at->format('d/m/Y') }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="px-6 py-12 text-center text-sm text-slate-500">
                        Aucun mouvement enregistré.
                    </div>

                @endif

            </div>

        </main>

    @endif

</div>

@endsection