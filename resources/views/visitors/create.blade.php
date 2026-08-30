@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('visitors.index') }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Ajouter un visiteur
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Enregistrer les informations du visiteur et sa visite.
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
        <form action="{{ route('visitors.store') }}" method="POST">

            @csrf


            {{-- INFORMATIONS PERSONNELLES --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Informations personnelles
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Identité et coordonnées du visiteur.
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
                                value="{{ old('first_name') }}"
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
                                value="{{ old('last_name') }}"
                                required
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
                                value="{{ old('phone') }}"
                                placeholder="Ex. 06 12 34 56 78"
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
                                value="{{ old('email') }}"
                                placeholder="visiteur@example.com"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- SOCIÉTÉ --}}
                        <div class="md:col-span-2">

                            <label for="company"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Société / Organisation

                            </label>

                            <input
                                type="text"
                                id="company"
                                name="company"
                                value="{{ old('company') }}"
                                placeholder="Ex. Société ABC"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- IDENTIFICATION --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Identification
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Informations du document d'identité du visiteur.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- TYPE DOCUMENT --}}
                        <div>

                            <label for="id_type"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Type de document

                            </label>

                            <select
                                id="id_type"
                                name="id_type"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner
                                </option>

                                <option value="id_card"
                                    @selected(old('id_type') === 'id_card')>
                                    Carte d'identité
                                </option>

                                <option value="passport"
                                    @selected(old('id_type') === 'passport')>
                                    Passeport
                                </option>

                                <option value="driving_license"
                                    @selected(old('id_type') === 'driving_license')>
                                    Permis de conduire
                                </option>

                                <option value="other"
                                    @selected(old('id_type') === 'other')>
                                    Autre
                                </option>

                            </select>

                        </div>


                        {{-- NUMÉRO --}}
                        <div>

                            <label for="id_number"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Numéro du document

                            </label>

                            <input
                                type="text"
                                id="id_number"
                                name="id_number"
                                value="{{ old('id_number') }}"
                                placeholder="Numéro du document"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMATIONS DE LA VISITE --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Informations de la visite
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Planifiez la visite et indiquez l'employé qui reçoit le visiteur.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- EMPLOYÉ HÔTE --}}
                        <div class="md:col-span-2">

                            <label for="host_employee_id"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Employé hôte

                            </label>

                            <select
                                id="host_employee_id"
                                name="host_employee_id"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Aucun employé sélectionné
                                </option>

                                @foreach($employees as $employee)

                                    <option
                                        value="{{ $employee->id }}"
                                        @selected(old('host_employee_id') == $employee->id)
                                    >

                                        {{ $employee->first_name }}
                                        {{ $employee->last_name }}

                                        @if($employee->position)
                                            — {{ $employee->position }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            @if($employees->isEmpty())

                                <p class="text-sm text-amber-600 mt-2">
                                    Aucun employé actif n'est disponible.
                                </p>

                            @endif

                        </div>


                        {{-- ARRIVÉE --}}
                        <div>

                            <label for="expected_arrival"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Arrivée prévue

                            </label>

                            <input
                                type="datetime-local"
                                id="expected_arrival"
                                name="expected_arrival"
                                value="{{ old('expected_arrival') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- DÉPART --}}
                        <div>

                            <label for="expected_departure"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Départ prévu

                            </label>

                            <input
                                type="datetime-local"
                                id="expected_departure"
                                name="expected_departure"
                                value="{{ old('expected_departure') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- MOTIF --}}
                        <div class="md:col-span-2">

                            <label for="purpose"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Motif de la visite

                            </label>

                            <textarea
                                id="purpose"
                                name="purpose"
                                rows="4"
                                placeholder="Ex. Rendez-vous professionnel, livraison, entretien..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >{{ old('purpose') }}</textarea>

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

                                <option value="expected"
                                    @selected(old('status', 'expected') === 'expected')>
                                    Attendu
                                </option>

                                <option value="inside"
                                    @selected(old('status') === 'inside')>
                                    Présent
                                </option>

                                <option value="completed"
                                    @selected(old('status') === 'completed')>
                                    Terminé
                                </option>

                                <option value="blocked"
                                    @selected(old('status') === 'blocked')>
                                    Bloqué
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMATION --}}
            <div class="mb-6 rounded-xl bg-indigo-50 border border-indigo-100 p-5">

                <div class="flex gap-3">

                    <div class="text-indigo-600 text-xl">
                        ℹ
                    </div>

                    <div>

                        <h3 class="font-semibold text-indigo-900">
                            Code visiteur
                        </h3>

                        <p class="text-sm text-indigo-700 mt-1">
                            Un code visiteur unique sera généré automatiquement
                            lors de l'enregistrement.
                        </p>

                    </div>

                </div>

            </div>


            {{-- ACTIONS --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('visitors.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">

                    Annuler

                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">

                    <span>✓</span>

                    Enregistrer le visiteur

                </button>

            </div>

        </form>

    </div>

</div>

@endsection