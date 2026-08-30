@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-white px-6 py-8">

    {{-- En-tête --}}
    <div class="max-w-6xl mx-auto">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">
                Mon pointage
            </h1>

            <p class="text-slate-400 mt-2">
                Enregistrez votre entrée et votre sortie de travail.
            </p>
        </div>


        {{-- Carte principale --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            {{-- ENTRÉE --}}
            <div class="bg-[#172033] border border-slate-700 rounded-2xl p-6 shadow-xl">

                <div class="flex items-center justify-between mb-6">

                    <div>
                        <p class="text-slate-400 text-sm">
                            Entrée
                        </p>

                        <h2 class="text-2xl font-bold mt-1">
                            Pointer mon entrée
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-full bg-green-500/10 flex items-center justify-center">
                        <span class="text-3xl">🟢</span>
                    </div>

                </div>

                <p class="text-slate-400 mb-6">
                    Enregistrez votre arrivée dans l'entreprise.
                </p>

                <form method="POST" action="/attendance/check-in">
                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 transition
                               text-white font-semibold py-4 rounded-xl">
                        🟢 Pointer mon entrée
                    </button>
                </form>

            </div>


            {{-- SORTIE --}}
            <div class="bg-[#172033] border border-slate-700 rounded-2xl p-6 shadow-xl">

                <div class="flex items-center justify-between mb-6">

                    <div>
                        <p class="text-slate-400 text-sm">
                            Sortie
                        </p>

                        <h2 class="text-2xl font-bold mt-1">
                            Pointer ma sortie
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-full bg-red-500/10 flex items-center justify-center">
                        <span class="text-3xl">🔴</span>
                    </div>

                </div>

                <p class="text-slate-400 mb-6">
                    Enregistrez votre départ de l'entreprise.
                </p>

                <form method="POST" action="/attendance/check-out">
                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 transition
                               text-white font-semibold py-4 rounded-xl">
                        🔴 Pointer ma sortie
                    </button>
                </form>

            </div>

        </div>


        {{-- Situation du jour --}}
        <div class="bg-[#172033] border border-slate-700 rounded-2xl p-6 shadow-xl mb-8">

            <h2 class="text-xl font-bold mb-6">
                📅 Mon pointage aujourd'hui
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Entrée --}}
                <div class="bg-[#0f172a] rounded-xl p-5 border border-slate-700">

                    <p class="text-slate-400 text-sm">
                        Heure d'entrée
                    </p>

                    <p class="text-2xl font-bold mt-2 text-green-400">
                        {{ $todayEntry ?? '--:--' }}
                    </p>

                </div>


                {{-- Sortie --}}
                <div class="bg-[#0f172a] rounded-xl p-5 border border-slate-700">

                    <p class="text-slate-400 text-sm">
                        Heure de sortie
                    </p>

                    <p class="text-2xl font-bold mt-2 text-red-400">
                        {{ $todayExit ?? '--:--' }}
                    </p>

                </div>


                {{-- Statut --}}
                <div class="bg-[#0f172a] rounded-xl p-5 border border-slate-700">

                    <p class="text-slate-400 text-sm">
                        Statut
                    </p>

                    <p class="text-2xl font-bold mt-2">
                        {{ $status ?? 'Non pointé' }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Historique --}}
        <div class="bg-[#172033] border border-slate-700 rounded-2xl shadow-xl overflow-hidden">

            <div class="p-6 border-b border-slate-700">

                <h2 class="text-xl font-bold">
                    📋 Historique de mes pointages
                </h2>

                <p class="text-slate-400 text-sm mt-1">
                    Consultez vos derniers mouvements.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-[#0f172a]">

                        <tr>

                            <th class="text-left px-6 py-4 text-slate-400 text-sm">
                                Date
                            </th>

                            <th class="text-left px-6 py-4 text-slate-400 text-sm">
                                Entrée
                            </th>

                            <th class="text-left px-6 py-4 text-slate-400 text-sm">
                                Sortie
                            </th>

                            <th class="text-left px-6 py-4 text-slate-400 text-sm">
                                Statut
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($movements ?? [] as $movement)

                            <tr class="border-t border-slate-700">

                                <td class="px-6 py-4">
                                    {{ $movement->date ?? $movement->created_at?->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 text-green-400">
                                    {{ $movement->entry_time ?? '--:--' }}
                                </td>

                                <td class="px-6 py-4 text-red-400">
                                    {{ $movement->exit_time ?? '--:--' }}
                                </td>

                                <td class="px-6 py-4">

                                    <span class="px-3 py-1 rounded-full text-sm bg-blue-500/10 text-blue-400">
                                        {{ $movement->status ?? 'Présent' }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="px-6 py-10 text-center text-slate-500">

                                    Aucun pointage enregistré pour le moment.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection