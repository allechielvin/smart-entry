@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-6 py-6">

            <div class="flex items-center gap-4">

                <a href="{{ route('qr_codes.index') }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    ←
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Scanner un QR Code
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Placez le QR Code devant la caméra.
                    </p>
                </div>

            </div>

        </div>
    </div>


    {{-- CONTENU --}}
    <div class="max-w-4xl mx-auto px-6 py-8">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- TITRE --}}
            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Scanner
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Autorisez l'accès à votre caméra pour commencer.
                </p>

            </div>


            <div class="p-6">

                {{-- ZONE CAMERA --}}
                <div class="relative max-w-xl mx-auto">

                    <div id="reader"
                         class="w-full overflow-hidden rounded-2xl border-2 border-slate-200 bg-black">
                    </div>

                    {{-- CADRE DE SCAN --}}
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center">

                        <div class="w-56 h-56 border-4 border-indigo-500 rounded-2xl shadow-lg shadow-indigo-500/30">
                        </div>

                    </div>

                </div>


                {{-- MESSAGE --}}
                <div id="scan-status"
                     class="mt-6 p-4 rounded-xl bg-slate-100 border border-slate-200 text-center text-slate-600">

                    Initialisation de la caméra...

                </div>


                {{-- RESULTAT --}}
                <div id="scan-result"
                     class="hidden mt-6">

                    <div id="result-box"
                         class="rounded-2xl p-6 border">

                        <div id="result-icon"
                             class="text-4xl text-center mb-3">
                        </div>

                        <h3 id="result-title"
                            class="text-xl font-bold text-center">
                        </h3>

                        <p id="result-message"
                           class="mt-2 text-center text-slate-600">
                        </p>

                        <div id="owner-info"
                             class="hidden mt-5 bg-white rounded-xl border border-slate-200 p-4">

                            <p class="text-xs font-semibold text-slate-500 uppercase">
                                Propriétaire
                            </p>

                            <p id="owner-name"
                               class="mt-1 text-lg font-bold text-slate-900">
                            </p>

                            <p id="owner-type"
                               class="text-sm text-slate-500">
                            </p>

                        </div>

                    </div>

                </div>


                {{-- BOUTON RETOUR --}}
                <div class="mt-6 flex justify-center">

                    <a href="{{ route('qr_codes.index') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">

                        Retour aux QR Codes

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- LIBRAIRIE QR CODE --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const status = document.getElementById('scan-status');
    const result = document.getElementById('scan-result');

    const resultBox = document.getElementById('result-box');
    const resultIcon = document.getElementById('result-icon');
    const resultTitle = document.getElementById('result-title');
    const resultMessage = document.getElementById('result-message');

    const ownerInfo = document.getElementById('owner-info');
    const ownerName = document.getElementById('owner-name');
    const ownerType = document.getElementById('owner-type');

    let scanner = null;
    let processing = false;


    /*
    |--------------------------------------------------------------------------
    | Afficher le statut
    |--------------------------------------------------------------------------
    */

    function setStatus(message, type = 'normal') {

        status.textContent = message;

        status.className =
            'mt-6 p-4 rounded-xl border text-center';

        if (type === 'success') {

            status.classList.add(
                'bg-green-50',
                'border-green-200',
                'text-green-700'
            );

        } else if (type === 'error') {

            status.classList.add(
                'bg-red-50',
                'border-red-200',
                'text-red-700'
            );

        } else {

            status.classList.add(
                'bg-slate-100',
                'border-slate-200',
                'text-slate-600'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Afficher le résultat
    |--------------------------------------------------------------------------
    */

    function showResult(data) {

        result.classList.remove('hidden');

        ownerInfo.classList.add('hidden');

        if (data.success) {

            resultBox.className =
                'rounded-2xl p-6 border bg-green-50 border-green-200';

            resultIcon.textContent = '✓';

            resultIcon.className =
                'text-4xl text-center mb-3 text-green-600';

            resultTitle.textContent = 'QR Code valide';

            resultTitle.className =
                'text-xl font-bold text-center text-green-800';

            resultMessage.textContent = data.message;

            if (data.owner) {

                ownerInfo.classList.remove('hidden');

                ownerName.textContent =
                    data.owner.first_name +
                    ' ' +
                    data.owner.last_name;

                ownerType.textContent =
                    data.owner.type === 'employee'
                        ? 'Employé'
                        : 'Visiteur';
            }

            setStatus(
                'QR Code détecté avec succès.',
                'success'
            );

        } else {

            resultBox.className =
                'rounded-2xl p-6 border bg-red-50 border-red-200';

            resultIcon.textContent = '✕';

            resultIcon.className =
                'text-4xl text-center mb-3 text-red-600';

            resultTitle.textContent = 'QR Code invalide';

            resultTitle.className =
                'text-xl font-bold text-center text-red-800';

            resultMessage.textContent =
                data.message || 'QR Code invalide.';

            setStatus(
                'Le QR Code n\'est pas valide.',
                'error'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Envoyer le token au serveur
    |--------------------------------------------------------------------------
    */

    async function processToken(token) {

        if (processing) {
            return;
        }

        processing = true;

        setStatus(
            'QR Code détecté. Vérification...',
            'normal'
        );

        try {

            const response = await fetch(
                "{{ route('qr_codes.scan.process') }}",
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            "{{ csrf_token() }}"
                    },

                    body: JSON.stringify({
                        token: token
                    })
                }
            );


            const data = await response.json();

            showResult(data);


        } catch (error) {

            console.error(error);

            showResult({
                success: false,
                message:
                    'Une erreur est survenue lors de la vérification.'
            });

        } finally {

            processing = false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | QR Code détecté
    |--------------------------------------------------------------------------
    */

    function onScanSuccess(decodedText) {

        processToken(decodedText);
    }


    /*
    |--------------------------------------------------------------------------
    | Erreur de scan
    |--------------------------------------------------------------------------
    */

    function onScanFailure(errorMessage) {

        // Ne rien afficher ici.
        // Cette fonction est appelée très souvent
        // pendant la recherche du QR Code.
    }


    /*
    |--------------------------------------------------------------------------
    | Démarrer la caméra
    |--------------------------------------------------------------------------
    */

    scanner = new Html5QrcodeScanner(
        "reader",
        {
            fps: 10,

            qrbox: {
                width: 250,
                height: 250
            },

            rememberLastUsedCamera: true,

            showTorchButtonIfSupported: true
        },
        false
    );


    scanner.render(
        onScanSuccess,
        onScanFailure
    );

});

</script>

@endsection