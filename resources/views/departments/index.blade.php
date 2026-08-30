@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                            🏢
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">
                                Départements
                            </h1>

                            <p class="text-sm text-slate-500 mt-1">
                                Gérez les départements de votre organisation.
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('departments.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    <span class="text-lg">+</span>
                    Ajouter un département
                </a>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- MESSAGES --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">
                <span class="text-lg">✓</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                <span class="text-lg">!</span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif


        {{-- STATISTIQUES --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Total départements
                </p>

                <p class="text-3xl font-bold text-slate-900 mt-2">
                    {{ $departments->total() }}
                </p>
            </div>


            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Départements actifs
                </p>

                <p class="text-3xl font-bold text-emerald-600 mt-2">
                    {{ \App\Models\Department::where('is_active', true)->count() }}
                </p>

            </div>


            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Employés affectés
                </p>

                <p class="text-3xl font-bold text-indigo-600 mt-2">
                    {{ \App\Models\Employee::count() }}
                </p>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Liste des départements
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Retrouvez ici tous les départements enregistrés.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50 border-b border-slate-200">

                        <tr>

                            <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Département
                            </th>

                            <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Code
                            </th>

                            <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Employés
                            </th>

                            <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Statut
                            </th>

                            <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($departments as $department)

                            <tr class="hover:bg-slate-50 transition">

                                {{-- NOM --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                            {{ strtoupper(substr($department->name, 0, 1)) }}
                                        </div>

                                        <div>

                                            <div class="font-semibold text-slate-900">
                                                {{ $department->name }}
                                            </div>

                                            @if($department->description)
                                                <div class="text-sm text-slate-500 mt-1 max-w-md truncate">
                                                    {{ $department->description }}
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- CODE --}}
                                <td class="px-6 py-5">

                                    @if($department->code)

                                        <span class="inline-flex px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold">
                                            {{ $department->code }}
                                        </span>

                                    @else

                                        <span class="text-slate-400">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- EMPLOYÉS --}}
                                <td class="px-6 py-5">

                                    <span class="inline-flex items-center gap-2 text-slate-700 font-medium">

                                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                            👤
                                        </span>

                                        {{ $department->employees_count }}

                                    </span>

                                </td>


                                {{-- STATUT --}}
                                <td class="px-6 py-5">

                                    @if($department->is_active)

                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-sm font-semibold">

                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                            Actif

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 text-sm font-semibold">

                                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>

                                            Inactif

                                        </span>

                                    @endif

                                </td>


                                {{-- ACTIONS --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center justify-end gap-2">

                                        <a href="{{ route('departments.edit', $department) }}"
                                           class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-sm font-semibold transition">
                                            Modifier
                                        </a>


                                        <form action="{{ route('departments.destroy', $department) }}"
                                              method="POST"
                                              onsubmit="return confirm('Voulez-vous vraiment supprimer ce département ?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold transition">
                                                Supprimer
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-6 py-16 text-center">

                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-3xl mb-4">
                                        🏢
                                    </div>

                                    <h3 class="text-lg font-bold text-slate-900">
                                        Aucun département
                                    </h3>

                                    <p class="text-sm text-slate-500 mt-1 mb-5">
                                        Commencez par créer votre premier département.
                                    </p>

                                    <a href="{{ route('departments.create') }}"
                                       class="inline-flex items-center px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                        + Créer un département
                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if($departments->hasPages())

                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $departments->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

@endsection