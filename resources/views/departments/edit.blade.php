@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('departments.index') }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Modifier le département
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Modifiez les informations de {{ $department->name }}.
                    </p>
                </div>

            </div>

        </div>
    </div>


    <div class="max-w-5xl mx-auto px-6 py-8">

        @if($errors->any())

            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-5">

                <h3 class="font-semibold text-red-800">
                    Vérifiez les informations saisies
                </h3>

                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif


        <form action="{{ route('departments.update', $department) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-200">

                    <div class="flex items-center gap-3">

                        <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                            🏢
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-slate-900">
                                Informations du département
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Modifiez les informations ci-dessous.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- NOM --}}
                        <div class="md:col-span-2">

                            <label for="name"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Nom du département
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $department->name) }}"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- CODE --}}
                        <div>

                            <label for="code"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Code du département
                            </label>

                            <input
                                type="text"
                                id="code"
                                name="code"
                                value="{{ old('code', $department->code) }}"
                                maxlength="50"
                                placeholder="Ex. IT"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition"
                            >

                        </div>


                        {{-- STATUT --}}
                        <div>

                            <label for="is_active"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Statut
                            </label>

                            <select
                                id="is_active"
                                name="is_active"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition">

                                <option value="1"
                                    {{ old('is_active', $department->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>
                                    Actif
                                </option>

                                <option value="0"
                                    {{ old('is_active', $department->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>
                                    Inactif
                                </option>

                            </select>

                        </div>


                        {{-- DESCRIPTION --}}
                        <div class="md:col-span-2">

                            <label for="description"
                                   class="block text-sm font-semibold text-slate-700 mb-2">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                maxlength="1000"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition resize-none"
                            >{{ old('description', $department->description) }}</textarea>

                        </div>

                    </div>

                </div>


                <div class="px-6 py-5 bg-slate-50 border-t border-slate-200">

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                        <a href="{{ route('departments.index') }}"
                           class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-100 transition">
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                            ✓ Enregistrer les modifications
                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection