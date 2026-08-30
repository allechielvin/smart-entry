@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('qr_codes.index') }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Créer un QR Code
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Générer un QR Code sécurisé pour un employé ou un visiteur.
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
        <form action="{{ route('qr_codes.store') }}" method="POST">

            @csrf

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- TITRE --}}
                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Informations du QR Code
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Sélectionnez la personne à laquelle le QR Code sera associé.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                        {{-- TYPE DE PROPRIÉTAIRE --}}
                        <div class="md:col-span-2">

                            <label for="owner_type"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Type de QR Code
                                <span class="text-red-500">*</span>

                            </label>

                            <select
                                id="owner_type"
                                name="owner_type"
                                required
                                onchange="changeOwnerType()"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner
                                </option>

                                <option value="employee"
                                    @selected(old('owner_type') === 'employee')>
                                    👤 Employé
                                </option>

                                <option value="visitor"
                                    @selected(old('owner_type') === 'visitor')>
                                    🧑 Visiteur
                                </option>

                            </select>

                        </div>


                        {{-- EMPLOYÉ --}}
                        <div id="employee-container"
                             class="md:col-span-2 hidden">

                            <label for="employee_id"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Employé

                                <span class="text-red-500">*</span>

                            </label>

                            <select
                                id="employee_id"
                                name="employee_id"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner un employé
                                </option>

                                @foreach($employees as $employee)

                                    <option
                                        value="{{ $employee->id }}"
                                        @selected(old('employee_id') == $employee->id)
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
                                    Aucun employé actif disponible.
                                </p>

                            @endif

                        </div>


                        {{-- VISITEUR --}}
                        <div id="visitor-container"
                             class="md:col-span-2 hidden">

                            <label for="visitor_id"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Visiteur

                                <span class="text-red-500">*</span>

                            </label>

                            <select
                                id="visitor_id"
                                name="visitor_id"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner un visiteur
                                </option>

                                @foreach($visitors as $visitor)

                                    <option
                                        value="{{ $visitor->id }}"
                                        @selected(old('visitor_id') == $visitor->id)
                                    >

                                        {{ $visitor->first_name }}
                                        {{ $visitor->last_name }}

                                        @if($visitor->company)
                                            — {{ $visitor->company }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            @if($visitors->isEmpty())

                                <p class="text-sm text-amber-600 mt-2">
                                    Aucun visiteur disponible.
                                </p>

                            @endif

                        </div>


                        {{-- EXPIRATION --}}
                        <div class="md:col-span-2">

                            <label for="expires_at"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Date d'expiration

                            </label>

                            <input
                                type="datetime-local"
                                id="expires_at"
                                name="expires_at"
                                value="{{ old('expires_at') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                            <p class="text-xs text-slate-500 mt-2">
                                Laissez vide si le QR Code ne doit pas avoir de date d'expiration.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMATION --}}
            <div class="mt-6 rounded-xl bg-indigo-50 border border-indigo-100 p-5">

                <div class="flex gap-3">

                    <div class="text-indigo-600 text-xl">
                        🔐
                    </div>

                    <div>

                        <h3 class="font-semibold text-indigo-900">
                            QR Code sécurisé
                        </h3>

                        <p class="text-sm text-indigo-700 mt-1">
                            Un token unique et sécurisé sera automatiquement généré
                            lors de la création du QR Code.
                        </p>

                    </div>

                </div>

            </div>


            {{-- ACTIONS --}}
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('qr_codes.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">

                    Annuler

                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">

                    <span>✓</span>

                    Générer le QR Code

                </button>

            </div>

        </form>

    </div>

</div>


{{-- JAVASCRIPT --}}
<script>

    function changeOwnerType() {

        const ownerType = document.getElementById('owner_type').value;

        const employeeContainer =
            document.getElementById('employee-container');

        const visitorContainer =
            document.getElementById('visitor-container');

        const employeeSelect =
            document.getElementById('employee_id');

        const visitorSelect =
            document.getElementById('visitor_id');


        // Cacher les deux champs
        employeeContainer.classList.add('hidden');
        visitorContainer.classList.add('hidden');


        // Retirer les valeurs
        if (ownerType !== 'employee') {
            employeeSelect.value = '';
        }

        if (ownerType !== 'visitor') {
            visitorSelect.value = '';
        }


        // Afficher le bon champ
        if (ownerType === 'employee') {

            employeeContainer.classList.remove('hidden');

        }

        if (ownerType === 'visitor') {

            visitorContainer.classList.remove('hidden');

        }

    }


    // Restaurer le choix après une erreur de validation
    document.addEventListener('DOMContentLoaded', function () {

        changeOwnerType();

    });

</script>

@endsection