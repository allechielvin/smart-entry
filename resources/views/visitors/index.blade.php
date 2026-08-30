@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Visiteurs
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Gérez les visiteurs et leurs accès à Smart Entry.
                    </p>
                </div>

                <a href="{{ route('visitors.create') }}"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    + Ajouter un visiteur
                </a>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- MESSAGE DE SUCCÈS --}}
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
                    Liste des visiteurs
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $visitors->total() }} visiteur(s) enregistré(s)
                </p>

            </div>


            @if($visitors->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Visiteur
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Société
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Employé hôte
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Arrivée prévue
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Statut
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-200">

                            @foreach($visitors as $visitor)

                                <tr class="hover:bg-slate-50 transition">

                                    {{-- VISITEUR --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="flex items-center gap-3">

                                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold">

                                                {{ strtoupper(substr($visitor->first_name, 0, 1)) }}

                                            </div>

                                            <div>

                                                <div class="font-semibold text-slate-900">
                                                    {{ $visitor->first_name }}
                                                    {{ $visitor->last_name }}
                                                </div>

                                                @if($visitor->visitor_code)

                                                    <div class="text-xs text-slate-500">
                                                        {{ $visitor->visitor_code }}
                                                    </div>

                                                @elseif($visitor->email)

                                                    <div class="text-xs text-slate-500">
                                                        {{ $visitor->email }}
                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- SOCIÉTÉ --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($visitor->company)

                                            <span class="text-sm text-slate-700">
                                                {{ $visitor->company }}
                                            </span>

                                        @else

                                            <span class="text-sm text-slate-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- EMPLOYÉ HÔTE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($visitor->hostEmployee)

                                            <div class="text-sm font-medium text-slate-900">

                                                {{ $visitor->hostEmployee->first_name }}
                                                {{ $visitor->hostEmployee->last_name }}

                                            </div>

                                            @if($visitor->hostEmployee->position)

                                                <div class="text-xs text-slate-500">
                                                    {{ $visitor->hostEmployee->position }}
                                                </div>

                                            @endif

                                        @else

                                            <span class="text-sm text-slate-400">
                                                Aucun
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ARRIVÉE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($visitor->expected_arrival)

                                            <div class="text-sm font-medium text-slate-900">

                                                {{ $visitor->expected_arrival->format('d/m/Y') }}

                                            </div>

                                            <div class="text-xs text-slate-500">

                                                {{ $visitor->expected_arrival->format('H:i') }}

                                            </div>

                                        @else

                                            <span class="text-sm text-slate-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- STATUT --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @switch($visitor->status)

                                            @case('expected')

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                    Attendu
                                                </span>

                                                @break

                                            @case('inside')

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                    Présent
                                                </span>

                                                @break

                                            @case('completed')

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                    Terminé
                                                </span>

                                                @break

                                            @case('blocked')

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                    Bloqué
                                                </span>

                                                @break

                                            @default

                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                    {{ ucfirst($visitor->status) }}
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- ACTION --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">

                                        <a href="{{ route('visitors.show', $visitor) }}"
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

                    {{ $visitors->links() }}

                </div>

            @else

                {{-- AUCUN VISITEUR --}}
                <div class="px-6 py-16 text-center">

                    <div class="text-5xl mb-4">
                        👤
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900">
                        Aucun visiteur
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Aucun visiteur n'est encore enregistré.
                    </p>

                    <a href="{{ route('visitors.create') }}"
                       class="inline-flex mt-5 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">

                        Ajouter le premier visiteur

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection