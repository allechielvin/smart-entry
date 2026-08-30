@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h1 class="text-3xl font-bold text-slate-900">
                        QR Codes
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Gérez les QR Codes des employés et des visiteurs.
                    </p>
                </div>

                <a href="{{ route('qr_codes.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">

                    <span class="text-lg">+</span>

                    Créer un QR Code

                </a>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- MESSAGE DE SUCCÈS --}}
        @if(session('success'))

            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-700">

                <div class="flex items-center gap-2">
                    <span class="font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>

            </div>

        @endif


        {{-- ERREURS --}}
        @if($errors->any())

            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-5">

                <h3 class="font-semibold text-red-800">
                    Une erreur est survenue
                </h3>

                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{-- LISTE DES QR CODES --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- HEADER DE LA CARTE --}}
            <div class="px-6 py-6 border-b border-slate-200">

                <h2 class="text-xl font-bold text-slate-900">
                    QR Codes enregistrés
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $qrCodes->total() }}
                    QR Code{{ $qrCodes->total() > 1 ? 's' : '' }}
                </p>

            </div>


            {{-- SI AUCUN QR CODE --}}
            @if($qrCodes->isEmpty())

                <div class="py-20 px-6 text-center">

                    <div class="mx-auto w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center">

                        <svg
                            class="w-10 h-10 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                            <path d="M14 14h3v3h-3z"></path>
                            <path d="M18 18h3v3h-3z"></path>
                            <path d="M14 21h3"></path>
                            <path d="M21 14v3"></path>
                        </svg>

                    </div>


                    <h3 class="mt-6 text-xl font-bold text-slate-900">
                        Aucun QR Code
                    </h3>

                    <p class="mt-2 text-slate-500">
                        Aucun QR Code n'a encore été créé.
                    </p>


                    <a href="{{ route('qr_codes.create') }}"
                       class="mt-6 inline-flex items-center justify-center px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">

                        Créer le premier QR Code

                    </a>

                </div>

            @else


                {{-- GRILLE DES QR CODES --}}
                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        @foreach($qrCodes as $qrCode)

                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition">


                                {{-- QR --}}
                                <div class="p-6 text-center bg-slate-50 border-b border-slate-200">

                                    <div class="inline-flex items-center justify-center bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">

                                        {!! QrCode::size(190)
                                            ->margin(1)
                                            ->generate($qrCode->token) !!}

                                    </div>

                                </div>


                                {{-- INFORMATIONS --}}
                                <div class="p-5">

                                    {{-- PROPRIÉTAIRE --}}
                                    @if($qrCode->employee)

                                        <div class="flex items-center gap-3">

                                            <div class="w-11 h-11 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">

                                                {{ strtoupper(substr($qrCode->employee->first_name, 0, 1)) }}

                                            </div>

                                            <div class="min-w-0">

                                                <p class="font-bold text-slate-900 truncate">

                                                    {{ $qrCode->employee->first_name }}
                                                    {{ $qrCode->employee->last_name }}

                                                </p>

                                                <p class="text-sm text-slate-500 truncate">

                                                    {{ $qrCode->employee->position ?? 'Employé' }}

                                                </p>

                                            </div>

                                        </div>


                                        <div class="mt-4">

                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">

                                                Employé

                                            </span>

                                        </div>


                                    @elseif($qrCode->visitor)

                                        <div class="flex items-center gap-3">

                                            <div class="w-11 h-11 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold">

                                                {{ strtoupper(substr($qrCode->visitor->first_name, 0, 1)) }}

                                            </div>

                                            <div class="min-w-0">

                                                <p class="font-bold text-slate-900 truncate">

                                                    {{ $qrCode->visitor->first_name }}
                                                    {{ $qrCode->visitor->last_name }}

                                                </p>

                                                <p class="text-sm text-slate-500 truncate">

                                                    {{ $qrCode->visitor->company ?? 'Visiteur' }}

                                                </p>

                                            </div>

                                        </div>


                                        <div class="mt-4">

                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">

                                                Visiteur

                                            </span>

                                        </div>


                                    @else

                                        <div class="p-3 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">

                                            Propriétaire supprimé

                                        </div>

                                    @endif


                                    {{-- STATUT --}}
                                    <div class="mt-5 flex items-center justify-between">

                                        <div>

                                            <p class="text-xs font-semibold text-slate-400 uppercase">
                                                Statut
                                            </p>

                                            @if($qrCode->isValid())

                                                <span class="inline-flex items-center gap-1 mt-1 text-sm font-semibold text-green-600">

                                                    <span>●</span>
                                                    Actif

                                                </span>

                                            @else

                                                <span class="inline-flex items-center gap-1 mt-1 text-sm font-semibold text-red-600">

                                                    <span>●</span>
                                                    Inactif

                                                </span>

                                            @endif

                                        </div>


                                        <div class="text-right">

                                            <p class="text-xs font-semibold text-slate-400 uppercase">
                                                Version
                                            </p>

                                            <p class="text-sm font-semibold text-slate-700 mt-1">
                                                v{{ $qrCode->version }}
                                            </p>

                                        </div>

                                    </div>


                                    {{-- EXPIRATION --}}
                                    @if($qrCode->expires_at)

                                        <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-200">

                                            <p class="text-xs text-slate-400 font-semibold uppercase">
                                                Expiration
                                            </p>

                                            <p class="text-sm text-slate-700 mt-1">

                                                {{ $qrCode->expires_at->format('d/m/Y à H:i') }}

                                            </p>

                                        </div>

                                    @endif


                                    {{-- ACTIONS --}}
                                    <div class="mt-5 flex gap-2">

                                        <a href="{{ route('qr_codes.show', $qrCode) }}"
                                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">

                                            Voir

                                        </a>


                                        <form action="{{ route('qr_codes.toggle', $qrCode) }}"
                                              method="POST"
                                              class="flex-1">

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold transition
                                                {{ $qrCode->is_active
                                                    ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                                    : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                            >

                                                {{ $qrCode->is_active ? 'Désactiver' : 'Activer' }}

                                            </button>

                                        </form>

                                    </div>


                                    {{-- SUPPRESSION --}}
                                    <form action="{{ route('qr_codes.destroy', $qrCode) }}"
                                          method="POST"
                                          class="mt-2"
                                          onsubmit="return confirm('Voulez-vous vraiment supprimer ce QR Code ?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition"
                                        >

                                            Supprimer

                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- PAGINATION --}}
                @if($qrCodes->hasPages())

                    <div class="px-6 py-5 border-t border-slate-200">

                        {{ $qrCodes->links() }}

                    </div>

                @endif

            @endif

        </div>

    </div>

</div>

@endsection