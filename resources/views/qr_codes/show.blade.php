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
                        QR Code
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Détails et gestion du QR Code.
                    </p>
                </div>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-5xl mx-auto px-6 py-8">

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

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- ========================================================= --}}
            {{-- QR CODE --}}
            {{-- ========================================================= --}}

            <div class="lg:col-span-1">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    {{-- TITRE --}}
                    <div class="px-6 py-5 border-b border-slate-200 text-center">

                        <h2 class="text-lg font-bold text-slate-900">
                            Smart Entry
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            QR Code d'accès
                        </p>

                    </div>


                    {{-- QR --}}
                    <div class="p-8">

                        <div class="flex items-center justify-center bg-white rounded-2xl border border-slate-200 p-5">

                            {!! QrCode::size(280)->margin(2)->generate($qrCode->token) !!}

                        </div>


                        {{-- STATUT QR --}}
                        <div class="mt-6 text-center">

                            @if($qrCode->isValid())

                                <span class="inline-flex items-center px-4 py-2 rounded-full bg-green-100 text-green-700 text-sm font-semibold">

                                    <span class="mr-2">
                                        ●
                                    </span>

                                    QR Code actif

                                </span>

                            @else

                                <span class="inline-flex items-center px-4 py-2 rounded-full bg-red-100 text-red-700 text-sm font-semibold">

                                    <span class="mr-2">
                                        ●
                                    </span>

                                    QR Code invalide

                                </span>

                            @endif

                        </div>


                        {{-- TOKEN --}}
                        <div class="mt-6">

                            <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                Token
                            </p>

                            <div class="bg-slate-50 rounded-xl border border-slate-200 p-3">

                                <code class="text-xs text-slate-600 break-all">
                                    {{ $qrCode->token }}
                                </code>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- INFORMATIONS --}}
            {{-- ========================================================= --}}

            <div class="lg:col-span-2">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">


                    {{-- HEADER --}}
                    <div class="px-6 py-5 border-b border-slate-200">

                        <h2 class="text-lg font-bold text-slate-900">
                            Informations
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Informations associées à ce QR Code.
                        </p>

                    </div>


                    {{-- INFORMATIONS --}}
                    <div class="p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                            {{-- ================================================= --}}
                            {{-- PROPRIÉTAIRE --}}
                            {{-- ================================================= --}}

                            <div class="md:col-span-2">

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Propriétaire
                                </p>


                                {{-- EMPLOYÉ --}}
                                @if($qrCode->employee)

                                    <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100">

                                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg">

                                            {{ strtoupper(substr($qrCode->employee->first_name, 0, 1)) }}

                                        </div>

                                        <div>

                                            <div class="font-bold text-slate-900">

                                                {{ $qrCode->employee->first_name }}
                                                {{ $qrCode->employee->last_name }}

                                            </div>

                                            <div class="text-sm text-slate-500">

                                                {{ $qrCode->employee->position ?? 'Employé' }}

                                            </div>

                                        </div>

                                    </div>


                                {{-- VISITEUR --}}
                                @elseif($qrCode->visitor)

                                    <div class="flex items-center gap-4 p-4 bg-purple-50 rounded-xl border border-purple-100">

                                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-lg">

                                            {{ strtoupper(substr($qrCode->visitor->first_name, 0, 1)) }}

                                        </div>

                                        <div>

                                            <div class="font-bold text-slate-900">

                                                {{ $qrCode->visitor->first_name }}
                                                {{ $qrCode->visitor->last_name }}

                                            </div>

                                            <div class="text-sm text-slate-500">

                                                {{ $qrCode->visitor->company ?? 'Visiteur' }}

                                            </div>

                                        </div>

                                    </div>


                                {{-- AUCUN PROPRIÉTAIRE --}}
                                @else

                                    <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-red-700">

                                        Le propriétaire associé à ce QR Code n'existe plus.

                                    </div>

                                @endif

                            </div>


                            {{-- ================================================= --}}
                            {{-- TYPE --}}
                            {{-- ================================================= --}}

                            <div>

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Type
                                </p>


                                @if($qrCode->employee)

                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-700">

                                        Employé

                                    </span>


                                @elseif($qrCode->visitor)

                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-700">

                                        Visiteur

                                    </span>


                                @else

                                    <span class="text-slate-400">
                                        Inconnu
                                    </span>

                                @endif

                            </div>


                            {{-- ================================================= --}}
                            {{-- STATUT --}}
                            {{-- ================================================= --}}

                            <div>

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Statut
                                </p>


                                @if($qrCode->isValid())

                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">

                                        ● Actif

                                    </span>

                                @else

                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">

                                        ● Inactif

                                    </span>

                                @endif

                            </div>


                            {{-- ================================================= --}}
                            {{-- VERSION --}}
                            {{-- ================================================= --}}

                            <div>

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Version
                                </p>

                                <p class="text-sm font-medium text-slate-900">
                                    v{{ $qrCode->version }}
                                </p>

                            </div>


                            {{-- ================================================= --}}
                            {{-- EXPIRATION --}}
                            {{-- ================================================= --}}

                            <div>

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Expiration
                                </p>


                                @if($qrCode->expires_at)

                                    <p class="text-sm font-medium text-slate-900">

                                        {{ $qrCode->expires_at->format('d/m/Y à H:i') }}

                                    </p>


                                    @if($qrCode->expires_at->isPast())

                                        <p class="text-xs text-red-600 mt-1">

                                            QR Code expiré

                                        </p>

                                    @endif


                                @else

                                    <p class="text-sm text-slate-500">

                                        Aucune expiration

                                    </p>

                                @endif

                            </div>


                            {{-- ================================================= --}}
                            {{-- DERNIÈRE UTILISATION --}}
                            {{-- ================================================= --}}

                            <div>

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Dernière utilisation
                                </p>


                                @if($qrCode->last_used_at)

                                    <p class="text-sm font-medium text-slate-900">

                                        {{ $qrCode->last_used_at->format('d/m/Y à H:i') }}

                                    </p>

                                @else

                                    <p class="text-sm text-slate-500">

                                        Jamais utilisé

                                    </p>

                                @endif

                            </div>


                            {{-- ================================================= --}}
                            {{-- CRÉATION --}}
                            {{-- ================================================= --}}

                            <div>

                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">
                                    Créé le
                                </p>

                                <p class="text-sm font-medium text-slate-900">

                                    {{ $qrCode->created_at->format('d/m/Y à H:i') }}

                                </p>

                            </div>


                        </div>

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- ACTIONS --}}
                {{-- ========================================================= --}}

                <div class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                    <h2 class="text-lg font-bold text-slate-900">
                        Actions
                    </h2>


                    <p class="text-sm text-slate-500 mt-1">
                        Gérez l'état de ce QR Code.
                    </p>


                    <div class="mt-5 flex flex-col sm:flex-row gap-3">


                        {{-- ================================================ --}}
                        {{-- ACTIVER / DÉSACTIVER --}}
                        {{-- ================================================ --}}

                        <form
                            action="{{ route('qr_codes.toggle', $qrCode) }}"
                            method="POST"
                        >

                            @csrf

                            @method('PATCH')


                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-xl font-semibold transition
                                {{ $qrCode->is_active
                                    ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                    : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                            >

                                @if($qrCode->is_active)

                                    Désactiver

                                @else

                                    Activer

                                @endif

                            </button>

                        </form>


                        {{-- ================================================ --}}
                        {{-- SUPPRIMER --}}
                        {{-- ================================================ --}}

                        <form
                            action="{{ route('qr_codes.destroy', $qrCode) }}"
                            method="POST"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce QR Code ?');"
                        >

                            @csrf

                            @method('DELETE')


                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition"
                            >

                                Supprimer

                            </button>

                        </form>


                        {{-- ================================================ --}}
                        {{-- IMPRIMER --}}
                        {{-- ================================================ --}}

                        <button
                            type="button"
                            onclick="window.print()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition"
                        >

                            🖨 Imprimer

                        </button>


                        {{-- ================================================ --}}
                        {{-- RETOUR --}}
                        {{-- ================================================ --}}

                        <a
                            href="{{ route('qr_codes.index') }}"
                            class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition"
                        >

                            Retour

                        </a>


                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- STYLE IMPRESSION --}}
{{-- ========================================================= --}}

<style>

@media print {

    body {
        background: white !important;
    }

    nav,
    header,
    button,
    form,
    a {
        display: none !important;
    }

    .min-h-screen {
        min-height: auto !important;
    }

}

</style>

@endsection