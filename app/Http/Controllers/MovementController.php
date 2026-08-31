<?php

namespace App\Http\Controllers;

use App\Models\AccessPoint;
use App\Models\Employee;
use App\Models\Movement;
use App\Models\QrCode;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    /**
     * Liste des mouvements.
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

        $today = now()->toDateString();

        $todayEntries = Movement::whereDate('occurred_at', $today)
            ->where('type', 'entry')
            ->count();

        $todayExits = Movement::whereDate('occurred_at', $today)
            ->where('type', 'exit')
            ->count();

        /*
         * Nombre d'employés actuellement présents.
         *
         * On prend le dernier mouvement de chaque employé
         * pour déterminer s'il est actuellement à l'intérieur.
         */
        $employeesInside = Employee::where('status', 'active')
            ->whereHas('movements', function ($query) {
                $query->where('type', 'entry')
                    ->whereDate('occurred_at', today());
            })
            ->whereHas('movements', function ($query) {
                $query->where('type', 'entry')
                    ->whereDate('occurred_at', today())
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('movements as exits')
                            ->whereColumn(
                                'exits.employee_id',
                                'movements.employee_id'
                            )
                            ->whereDate(
                                'exits.occurred_at',
                                DB::raw('DATE(movements.occurred_at)')
                            )
                            ->where('exits.type', 'exit')
                            ->whereColumn(
                                'exits.occurred_at',
                                '>',
                                'movements.occurred_at'
                            );
                    });
            })
            ->count();

        $visitorsInside = Visitor::where('status', 'inside')->count();

        return view('movements.index', compact(
            'movements',
            'todayEntries',
            'todayExits',
            'employeesInside',
            'visitorsInside'
        ));
    }


    /**
     * Pointage personnel de l'employé connecté.
     */
    public function myAttendance(Request $request)
    {
        $user = $request->user();

        if (!$user->employee_id) {
            abort(
                403,
                'Votre compte n’est associé à aucun employé.'
            );
        }

        $employee = Employee::findOrFail($user->employee_id);

        if ($employee->status !== 'active') {
            abort(
                403,
                'Votre compte employé n’est pas actif.'
            );
        }

        $today = now()->toDateString();

        $entry = Movement::where('employee_id', $employee->id)
            ->whereDate('occurred_at', $today)
            ->where('type', 'entry')
            ->latest('occurred_at')
            ->first();

        $exit = Movement::where('employee_id', $employee->id)
            ->whereDate('occurred_at', $today)
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
     * Enregistrer l'entrée de l'employé connecté.
     */
    public function clockIn(Request $request)
    {
        $user = $request->user();

        if (!$user->employee_id) {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte n’est associé à aucun employé.',
            ]);
        }

        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return back()->withErrors([
                'attendance' =>
                    'Employé associé au compte introuvable.',
            ]);
        }

        if ($employee->status !== 'active') {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte employé n’est pas actif.',
            ]);
        }

        $now = now();

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

        $alreadyExists = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate('occurred_at', $now->toDateString())
            ->where('type', 'entry')
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'attendance' =>
                    'Vous avez déjà pointé votre entrée aujourd’hui.',
            ]);
        }

        $accessPoint = $this->getActiveAccessPoint();

        if (!$accessPoint) {
            return back()->withErrors([
                'attendance' =>
                    'Aucun point d’accès actif n’est configuré.',
            ]);
        }

        DB::transaction(function () use (
            $employee,
            $accessPoint,
            $now,
            $request
        ) {
            Movement::create([
                'employee_id' => $employee->id,
                'visitor_id' => null,
                'access_point_id' => $accessPoint->id,
                'type' => 'entry',
                'method' => 'manual',
                'occurred_at' => $now,
                'device_id' => substr(md5($request->userAgent() . $request->ip()), 0, 50),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'verification_status' => 'verified',
                'anomaly_score' => 0,
                'notes' =>
                    'Pointage d’entrée effectué par l’employé connecté.',
            ]);
        });

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
     * Enregistrer la sortie de l'employé connecté.
     */
    public function clockOut(Request $request)
    {
        $user = $request->user();

        if (!$user->employee_id) {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte n’est associé à aucun employé.',
            ]);
        }

        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return back()->withErrors([
                'attendance' =>
                    'Employé associé au compte introuvable.',
            ]);
        }

        if ($employee->status !== 'active') {
            return back()->withErrors([
                'attendance' =>
                    'Votre compte employé n’est pas actif.',
            ]);
        }

        $now = now();

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

        $entry = Movement::where('employee_id', $employee->id)
            ->whereDate('occurred_at', $now->toDateString())
            ->where('type', 'entry')
            ->latest('occurred_at')
            ->first();

        if (!$entry) {
            return back()->withErrors([
                'attendance' =>
                    'Impossible de pointer la sortie : aucune entrée n’a été enregistrée aujourd’hui.',
            ]);
        }

        $alreadyExists = Movement::where(
            'employee_id',
            $employee->id
        )
            ->whereDate('occurred_at', $now->toDateString())
            ->where('type', 'exit')
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'attendance' =>
                    'Vous avez déjà pointé votre sortie aujourd’hui.',
            ]);
        }

        $accessPoint = $this->getActiveAccessPoint();

        if (!$accessPoint) {
            return back()->withErrors([
                'attendance' =>
                    'Aucun point d’accès actif n’est configuré.',
            ]);
        }

        DB::transaction(function () use (
            $employee,
            $accessPoint,
            $now,
            $request
        ) {
            Movement::create([
                'employee_id' => $employee->id,
                'visitor_id' => null,
                'access_point_id' => $accessPoint->id,
                'type' => 'exit',
                'method' => 'manual',
                'occurred_at' => $now,
                'device_id' => $this->getDeviceId($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'verification_status' => 'verified',
                'anomaly_score' => 0,
                'notes' =>
                    'Pointage de sortie effectué par l’employé connecté.',
            ]);
        });

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
     * Formulaire de création manuelle.
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
     * Enregistrer un mouvement manuel.
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

        $accessPoint = AccessPoint::where(
            'id',
            $validated['access_point_id']
        )
            ->where('is_active', true)
            ->first();

        if (!$accessPoint) {
            return back()
                ->withErrors([
                    'access_point_id' =>
                        'Le point d’accès sélectionné est inactif ou introuvable.',
                ])
                ->withInput();
        }

        $occurredAt = Carbon::parse(
            $validated['occurred_at']
        );

        /*
         * EMPLOYÉ
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
         * VISITEUR
         */
        if (!empty($validated['visitor_id'])) {

            $visitor = Visitor::findOrFail(
                $validated['visitor_id']
            );

            if (
                $validated['type'] === 'entry' &&
                $visitor->status !== 'expected'
            ) {
                return back()
                    ->withErrors([
                        'visitor_id' =>
                            'Ce visiteur n’est pas actuellement attendu.',
                    ])
                    ->withInput();
            }

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
         * CRÉATION
         */
        DB::transaction(function () use (
            $validated,
            $occurredAt
        ) {

            $validated['occurred_at'] = $occurredAt;

            Movement::create($validated);

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
        });

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
     * Scanner un QR Code.
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

        if (!$qrCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Ce QR Code est désactivé ou expiré.',
            ], 422);
        }

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

        $accessPointId =
            $validated['access_point_id'] ?? null;

        if ($accessPointId) {

            $accessPoint = AccessPoint::where(
                'id',
                $accessPointId
            )
                ->where('is_active', true)
                ->first();

        } else {

            $accessPoint =
                $this->getActiveAccessPoint();
        }

        if (!$accessPoint) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Aucun point d’accès actif n’est configuré.',
            ], 422);
        }


        /*
         * EMPLOYÉ
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

            $movement = DB::transaction(
                function () use (
                    $employee,
                    $accessPoint,
                    $validated,
                    $now,
                    $request,
                    $qrCode
                ) {

                    $movement = Movement::create([
                        'employee_id' =>
                            $employee->id,

                        'visitor_id' =>
                            null,

                        'access_point_id' =>
                            $accessPoint->id,

                        'type' =>
                            $validated['type'],

                        'method' =>
                            'qr',

                        'occurred_at' =>
                            $now,

                        'device_id' =>
                            $this->getDeviceId($request),

                        'ip_address' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'verification_status' =>
                            'verified',

                        'anomaly_score' =>
                            0,

                        'notes' =>
                            'Pointage employé effectué par QR Code.',
                    ]);

                    $qrCode->markAsUsed();

                    return $movement;
                }
            );

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
         * VISITEUR
         */
        $visitor = $qrCode->visitor;

        $now = now();

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

        $movement = DB::transaction(
            function () use (
                $visitor,
                $accessPoint,
                $validated,
                $now,
                $request,
                $qrCode
            ) {

                $movement = Movement::create([
                    'employee_id' =>
                        null,

                    'visitor_id' =>
                        $visitor->id,

                    'access_point_id' =>
                        $accessPoint->id,

                    'type' =>
                        $validated['type'],

                    'method' =>
                        'qr',

                    'occurred_at' =>
                        $now,

                    'device_id' =>
                        $this->getDeviceId($request),

                    'ip_address' =>
                        $request->ip(),

                    'user_agent' =>
                        $request->userAgent(),

                    'verification_status' =>
                        'verified',

                    'anomaly_score' =>
                        0,

                    'notes' =>
                        'Mouvement visiteur enregistré par scan QR.',
                ]);

                $qrCode->markAsUsed();

                if ($validated['type'] === 'entry') {

                    $visitor->update([
                        'status' => 'inside',
                    ]);

                } else {

                    $visitor->update([
                        'status' => 'completed',
                    ]);
                }

                return $movement;
            }
        );

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
     * Vérification des horaires employés.
     */
    private function validateEmployeeMovementTime(
        Employee $employee,
        string $type,
        Carbon $dateTime
    ): ?string {

        $time = $dateTime->format('H:i');

        if ($type === 'entry') {

            if ($time < '06:00') {
                return
                    'Le pointage d’entrée est disponible à partir de 06:00.';
            }

            if ($time > '08:30') {
                return
                    'Le pointage d’entrée est fermé. Il est possible de pointer entre 06:00 et 08:30.';
            }
        }

        if ($type === 'exit') {

            if ($time < '18:00') {
                return
                    'Le pointage de sortie est disponible à partir de 18:00.';
            }

            if ($time > '22:00') {
                return
                    'Le pointage de sortie est fermé. Il est possible de pointer entre 18:00 et 22:00.';
            }
        }

        return null;
    }


    /**
     * Vérification des horaires visiteurs.
     */
    private function validateVisitorMovementTime(
        string $type,
        Carbon $dateTime
    ): ?string {

        $time = $dateTime->format('H:i');

        if ($type === 'entry') {

            if ($time < '06:00') {
                return
                    'L’entrée des visiteurs est disponible à partir de 06:00.';
            }

            if ($time > '08:30') {
                return
                    'L’entrée des visiteurs est fermée. Les entrées sont autorisées entre 06:00 et 08:30.';
            }
        }

        if ($type === 'exit') {

            if ($time < '18:00') {
                return
                    'La sortie des visiteurs est disponible à partir de 18:00.';
            }

            if ($time > '22:00') {
                return
                    'La sortie des visiteurs est fermée. Les sorties sont autorisées entre 18:00 et 22:00.';
            }
        }

        return null;
    }


    /**
     * Premier point d'accès actif.
     */
    private function getActiveAccessPoint(): ?AccessPoint
    {
        return AccessPoint::where(
            'is_active',
            true
        )
            ->orderBy('id')
            ->first();
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
