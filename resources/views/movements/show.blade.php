@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('movements.index') }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Détail du mouvement
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Informations détaillées sur cet accès.
                    </p>
                </div>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-5xl mx-auto px-6 py-8">

        {{-- MESSAGE SUCCÈS --}}
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-700">
                ✓ {{ session('success') }}
            </div>
        @endif


        {{-- CARTE PRINCIPALE --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- EN-TÊTE --}}
            <div class="px-6 py-5 border-b border-slate-200">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>

                        <h2 class="text-xl font-bold text-slate-900">
                            @if($movement->employee)
                                {{ $movement->employee->first_name }}
                                {{ $movement->employee->last_name }}
                            @else
                                Employé supprimé
                            @endif
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Mouvement #{{ $movement->id }}
                        </p>

                    </div>


                    {{-- TYPE --}}
                    @if($movement->type === 'entry')

                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            ↑ Entrée
                        </span>

                    @else

                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-700">
                            ↓ Sortie
                        </span>

                    @endif

                </div>

            </div>


            {{-- INFORMATIONS --}}
            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    {{-- EMPLOYÉ --}}
                    <div class="rounded-xl bg-slate-50 p-5">

                        <p class="text-xs font-semibold text-slate-500 uppercase">
                            Employé
                        </p>

                        @if($movement->employee)

                            <p class="mt-2 text-lg font-semibold text-slate-900">
                                {{ $movement->employee->first_name }}
                                {{ $movement->employee->last_name }}
                            </p>

                            @if($movement->employee->position)
                                <p class="text-sm text-slate-500 mt-1">
                                    {{ $movement->employee->position }}
                                </p>
                            @endif

                        @else

                            <p class="mt-2 text-slate-400">
                                Employé supprimé
                            </p>

                        @endif

                    </div>


                    {{-- DATE --}}
                    <div class="rounded-xl bg-slate-50 p-5">

                        <p class="text-xs font-semibold text-slate-500 uppercase">
                            Date et heure
                        </p>

                        <p class="mt-2 text-lg font-semibold text-slate-900">
                            {{ $movement->occurred_at?->format('d/m/Y') ?? '—' }}
                        </p>

                        <p class="text-sm text-slate-500 mt-1">
                            {{ $movement->occurred_at?->format('H:i:s') ?? '—' }}
                        </p>

                    </div>


                    {{-- MÉTHODE --}}
                    <div class="rounded-xl bg-slate-50 p-5">

                        <p class="text-xs font-semibold text-slate-500 uppercase">
                            Méthode
                        </p>

                        <p class="mt-2 text-lg font-semibold text-slate-900">
                            {{ ucfirst(str_replace('_', ' ', $movement->method ?? '—')) }}
                        </p>

                    </div>


                    {{-- VÉRIFICATION --}}
                    <div class="rounded-xl bg-slate-50 p-5">

                        <p class="text-xs font-semibold text-slate-500 uppercase">
                            Vérification
                        </p>

                        <div class="mt-2">

                            @if($movement->verification_status === 'verified')

                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                    Vérifié
                                </span>

                            @elseif($movement->verification_status === 'rejected')

                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                    Refusé
                                </span>

                            @elseif($movement->verification_status)

                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                                    {{ ucfirst($movement->verification_status) }}
                                </span>

                            @else

                                <span class="text-slate-400">
                                    Non renseigné
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- POINT D'ACCÈS --}}
                    <div class="rounded-xl bg-slate-50 p-5">

                        <p class="text-xs font-semibold text-slate-500 uppercase">
                            Point d'accès
                        </p>

                        @if($movement->accessPoint)

                            <p class="mt-2 text-lg font-semibold text-slate-900">
                                {{ $movement->accessPoint->name }}
                            </p>

                        @else

                            <p class="mt-2 text-slate-400">
                                Non renseigné
                            </p>

                        @endif

                    </div>


                    {{-- SCORE --}}
                    <div class="rounded-xl bg-slate-50 p-5">

                        <p class="text-xs font-semibold text-slate-500 uppercase">
                            Score d'anomalie
                        </p>

                        <p class="mt-2 text-lg font-semibold
                            @if(($movement->anomaly_score ?? 0) >= 50)
                                text-red-600
                            @else
                                text-green-600
                            @endif
                        ">
                            {{ $movement->anomaly_score ?? 0 }}
                        </p>

                    </div>

                </div>


                {{-- NOTES --}}
                @if($movement->notes)

                    <div class="mt-6 rounded-xl bg-indigo-50 border border-indigo-100 p-5">

                        <p class="text-xs font-semibold text-indigo-700 uppercase">
                            Notes
                        </p>

                        <p class="mt-2 text-sm text-indigo-900 whitespace-pre-line">
                            {{ $movement->notes }}
                        </p>

                    </div>

                @endif


                {{-- INFORMATIONS TECHNIQUES --}}
                <div class="mt-6 border-t border-slate-200 pt-6">

                    <h3 class="text-lg font-bold text-slate-900 mb-4">
                        Informations techniques
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                        <div>
                            <span class="text-slate-500">
                                Appareil :
                            </span>

                            <span class="font-medium text-slate-900">
                                {{ $movement->device_id ?? '—' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-500">
                                Adresse IP :
                            </span>

                            <span class="font-medium text-slate-900">
                                {{ $movement->ip_address ?? '—' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-500">
                                Latitude :
                            </span>

                            <span class="font-medium text-slate-900">
                                {{ $movement->latitude ?? '—' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-500">
                                Longitude :
                            </span>

                            <span class="font-medium text-slate-900">
                                {{ $movement->longitude ?? '—' }}
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ACTIONS --}}
        <div class="mt-6 flex justify-end">

            <a href="{{ route('movements.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">

                ← Retour aux mouvements

            </a>

        </div>

    </div>

</div>

@endsection