<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\QrCode;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    /**
     * Liste des QR codes.
     */
    public function index()
    {
        $qrCodes = QrCode::with([
            'employee',
            'visitor',
        ])
            ->latest()
            ->paginate(15);

        return view('qr_codes.index', compact('qrCodes'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        $employees = Employee::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $visitors = Visitor::whereIn('status', [
            'expected',
            'inside',
        ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'qr_codes.create',
            compact('employees', 'visitors')
        );
    }

    /**
     * Page de scan QR Code.
     */
    public function scan()
    {
        return view('qr_codes.scan');
    }

    /**
     * Traiter un QR Code scanné.
     */
    public function processScan(Request $request)
    {
        $validated = $request->validate([
            'token' => [
                'required',
                'string',
                'max:191',
            ],
        ]);

        $qrCode = QrCode::with([
            'employee',
            'visitor',
        ])
            ->where('token', $validated['token'])
            ->first();

        /*
        |--------------------------------------------------------------------------
        | QR Code inexistant
        |--------------------------------------------------------------------------
        */
        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code invalide ou inexistant.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | QR Code désactivé ou expiré
        |--------------------------------------------------------------------------
        */
        if (!$qrCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce QR Code est désactivé ou expiré.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Identifier le propriétaire
        |--------------------------------------------------------------------------
        */
        if ($qrCode->employee) {

            $owner = [
                'type' => 'employee',
                'id' => $qrCode->employee->id,
                'first_name' => $qrCode->employee->first_name,
                'last_name' => $qrCode->employee->last_name,
            ];

        } elseif ($qrCode->visitor) {

            $owner = [
                'type' => 'visitor',
                'id' => $qrCode->visitor->id,
                'first_name' => $qrCode->visitor->first_name,
                'last_name' => $qrCode->visitor->last_name,
            ];

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Le propriétaire de ce QR Code n\'existe plus.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Enregistrer l'utilisation
        |--------------------------------------------------------------------------
        */
        $qrCode->markAsUsed();

        /*
        |--------------------------------------------------------------------------
        | Réponse
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'success' => true,
            'message' => 'QR Code valide.',
            'qr_code' => [
                'id' => $qrCode->id,
                'version' => $qrCode->version,
                'expires_at' => $qrCode->expires_at?->format('d/m/Y H:i'),
            ],
            'owner' => $owner,
        ]);
    }

    /**
     * Enregistrer un QR code.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_type' => [
                'required',
                'in:employee,visitor',
            ],

            'employee_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'visitor_id' => [
                'nullable',
                'exists:visitors,id',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
        ]);

        if ($validated['owner_type'] === 'employee') {

            if (empty($validated['employee_id'])) {
                return back()
                    ->withErrors([
                        'employee_id' =>
                            'Veuillez sélectionner un employé.',
                    ])
                    ->withInput();
            }

        } else {

            if (empty($validated['visitor_id'])) {
                return back()
                    ->withErrors([
                        'visitor_id' =>
                            'Veuillez sélectionner un visiteur.',
                    ])
                    ->withInput();
            }
        }

        QrCode::create([
            'employee_id' =>
                $validated['owner_type'] === 'employee'
                    ? $validated['employee_id']
                    : null,

            'visitor_id' =>
                $validated['owner_type'] === 'visitor'
                    ? $validated['visitor_id']
                    : null,

            'token' => Str::random(64),

            'version' => 1,

            'is_active' => true,

            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('qr_codes.index')
            ->with(
                'success',
                'QR Code créé avec succès.'
            );
    }

    /**
     * Afficher un QR code.
     */
    public function show(QrCode $qrCode)
    {
        $qrCode->load([
            'employee',
            'visitor',
        ]);

        return view(
            'qr_codes.show',
            compact('qrCode')
        );
    }

    /**
     * Activer / désactiver un QR code.
     */
    public function toggle(QrCode $qrCode)
    {
        $qrCode->update([
            'is_active' => !$qrCode->is_active,
        ]);

        return redirect()
            ->route('qr_codes.index')
            ->with(
                'success',
                $qrCode->is_active
                    ? 'QR Code activé.'
                    : 'QR Code désactivé.'
            );
    }

    /**
     * Supprimer un QR code.
     */
    public function destroy(QrCode $qrCode)
    {
        $qrCode->delete();

        return redirect()
            ->route('qr_codes.index')
            ->with(
                'success',
                'QR Code supprimé.'
            );
    }
}