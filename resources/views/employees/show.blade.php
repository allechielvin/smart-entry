@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    <div class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('employees.index') }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Informations de l'employé
                    </p>
                </div>

            </div>

        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="p-6">

                <div class="flex items-center gap-4 mb-8">

                    <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </h2>

                        <p class="text-sm text-slate-500">
                            {{ $employee->position ?? 'Poste non renseigné' }}
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-sm text-slate-500">Email</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ $employee->email ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Téléphone</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ $employee->phone ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Département</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ $employee->department?->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Poste</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ $employee->position ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Statut</p>

                        @if($employee->status === 'active')
                            <span class="inline-flex mt-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex mt-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-semibold">
                                Inactif
                            </span>
                        @endif

                    </div>

                </div>

            </div>

            <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">

                <a href="{{ route('employees.index') }}"
                   class="px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100">
                    Retour
                </a>

                <a href="{{ route('employees.edit', $employee) }}"
                   class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    Modifier
                </a>

            </div>

        </div>

    </div>

</div>

@endsection