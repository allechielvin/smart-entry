@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Gestion des employés
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Gérez les employés enregistrés dans Smart Entry.
                    </p>
                </div>

                <div class="flex gap-3">

                    <a href="{{ route('departments.index') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">
                        Départements
                    </a>

                    <a href="{{ route('employees.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                        <span>+</span>
                        Ajouter un employé
                    </a>

                </div>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- SUCCÈS --}}
        @if(session('success'))

            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-700">
                {{ session('success') }}
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


        {{-- STATISTIQUE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-slate-500">
                            Total employés
                        </p>

                        <p class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $employees->total() }}
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                        👥
                    </div>

                </div>

            </div>

        </div>


        {{-- TABLEAU --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Liste des employés
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $employees->total() }} employé(s) enregistré(s)
                </p>

            </div>


            @if($employees->count())

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50 border-b border-slate-200">

                            <tr>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Employé
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Département
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Poste
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Contact
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Statut
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach($employees as $employee)

                                <tr class="hover:bg-slate-50 transition">

                                    {{-- EMPLOYÉ --}}
                                    <td class="px-6 py-5">

                                        <div class="flex items-center gap-3">

                                            <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">

                                                {{ strtoupper(substr($employee->first_name ?? 'E', 0, 1)) }}

                                            </div>

                                            <div>

                                                <div class="font-semibold text-slate-900">
                                                    {{ $employee->first_name }}
                                                    {{ $employee->last_name }}
                                                </div>

                                                <div class="text-xs text-slate-400">
                                                    ID #{{ $employee->id }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- DÉPARTEMENT --}}
                                    <td class="px-6 py-5">

                                        @if($employee->department)

                                            <div class="font-medium text-slate-700">
                                                {{ $employee->department->name }}
                                            </div>

                                            @if($employee->department->code)

                                                <div class="text-xs text-slate-400">
                                                    {{ $employee->department->code }}
                                                </div>

                                            @endif

                                        @else

                                            <span class="text-slate-400">
                                                Aucun département
                                            </span>

                                        @endif

                                    </td>


                                    {{-- POSTE --}}
                                    <td class="px-6 py-5 text-sm text-slate-600">

                                        {{ $employee->position ?? '—' }}

                                    </td>


                                    {{-- CONTACT --}}
                                    <td class="px-6 py-5">

                                        @if($employee->email)

                                            <div class="text-sm text-slate-600">
                                                {{ $employee->email }}
                                            </div>

                                        @endif

                                        @if($employee->phone)

                                            <div class="text-xs text-slate-400 mt-1">
                                                {{ $employee->phone }}
                                            </div>

                                        @endif

                                        @if(!$employee->email && !$employee->phone)

                                            <span class="text-slate-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- STATUT --}}
                                    <td class="px-6 py-5">

                                        @if($employee->status === 'active')

                                            <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                                Actif
                                            </span>

                                        @else

                                            <span class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                                Inactif
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td class="px-6 py-5">

                                        <div class="flex items-center justify-end gap-2">

                                            <a href="{{ route('employees.show', $employee) }}"
                                               class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                                                Voir
                                            </a>

                                            <a href="{{ route('employees.edit', $employee) }}"
                                               class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-600 text-sm font-semibold hover:bg-indigo-100 transition">
                                                Modifier
                                            </a>

                                            <form action="{{ route('employees.destroy', $employee) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?');">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="px-3 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition">
                                                    Supprimer
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}
                @if($employees->hasPages())

                    <div class="px-6 py-5 border-t border-slate-200">

                        {{ $employees->links() }}

                    </div>

                @endif


            @else

                {{-- AUCUN EMPLOYÉ --}}
                <div class="px-6 py-16 text-center">

                    <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-100 flex items-center justify-center text-2xl">
                        👥
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-slate-900">
                        Aucun employé
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Aucun employé n'est encore enregistré.
                    </p>

                    <a href="{{ route('employees.create') }}"
                       class="inline-flex mt-5 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                        Ajouter le premier employé
                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection