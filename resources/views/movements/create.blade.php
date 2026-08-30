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
                        Enregistrer un mouvement
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Enregistrer une entrée ou une sortie dans Smart Entry.
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
        <form action="{{ route('movements.store') }}" method="POST">

            @csrf


            {{-- INFORMATIONS DU MOUVEMENT --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Informations du mouvement
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Indiquez l'employé, le type et les informations de l'accès.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                        {{-- EMPLOYÉ --}}
                        <div class="md:col-span-2">

                            <label for="employee_id"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Employé <span class="text-red-500">*</span>

                            </label>

                            <select
                                id="employee_id"
                                name="employee_id"
                                required
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
                                    Aucun employé actif n'est disponible.
                                    Créez d'abord un employé.
                                </p>

                            @endif

                        </div>


                        {{-- TYPE --}}
                        <div>

                            <label for="type"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Type de mouvement <span class="text-red-500">*</span>

                            </label>

                            <select
                                id="type"
                                name="type"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner
                                </option>

                                <option value="entry"
                                    @selected(old('type') === 'entry')>
                                    ↑ Entrée
                                </option>

                                <option value="exit"
                                    @selected(old('type') === 'exit')>
                                    ↓ Sortie
                                </option>

                            </select>

                        </div>


                        {{-- MÉTHODE --}}
                        <div>

                            <label for="method"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Méthode <span class="text-red-500">*</span>

                            </label>

                            <select
                                id="method"
                                name="method"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                                <option value="">
                                    Sélectionner
                                </option>

                                <option value="manual"
                                    @selected(old('method') === 'manual')>
                                    Saisie manuelle
                                </option>

                                <option value="qr_code"
                                    @selected(old('method') === 'qr_code')>
                                    QR Code
                                </option>

                                <option value="badge"
                                    @selected(old('method') === 'badge')>
                                    Badge
                                </option>

                                <option value="biometric"
                                    @selected(old('method') === 'biometric')>
                                    Biométrie
                                </option>

                            </select>

                        </div>


                        {{-- DATE ET HEURE --}}
                        <div class="md:col-span-2">

                            <label for="occurred_at"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Date et heure <span class="text-red-500">*</span>

                            </label>

                            <input
                                type="datetime-local"
                                id="occurred_at"
                                name="occurred_at"
                                value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- NOTES --}}
                        <div class="md:col-span-2">

                            <label for="notes"
                                   class="block text-sm font-semibold text-slate-700 mb-2">

                                Notes

                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="4"
                                placeholder="Ajouter une remarque concernant ce mouvement..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            >{{ old('notes') }}</textarea>

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
                            Enregistrement manuel
                        </h3>

                        <p class="text-sm text-indigo-700 mt-1">
                            Ce formulaire permet actuellement d'enregistrer
                            manuellement les entrées et sorties.
                            Le système QR Code pourra être connecté ensuite.
                        </p>

                    </div>

                </div>

            </div>


            {{-- ACTIONS --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('movements.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">

                    Annuler

                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">

                    <span>✓</span>

                    Enregistrer le mouvement

                </button>

            </div>

        </form>

    </div>

</div>

@endsection