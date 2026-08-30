@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('employees.show', $employee) }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Modifier l'employé
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Modifier les informations de {{ $employee->first_name }} {{ $employee->last_name }}.
                    </p>
                </div>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-5xl mx-auto px-6 py-8">

        {{-- ERREURS --}}
        @if($errors->any())

            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-5">

                <div class="flex gap-3">

                    <div class="text-red-500 text-xl">
                        ⚠
                    </div>

                    <div>
                        <h3 class="font-semibold text-red-800">
                            Vérifiez les informations saisies
                        </h3>

                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">

                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>
                    </div>

                </div>

            </div>

        @endif


        {{-- FORMULAIRE --}}
        <form action="{{ route('employees.update', $employee) }}" method="POST">

            @csrf
            @method('PUT')


            {{-- INFORMATIONS PERSONNELLES --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Informations personnelles
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Identité et coordonnées de l'employé.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- PRÉNOM --}}
                        <div>

                            <label for="first_name"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Prénom <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name', $employee->first_name) }}"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- NOM --}}
                        <div>

                            <label for="last_name"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name', $employee->last_name) }}"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- EMAIL --}}
                        <div>

                            <label for="email"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Adresse email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $employee->email) }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- TÉLÉPHONE --}}
                        <div>

                            <label for="phone"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Téléphone
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $employee->phone) }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMATIONS PROFESSIONNELLES --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Informations professionnelles
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Département, poste et statut de l'employé.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- DÉPARTEMENT --}}
                        <div>

                            <label for="department_id"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Département <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="department_id"
                                name="department_id"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner un département
                                </option>

                                @foreach($departments as $department)

                                    <option
                                        value="{{ $department->id }}"
                                        @selected(old('department_id', $employee->department_id) == $department->id)
                                    >
                                        {{ $department->name }}

                                        @if($department->code)
                                            — {{ $department->code }}
                                        @endif
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- POSTE --}}
                        <div>

                            <label for="position"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Poste
                            </label>

                            <input
                                type="text"
                                id="position"
                                name="position"
                                value="{{ old('position', $employee->position) }}"
                                placeholder="Ex. Développeur logiciel"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- STATUT --}}
                        <div>

                            <label for="status"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Statut <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="active"
                                    @selected(old('status', $employee->status) === 'active')>
                                    Actif
                                </option>

                                <option value="inactive"
                                    @selected(old('status', $employee->status) === 'inactive')>
                                    Inactif
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ACTIONS --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('employees.show', $employee) }}"
                   class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">
                    Annuler
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    ✓ Enregistrer les modifications
                </button>

            </div>

        </form>

    </div>

</div>

@endsection