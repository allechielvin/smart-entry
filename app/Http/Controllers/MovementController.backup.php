<?php

namespace App\Http\Controllers;

use App\Models\AccessPoint;
use App\Models\Employee;
use App\Models\Movement;
use App\Models\QrCode;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    /**
     * Liste de tous les mouvements.
     *
     * Cette page est destinée à l'administration.
     */
    public function index()
    {
        $movements = Movement::with([
            'employee',
            'visitor',
            'accessPoint',
        ])
            ->latest('occurred_at')
            ->paginate(15);

        return view('movements.index', compact('movements'));
    }


    /**
     * Tableau de pointage personnel de l'employé connecté.
     */
    public function myAttendance(Request $request)
    {
        $user = $request->user();

        /*
         * Pour l'instant cette page est destinée
         * aux comptes associés à un employé.
         */
        if (!$user->employee_id) {
            abort(
                403,
                'Votre compte n’est associé à aucun employé.'
            );
        }

        $employee = Employee::findOrFail(
            $user->employee_id
        );

        if ($employee->status !== 'active') {
            abort(
                403,
                'Votre compte employé n’est pas actif.'
            );
        }

        $today = now()->toDateString();

        $entry = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'occurred_at',
                $today
            )
            ->where('type', 'entry')
            ->latest('occurred_at')
            ->first();

        $exit = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'occurred_at',
                $today
            )
            ->where('type', 'exit')
            ->latest('occurred_at')
            ->first();

        return view('movements.my-attendance', [
            'employee' => $employee,
            'entry' => $entry,
            'exit' => $exit,
            'currentTime' => now(),
        ]);
    }


    /**
     * Pointer son entrée en tant qu'employé connecté.
     *
     * Autorisé uniquement :
     * 06:00 -> 08:30
     */
    public function clockIn(Request $request)
    {
        $user = $request->user();

        /*
         * Vérifier que le compte est lié à un employé.
         */
        if (!$user->employee_id) {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte n’est associé à aucun employé.',
            ]);
        }

        $employee = Employee::find(
            $user->employee_id
        );

        if (!$employee) {
            return back()->withErrors([
                'attendance' =>
                    'Employé associé au compte introuvable.',
            ]);
        }

        /*
         * Vérifier que l'employé est actif.
         */
        if ($employee->status !== 'active') {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte employé n’est pas actif.',
            ]);
        }

        $now = now();

        /*
         * Vérifier l'heure.
         */
        $timeError = $this->validateEmployeeMovementTime(
            $employee,
            'entry',
            $now
        );

        if ($timeError) {
            return back()->withErrors([
                'attendance' => $timeError,
            ]);
        }

        /*
         * Vérifier si l'entrée existe déjà aujourd'hui.
         */
        $alreadyExists = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'occurred_at',
                $now->toDateString()
            )
            ->where('type', 'entry')
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'attendance' =>
                    'Vous avez déjà pointé votre entrée aujourd’hui.',
            ]);
        }

        /*
         * Trouver un point d'accès actif.
         */
        $accessPoint = AccessPoint::where(
            'is_active',
            true
        )->first();

        if (!$accessPoint) {
            return back()->withErrors([
                'attendance' =>
                    'Aucun point d’accès actif n’est configuré.',
            ]);
        }

        /*
         * Enregistrer l'entrée.
         */
        Movement::create([
            'employee_id' => $employee->id,
            'visitor_id' => null,

            'access_point_id' => $accessPoint->id,

            'type' => 'entry',
            'method' => 'manual',

            'occurred_at' => $now,

            'device_id' => $request->header('User-Agent'),

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),

            'verification_status' => 'verified',

            'anomaly_score' => 0,

            'notes' =>
                'Pointage d’entrée effectué par l’employé connecté.',
        ]);

        return redirect()
            ->route('my_attendance')
            ->with(
                'success',
                'Bonjour ' .
                $employee->first_name .
                ', votre entrée a été enregistrée à ' .
                $now->format('H:i') .
                '.'
            );
    }


    /**
     * Pointer sa sortie en tant qu'employé connecté.
     *
     * Autorisé uniquement :
     * 18:00 -> 22:00
     */
    public function clockOut(Request $request)
    {
        $user = $request->user();

        /*
         * Vérifier que le compte est lié à un employé.
         */
        if (!$user->employee_id) {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte n’est associé à aucun employé.',
            ]);
        }

        $employee = Employee::find(
            $user->employee_id
        );

        if (!$employee) {
            return back()->withErrors([
                'attendance' =>
                    'Employé associé au compte introuvable.',
            ]);
        }

        /*
         * Vérifier que l'employé est actif.
         */
        if ($employee->status !== 'active') {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte employé n’est pas actif.',
            ]);
        }

        $now = now();

        /*
         * Vérifier l'heure de sortie.
         */
        $timeError = $this->validateEmployeeMovementTime(
            $employee,
            'exit',
            $now
        );

        if ($timeError) {
            return back()->withErrors([
                'attendance' => $timeError,
            ]);
        }

        /*
         * Vérifier qu'une entrée existe aujourd'hui.
         */
        $entry = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'occurred_at',
                $now->toDateString()
            )
            ->where('type', 'entry')
            ->latest('occurred_at')
            ->first();

        if (!$entry) {
            return back()->withErrors([
                'attendance' =>
                    'Impossible de pointer la sortie : aucune entrée n’a été enregistrée aujourd’hui.',
            ]);
        }

        /*
         * Vérifier si une sortie existe déjà.
         */
        $alreadyExists = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'occurred_at',
                $now->toDateString()
            )
            ->where('type', 'exit')
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'attendance' =>
                    'Vous avez déjà pointé votre sortie aujourd’hui.',
            ]);
        }

        /*
         * Trouver un point d'accès actif.
         */
        $accessPoint = AccessPoint::where(
            'is_active',
            true
        )->first();

        if (!$accessPoint) {
            return back()->withErrors([
                'attendance' =>
                    'Aucun point d’accès actif n’est configuré.',
            ]);
        }

        /*
         * Enregistrer la sortie.
         */
        Movement::create([
            'employee_id' => $employee->id,
            'visitor_id' => null,

            'access_point_id' => $accessPoint->id,

            'type' => 'exit',
            'method' => 'manual',

            'occurred_at' => $now,

            'device_id' => $request->header('User-Agent'),

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),

            'verification_status' => 'verified',

            'anomaly_score' => 0,

            'notes' =>
                'Pointage de sortie effectué par l’employé connecté.',
        ]);

        return redirect()
            ->route('my_attendance')
            ->with(
                'success',
                'Au revoir ' .
                $employee->first_name .
                ', votre sortie a été enregistrée à ' .
                $now->format('H:i') .
                '.'
            );
    }


    /**
     * Formulaire de création manuelle par l'administration.
     */
    public function create()
    {
        $employees = Employee::where(
            'status',
            'active'
        )
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

        $accessPoints = AccessPoint::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'movements.create',
            compact(
                'employees',
                'visitors',
                'accessPoints'
            )
        );
    }


    /**
     * Enregistrer un mouvement manuellement par l'administration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'visitor_id' => [
                'nullable',
                'exists:visitors,id',
            ],

            'access_point_id' => [
                'required',
                'exists:access_points,id',
            ],

            'type' => [
                'required',
                'in:entry,exit',
            ],

            'method' => [
                'required',
                'in:qr,nfc,manual',
            ],

            'occurred_at' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Une seule personne doit être associée.
         */
        if (
            empty($validated['employee_id']) &&
            empty($validated['visitor_id'])
        ) {
            return back()
                ->withErrors([
                    'employee_id' =>
                        'Veuillez sélectionner un employé ou un visiteur.',
                ])
                ->withInput();
        }

        if (
            !empty($validated['employee_id']) &&
            !empty($validated['visitor_id'])
        ) {
            return back()
                ->withErrors([
                    'employee_id' =>
                        'Un mouvement ne peut concerner qu’une seule personne.',
                ])
                ->withInput();
        }

        $occurredAt = Carbon::parse(
            $validated['occurred_at']
        );

        /*
         * ============================================================
         * EMPLOYÉ
         * ============================================================
         */
        if (!empty($validated['employee_id'])) {

            $employee = Employee::findOrFail(
                $validated['employee_id']
            );

            if ($employee->status !== 'active') {
                return back()
                    ->withErrors([
                        'employee_id' =>
                            'Cet employé n’est pas actif.',
                    ])
                    ->withInput();
            }

            /*
             * Appliquer les horaires.
             */
            $timeError = $this->validateEmployeeMovementTime(
                $employee,
                $validated['type'],
                $occurredAt
            );

            if ($timeError) {
                return back()
                    ->withErrors([
                        'occurred_at' => $timeError,
                    ])
                    ->withInput();
            }

            /*
             * Vérifier les doublons.
             */
            $alreadyExists = Movement::where(
                'employee_id',
                $employee->id
            )
                ->whereDate(
                    'occurred_at',
                    $occurredAt->toDateString()
                )
                ->where(
                    'type',
                    $validated['type']
                )
                ->exists();

            if ($alreadyExists) {
                return back()
                    ->withErrors([
                        'employee_id' =>
                            $validated['type'] === 'entry'
                                ? 'Cet employé a déjà pointé son entrée aujourd’hui.'
                                : 'Cet employé a déjà pointé sa sortie aujourd’hui.',
                    ])
                    ->withInput();
            }

            /*
             * Une sortie nécessite une entrée.
             */
            if ($validated['type'] === 'exit') {

                $hasEntry = Movement::where(
                    'employee_id',
                    $employee->id
                )
                    ->whereDate(
                        'occurred_at',
                        $occurredAt->toDateString()
                    )
                    ->where('type', 'entry')
                    ->exists();

                if (!$hasEntry) {
                    return back()
                        ->withErrors([
                            'type' =>
                                'Impossible d’enregistrer la sortie : cet employé n’a pas pointé son entrée aujourd’hui.',
                        ])
                        ->withInput();
                }
            }
        }


        /*
         * ============================================================
         * VISITEUR
         * ============================================================
         */
        if (!empty($validated['visitor_id'])) {

            $visitor = Visitor::findOrFail(
                $validated['visitor_id']
            );

            /*
             * Un visiteur ne peut entrer que s'il est attendu.
             */
            if (
                $validated['type'] === 'entry' &&
                !in_array(
                    $visitor->status,
                    ['expected']
                )
            ) {
                return back()
                    ->withErrors([
                        'visitor_id' =>
                            'Ce visiteur n’est pas actuellement attendu.',
                    ])
                    ->withInput();
            }

            /*
             * Une sortie nécessite que le visiteur soit présent.
             */
            if (
                $validated['type'] === 'exit' &&
                $visitor->status !== 'inside'
            ) {
                return back()
                    ->withErrors([
                        'visitor_id' =>
                            'Ce visiteur n’est pas actuellement présent dans l’établissement.',
                    ])
                    ->withInput();
            }

            /*
             * Vérifier les horaires visiteurs.
             */
            $timeError = $this->validateVisitorMovementTime(
                $validated['type'],
                $occurredAt
            );

            if ($timeError) {
                return back()
                    ->withErrors([
                        'occurred_at' => $timeError,
                    ])
                    ->withInput();
            }

            /*
             * Vérifier les doublons visiteurs.
             */
            $alreadyExists = Movement::where(
                'visitor_id',
                $visitor->id
            )
                ->whereDate(
                    'occurred_at',
                    $occurredAt->toDateString()
                )
                ->where(
                    'type',
                    $validated['type']
                )
                ->exists();

            if ($alreadyExists) {
                return back()
                    ->withErrors([
                        'visitor_id' =>
                            $validated['type'] === 'entry'
                                ? 'Ce visiteur a déjà enregistré son entrée aujourd’hui.'
                                : 'Ce visiteur a déjà enregistré sa sortie aujourd’hui.',
                    ])
                    ->withInput();
            }

            /*
             * Une sortie visiteur nécessite une entrée.
             */
            if ($validated['type'] === 'exit') {

                $hasEntry = Movement::where(
                    'visitor_id',
                    $visitor->id
                )
                    ->whereDate(
                        'occurred_at',
                        $occurredAt->toDateString()
                    )
                    ->where('type', 'entry')
                    ->exists();

                if (!$hasEntry) {
                    return back()
                        ->withErrors([
                            'type' =>
                                'Impossible d’enregistrer la sortie : ce visiteur n’a pas enregistré son entrée aujourd’hui.',
                        ])
                        ->withInput();
                }
            }
        }


        /*
         * Création du mouvement.
         */
        Movement::create($validated);

        /*
         * Mise à jour du statut visiteur.
         */
        if (!empty($validated['visitor_id'])) {

            $visitor = Visitor::find(
                $validated['visitor_id']
            );

            if ($validated['type'] === 'entry') {

                $visitor->update([
                    'status' => 'inside',
                ]);

            } else {

                $visitor->update([
                    'status' => 'completed',
                ]);
            }
        }

        return redirect()
            ->route('movements.index')
            ->with(
                'success',
                $validated['type'] === 'entry'
                    ? 'Entrée enregistrée avec succès.'
                    : 'Sortie enregistrée avec succès.'
            );
    }


    /**
     * Traiter un QR Code scanné.
     */
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'token' => [
                'required',
                'string',
                'max:191',
            ],

            'type' => [
                'required',
                'in:entry,exit',
            ],

            'access_point_id' => [
                'nullable',
                'exists:access_points,id',
            ],
        ]);

        /*
         * Recherche du QR Code.
         */
        $qrCode = QrCode::with([
            'employee',
            'visitor',
        ])
            ->where(
                'token',
                $validated['token']
            )
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code inconnu.',
            ], 404);
        }

        /*
         * Vérifier si le QR est valide.
         */
        if (!$qrCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Ce QR Code est désactivé ou expiré.',
            ], 422);
        }

        /*
         * Aucun propriétaire.
         */
        if (
            !$qrCode->employee &&
            !$qrCode->visitor
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Ce QR Code n’est associé à aucune personne.',
            ], 422);
        }

        /*
         * Configuration invalide.
         */
        if (
            $qrCode->employee &&
            $qrCode->visitor
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Configuration invalide du QR Code.',
            ], 422);
        }

        /*
         * Point d'accès.
         */
        $accessPointId =
            $validated['access_point_id'] ?? null;

        if (!$accessPointId) {
            $accessPointId = AccessPoint::where(
                'is_active',
                true
            )->value('id');
        }

        if (!$accessPointId) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Aucun point d’accès actif n’est configuré.',
            ], 422);
        }


        /*
         * ============================================================
         * EMPLOYÉ PAR QR
         * ============================================================
         */
        if ($qrCode->employee) {

            $employee = $qrCode->employee;

            if ($employee->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Cet employé n’est pas actif.',
                ], 422);
            }

            $now = now();

            /*
             * Vérifier l'heure.
             */
            $timeError =
                $this->validateEmployeeMovementTime(
                    $employee,
                    $validated['type'],
                    $now
                );

            if ($timeError) {
                return response()->json([
                    'success' => false,
                    'message' => $timeError,
                ], 422);
            }

            /*
             * Vérifier les doublons.
             */
            $alreadyExists = Movement::where(
                'employee_id',
                $employee->id
            )
                ->whereDate(
                    'occurred_at',
                    $now->toDateString()
                )
                ->where(
                    'type',
                    $validated['type']
                )
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        $validated['type'] === 'entry'
                            ? 'Vous avez déjà pointé votre entrée aujourd’hui.'
                            : 'Vous avez déjà pointé votre sortie aujourd’hui.',
                ], 422);
            }

            /*
             * Une sortie nécessite une entrée.
             */
            if ($validated['type'] === 'exit') {

                $hasEntry = Movement::where(
                    'employee_id',
                    $employee->id
                )
                    ->whereDate(
                        'occurred_at',
                        $now->toDateString()
                    )
                    ->where('type', 'entry')
                    ->exists();

                if (!$hasEntry) {
                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Impossible de pointer la sortie : aucune entrée n’a été enregistrée aujourd’hui.',
                    ], 422);
                }
            }

            /*
             * Enregistrer le mouvement.
             */
            $movement = Movement::create([
                'employee_id' => $employee->id,
                'visitor_id' => null,

                'access_point_id' =>
                    $accessPointId,

                'type' =>
                    $validated['type'],

                'method' => 'qr',

                'occurred_at' => $now,

                'device_id' =>
                    $request->header('User-Agent'),

                'ip_address' =>
                    $request->ip(),

                'user_agent' =>
                    $request->userAgent(),

                'verification_status' =>
                    'verified',

                'anomaly_score' => 0,

                'notes' =>
                    'Pointage employé effectué par QR Code.',
            ]);

            $qrCode->markAsUsed();

            return response()->json([
                'success' => true,

                'message' =>
                    $validated['type'] === 'entry'
                        ? 'Bonjour ' .
                          $employee->first_name .
                          ', votre entrée est enregistrée.'
                        : 'Au revoir ' .
                          $employee->first_name .
                          ', votre sortie est enregistrée.',

                'movement_id' =>
                    $movement->id,

                'person' => [
                    'type' => 'employee',

                    'id' =>
                        $employee->id,

                    'name' =>
                        $employee->first_name .
                        ' ' .
                        $employee->last_name,
                ],
            ]);
        }


        /*
         * ============================================================
         * VISITEUR PAR QR
         * ============================================================
         */

        $visitor = $qrCode->visitor;

        $now = now();

        /*
         * Vérifier les horaires du visiteur.
         */
        $timeError =
            $this->validateVisitorMovementTime(
                $validated['type'],
                $now
            );

        if ($timeError) {
            return response()->json([
                'success' => false,
                'message' => $timeError,
            ], 422);
        }

        /*
         * Vérifier le statut du visiteur.
         */
        if (
            $validated['type'] === 'entry' &&
            $visitor->status !== 'expected'
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Ce visiteur n’est pas actuellement attendu.',
            ], 422);
        }

        if (
            $validated['type'] === 'exit' &&
            $visitor->status !== 'inside'
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Ce visiteur n’est pas actuellement présent dans l’établissement.',
            ], 422);
        }

        /*
         * Vérifier les doublons.
         */
        $alreadyExists = Movement::where(
            'visitor_id',
            $visitor->id
        )
            ->whereDate(
                'occurred_at',
                $now->toDateString()
            )
            ->where(
                'type',
                $validated['type']
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' =>
                    $validated['type'] === 'entry'
                        ? 'Ce visiteur a déjà enregistré son entrée aujourd’hui.'
                        : 'Ce visiteur a déjà enregistré sa sortie aujourd’hui.',
            ], 422);
        }

        /*
         * Une sortie nécessite une entrée.
         */
        if ($validated['type'] === 'exit') {

            $hasEntry = Movement::where(
                'visitor_id',
                $visitor->id
            )
                ->whereDate(
                    'occurred_at',
                    $now->toDateString()
                )
                ->where('type', 'entry')
                ->exists();

            if (!$hasEntry) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Impossible de pointer la sortie : aucune entrée n’a été enregistrée aujourd’hui.',
                ], 422);
            }
        }

        /*
         * Enregistrer le mouvement.
         */
        $movement = Movement::create([
            'employee_id' => null,

            'visitor_id' =>
                $visitor->id,

            'access_point_id' =>
                $accessPointId,

            'type' =>
                $validated['type'],

            'method' => 'qr',

            'occurred_at' => $now,

            'device_id' =>
                $request->header('User-Agent'),

            'ip_address' =>
                $request->ip(),

            'user_agent' =>
                $request->userAgent(),

            'verification_status' =>
                'verified',

            'anomaly_score' => 0,

            'notes' =>
                'Mouvement visiteur enregistré par scan QR.',
        ]);

        $qrCode->markAsUsed();

        /*
         * Mise à jour du statut.
         */
        if ($validated['type'] === 'entry') {

            $visitor->update([
                'status' => 'inside',
            ]);

        } else {

            $visitor->update([
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'success' => true,

            'message' =>
                $validated['type'] === 'entry'
                    ? 'Entrée du visiteur enregistrée avec succès.'
                    : 'Sortie du visiteur enregistrée avec succès.',

            'movement_id' =>
                $movement->id,

            'person' => [
                'type' => 'visitor',

                'id' =>
                    $visitor->id,

                'name' =>
                    $visitor->first_name .
                    ' ' .
                    $visitor->last_name,
            ],
        ]);
    }


    /**
     * Vérifie si un pointage employé est autorisé.
     *
     * Entrée :
     * 06:00 -> 08:30
     *
     * Sortie :
     * 18:00 -> 22:00
     */
    private function validateEmployeeMovementTime(
        Employee $employee,
        string $type,
        Carbon $dateTime
    ): ?string {

        $time = $dateTime->format('H:i');

        /*
         * ENTRÉE
         */
        if ($type === 'entry') {

            if ($time < '06:00') {
                return
                    'Le pointage d’entrée est disponible à partir de 06:00.';
            }

            if ($time > '08:30') {
                return
                    'Le pointage d’entrée est fermé. Il est possible de pointer entre 06:00 et 08:30.';
            }

            return null;
        }

        /*
         * SORTIE
         */
        if ($type === 'exit') {

            if ($time < '18:00') {
                return
                    'Le pointage de sortie est disponible à partir de 18:00.';
            }

            if ($time > '22:00') {
                return
                    'Le pointage de sortie est fermé. Il est possible de pointer entre 18:00 et 22:00.';
            }

            return null;
        }

        return null;
    }


    /**
     * Vérifie si un pointage visiteur est autorisé.
     *
     * Entrée :
     * 06:00 -> 08:30
     *
     * Sortie :
     * 18:00 -> 22:00
     */
    private function validateVisitorMovementTime(
        string $type,
        Carbon $dateTime
    ): ?string {

        $time = $dateTime->format('H:i');

        /*
         * ENTRÉE VISITEUR
         */
        if ($type === 'entry') {

            if ($time < '06:00') {
                return
                    'L’entrée des visiteurs est disponible à partir de 06:00.';
            }

            if ($time > '08:30') {
                return
                    'L’entrée des visiteurs est fermée. Les entrées sont autorisées entre 06:00 et 08:30.';
            }

            return null;
        }

        /*
         * SORTIE VISITEUR
         */
        if ($type === 'exit') {

            if ($time < '18:00') {
                return
                    'La sortie des visiteurs est disponible à partir de 18:00.';
            }

            if ($time > '22:00') {
                return
                    'La sortie des visiteurs est fermée. Les sorties sont autorisées entre 18:00 et 22:00.';
            }

            return null;
        }

        return null;
    }


    /**
     * Afficher un mouvement.
     */
    public function show(Movement $movement)
    {
        $movement->load([
            'employee',
            'visitor',
            'accessPoint',
            'anomaly',
        ]);

        return view(
            'movements.show',
            compact('movement')
        );
    }
}