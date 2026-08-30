<?php

namespace App\Http\Controllers;

use App\Models\AccessPoint;
use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Page de pointage de l'employé.
     */
    public function index(): View
    {
        $user = auth()->user();

        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Aucun employé associé à ce compte.');
        }

        if ($employee->status !== 'active') {
            abort(403, 'Votre compte employé est actuellement inactif.');
        }

        $today = Carbon::today();

        $entry = Movement::where('employee_id', $employee->id)
            ->where('type', 'entry')
            ->whereDate('occurred_at', $today)
            ->latest('occurred_at')
            ->first();

        $exit = Movement::where('employee_id', $employee->id)
            ->where('type', 'exit')
            ->whereDate('occurred_at', $today)
            ->latest('occurred_at')
            ->first();

        $accessPoints = AccessPoint::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('attendance.index', compact(
            'employee',
            'entry',
            'exit',
            'accessPoints'
        ));
    }

    /**
     * Enregistrer un pointage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:entry,exit',
            ],

            'access_point_id' => [
                'required',
                'exists:access_points,id',
            ],
        ]);

        $user = auth()->user();

        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Aucun employé associé à ce compte.');
        }

        if ($employee->status !== 'active') {
            return back()
                ->withErrors([
                    'attendance' =>
                        'Votre compte employé est actuellement inactif.',
                ]);
        }

        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | Vérification du point d'accès
        |--------------------------------------------------------------------------
        */

        $accessPoint = AccessPoint::where('id', $validated['access_point_id'])
            ->where('is_active', true)
            ->first();

        if (!$accessPoint) {
            return back()
                ->withErrors([
                    'attendance' =>
                        'Ce point d’accès n’est pas disponible.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification du type de point d'accès
        |--------------------------------------------------------------------------
        */

        if (
            $accessPoint->type !== 'both' &&
            $accessPoint->type !== (
                $validated['type'] === 'entry'
                    ? 'entrance'
                    : 'exit'
            )
        ) {
            return back()
                ->withErrors([
                    'attendance' =>
                        'Ce point d’accès ne permet pas cette opération.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification des horaires
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] === 'entry') {

            $start = $now->copy()->setTime(6, 0, 0);
            $end = $now->copy()->setTime(8, 30, 0);

            if (!$now->between($start, $end)) {
                return back()
                    ->withErrors([
                        'attendance' =>
                            'Le pointage d’entrée est autorisé uniquement entre 06h00 et 08h30.',
                    ]);
            }
        }

        if ($validated['type'] === 'exit') {

            $start = $now->copy()->setTime(18, 0, 0);
            $end = $now->copy()->setTime(22, 0, 0);

            if (!$now->between($start, $end)) {
                return back()
                    ->withErrors([
                        'attendance' =>
                            'Le pointage de sortie est autorisé uniquement entre 18h00 et 22h00.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification des mouvements du jour
        |--------------------------------------------------------------------------
        */

        $today = $now->toDateString();

        $hasEntry = Movement::where('employee_id', $employee->id)
            ->where('type', 'entry')
            ->whereDate('occurred_at', $today)
            ->exists();

        $hasExit = Movement::where('employee_id', $employee->id)
            ->where('type', 'exit')
            ->whereDate('occurred_at', $today)
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | Empêcher une double entrée
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] === 'entry' && $hasEntry) {
            return back()
                ->withErrors([
                    'attendance' =>
                        'Vous avez déjà enregistré votre entrée aujourd’hui.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Empêcher une sortie sans entrée
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] === 'exit' && !$hasEntry) {
            return back()
                ->withErrors([
                    'attendance' =>
                        'Vous devez d’abord enregistrer votre entrée avant de pointer votre sortie.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Empêcher une double sortie
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] === 'exit' && $hasExit) {
            return back()
                ->withErrors([
                    'attendance' =>
                        'Vous avez déjà enregistré votre sortie aujourd’hui.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Création du mouvement
        |--------------------------------------------------------------------------
        */

        Movement::create([
            'employee_id' => $employee->id,
            'visitor_id' => null,
            'access_point_id' => $accessPoint->id,

            'type' => $validated['type'],

            'method' => 'manual',

            'occurred_at' => $now,

            'device_id' => $request->header('User-Agent'),

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),

            'verification_status' => 'verified',

            'anomaly_score' => 0,

            'notes' => null,
        ]);

        return redirect()
            ->route('attendance.index')
            ->with(
                'success',
                $validated['type'] === 'entry'
                    ? 'Votre entrée a été enregistrée avec succès.'
                    : 'Votre sortie a été enregistrée avec succès.'
            );
    }
}