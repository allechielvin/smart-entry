@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-white px-6 py-8">

    <div class="max-w-6xl mx-auto">

        {{-- EN-TÊTE --}}
        <div class="mb-8">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-blue-400 font-medium mb-2">
                        SMART ENTRY
                    </p>

                    <h1 class="text-3xl font-bold">
                        Mon pointage
                    </h1>

                    <p class="text-slate-400 mt-2">
                        Bonjour {{ $employee->first_name }},
                        gérez votre entrée et votre sortie.
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-slate-400 text-sm">
                        Aujourd'hui
                    </p>

                    <p class="text-xl font-semibold">
                        {{ now()->format('d/m/Y') }}
                    </p>

                    <p class="text-blue-400">
                        {{ $currentTime->format('H:i:s') }}
                    </p>
                </div>

            </div>

        </div>


        {{-- MESSAGES --}}
        @if(session('success'))

            <div class="mb-6 bg-green-500/10 border border-green-500/30
                        text-green-400 px-5 py-4 rounded-xl">

                ✓ {{ session('success') }}

            </div>

        @endif


        @if($errors->has('attendance'))

            <div class="mb-6 bg-red-500/10 border border-red-500/30
                        text-red-400 px-5 py-4 rounded-xl">

                ⚠ {{ $errors->first('attendance') }}

            </div>

        @endif


        {{-- CARTES DE POINTAGE --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">


            {{-- ENTRÉE --}}
            <div class="bg-[#172033] border border-slate-700
                        rounded-2xl p-6 shadow-xl">

                <div class="flex items-center justify-between mb-6">

                    <div>
                        <p class="text-slate-400 text-sm">
                            ARRIVÉE
                        </p>

                        <h2 class="text-2xl font-bold mt-1">
                            Pointer mon entrée
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-full
                                bg-green-500/10
                                flex items-center justify-center">

                        <span class="text-3xl">
                            🟢
                        </span>

                    </div>

                </div>


                @if($entry)

                    <div class="bg-green-500/10 border border-green-500/20
                                rounded-xl p-4 mb-5">

                        <p class="text-sm text-slate-400">
                            Entrée enregistrée à
                        </p>

                        <p class="text-3xl font-bold text-green-400 mt-1">
                            {{ $entry->occurred_at->format('H:i') }}
                        </p>

                    </div>

                    <button
                        type="button"
                        disabled
                        class="w-full bg-slate-700 text-slate-400
                               font-semibold py-4 rounded-xl cursor-not-allowed">

                        ✓ Entrée déjà enregistrée

                    </button>

                @else

                    <p class="text-slate-400 mb-6">
                        Enregistrez votre arrivée dans l'entreprise.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('my_attendance.entry') }}">

                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-green-600
                                   hover:bg-green-700
                                   transition text-white
                                   font-semibold py-4 rounded-xl">

                            🟢 Pointer mon entrée

                        </button>

                    </form>

                @endif

            </div>


            {{-- SORTIE --}}
            <div class="bg-[#172033] border border-slate-700
                        rounded-2xl p-6 shadow-xl">

                <div class="flex items-center justify-between mb-6">

                    <div>
                        <p class="text-slate-400 text-sm">
                            DÉPART
                        </p>

                        <h2 class="text-2xl font-bold mt-1">
                            Pointer ma sortie
                        </h2>
                    </div>

                    <div class="w-14 h-14 rounded-full
                                bg-red-500/10
                                flex items-center justify-center">

                        <span class="text-3xl">
                            🔴
                        </span>

                    </div>

                </div>


                @if($exit)

                    <div class="bg-red-500/10 border border-red-500/20
                                rounded-xl p-4 mb-5">

                        <p class="text-sm text-slate-400">
                            Sortie enregistrée à
                        </p>

                        <p class="text-3xl font-bold text-red-400 mt-1">
                            {{ $exit->occurred_at->format('H:i') }}
                        </p>

                    </div>

                    <button
                        type="button"
                        disabled
                        class="w-full bg-slate-700 text-slate-400
                               font-semibold py-4 rounded-xl cursor-not-allowed">

                        ✓ Sortie déjà enregistrée

                    </button>

                @elseif(!$entry)

                    <p class="text-slate-400 mb-6">
                        Vous devez d'abord enregistrer votre entrée.
                    </p>

                    <button
                        type="button"
                        disabled
                        class="w-full bg-slate-700 text-slate-400
                               font-semibold py-4 rounded-xl
                               cursor-not-allowed">

                        🔒 Entrée requise

                    </button>

                @else

                    <p class="text-slate-400 mb-6">
                        Enregistrez votre départ de l'entreprise.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('my_attendance.exit') }}">

                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-red-600
                                   hover:bg-red-700
                                   transition text-white
                                   font-semibold py-4 rounded-xl">

                            🔴 Pointer ma sortie

                        </button>

                    </form>

                @endif

            </div>

        </div>


        {{-- RÉSUMÉ DU JOUR --}}
        <div class="bg-[#172033] border border-slate-700
                    rounded-2xl p-6 shadow-xl mb-8">

            <h2 class="text-xl font-bold mb-6">
                📅 Situation du jour
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


                {{-- HEURE ENTRÉE --}}
                <div class="bg-[#0f172a] border border-slate-700
                            rounded-xl p-5">

                    <p class="text-slate-400 text-sm">
                        Heure d'entrée
                    </p>

                    <p class="text-3xl font-bold text-green-400 mt-2">

                        @if($entry)
                            {{ $entry->occurred_at->format('H:i') }}
                        @else
                            --:--
                        @endif

                    </p>

                </div>


                {{-- HEURE SORTIE --}}
                <div class="bg-[#0f172a] border border-slate-700
                            rounded-xl p-5">

                    <p class="text-slate-400 text-sm">
                        Heure de sortie
                    </p>

                    <p class="text-3xl font-bold text-red-400 mt-2">

                        @if($exit)
                            {{ $exit->occurred_at->format('H:i') }}
                        @else
                            --:--
                        @endif

                    </p>

                </div>


                {{-- STATUT --}}
                <div class="bg-[#0f172a] border border-slate-700
                            rounded-xl p-5">

                    <p class="text-slate-400 text-sm">
                        Statut
                    </p>

                    <p class="text-2xl font-bold mt-3">

                        @if($exit)

                            <span class="text-blue-400">
                                ✓ Journée terminée
                            </span>

                        @elseif($entry)

                            <span class="text-green-400">
                                ● Présent
                            </span>

                        @else

                            <span class="text-slate-400">
                                Non pointé
                            </span>

                        @endif

                    </p>

                </div>

            </div>

        </div>


        {{-- INFORMATIONS --}}
        <div class="bg-[#172033] border border-slate-700
                    rounded-2xl p-6 shadow-xl">

            <h2 class="text-xl font-bold mb-5">
                ℹ️ Informations de pointage
            </h2>

            <div class="space-y-4 text-slate-300">

                <div class="flex items-center gap-3">
                    <span class="text-green-400">●</span>
                    <span>
                        Entrée autorisée de <strong>06:00 à 08:30</strong>
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-red-400">●</span>
                    <span>
                        Sortie autorisée de <strong>18:00 à 22:00</strong>
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-blue-400">●</span>
                    <span>
                        Une seule entrée et une seule sortie sont
                        enregistrées par jour.
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection