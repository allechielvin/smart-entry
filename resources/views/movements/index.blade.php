@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Mouvements d'accès
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Consultez les entrées et sorties enregistrées dans Smart Entry.
                    </p>
                </div>

                <a href="{{ route('movements.create') }}"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    + Enregistrer un mouvement
                </a>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- SUCCÈS --}}
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-700">
                ✓ {{ session('success') }}
            </div>
        @endif


        {{-- ERREURS --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-red-700">

                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- TABLEAU --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Historique des mouvements
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $movements->total() }} mouvement(s) enregistré(s)
                </p>

            </div>


            @if($movements->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Employé
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Type
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Méthode
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Date / Heure
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Vérification
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-200">

                            @foreach($movements as $movement)

                                <tr class="hover:bg-slate-50 transition">

                                    {{-- EMPLOYÉ --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($movement->employee)

                                            <div class="flex items-center gap-3">

                                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                                    {{ strtoupper(substr($movement->employee->first_name, 0, 1)) }}
                                                </div>

                                                <div>

                                                    <div class="font-semibold text-slate-900">
                                                        {{ $movement->employee->first_name }}
                                                        {{ $movement->employee->last_name }}
                                                    </div>

                                                    <div class="text-xs text-slate-500">
                                                        {{ $movement->employee->position ?? 'Employé' }}
                                                    </div>

                                                </div>

                                            </div>

                                        @else

                                            <span class="text-slate-400">
                                                Employé supprimé
                                            </span>

                                        @endif

                                    </td>


                                    {{-- TYPE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($movement->type === 'entry')

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                ↑ Entrée
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                                ↓ Sortie
                                            </span>

                                        @endif

                                    </td>


                                    {{-- MÉTHODE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <span class="text-sm text-slate-700">
                                            {{ ucfirst($movement->method) }}
                                        </span>

                                    </td>


                                    {{-- DATE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm font-medium text-slate-900">
                                            {{ $movement->occurred_at?->format('d/m/Y') }}
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            {{ $movement->occurred_at?->format('H:i') }}
                                        </div>

                                    </td>


                                    {{-- VÉRIFICATION --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($movement->verification_status)

                                            @if($movement->verification_status === 'verified')

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                    Vérifié
                                                </span>

                                            @elseif($movement->verification_status === 'rejected')

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                    Refusé
                                                </span>

                                            @else

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                    {{ ucfirst($movement->verification_status) }}
                                                </span>

                                            @endif

                                        @else

                                            <span class="text-xs text-slate-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTION --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">

                                        <a href="{{ route('movements.show', $movement) }}"
                                           class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">
                                            Voir
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $movements->links() }}
                </div>

            @else

                <div class="px-6 py-16 text-center">

                    <div class="text-5xl mb-4">
                        🚪
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900">
                        Aucun mouvement
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Aucun mouvement d'accès n'est encore enregistré.
                    </p>

                    <a href="{{ route('movements.create') }}"
                       class="inline-flex mt-5 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                        Enregistrer le premier mouvement
                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection