<?php

namespace App\Http\Controllers;

use App\Models\Anomaly;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Movement;
use App\Models\Visitor;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | ESPACE EMPLOYÉ
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'employee') {

            /*
             * Dans Smart Entry, le compte utilisateur possède
             * directement l'identifiant de son employé.
             */
            if (!$user->employee_id) {

                return view('dashboard', [
                    'employeeDashboard' => true,
                    'recentMovements' => collect(),
                ]);
            }

            // Récupérer l'employé associé au compte connecté
            $employee = Employee::find($user->employee_id);

            if (!$employee) {

                return view('dashboard', [
                    'employeeDashboard' => true,
                    'recentMovements' => collect(),
                ]);
            }

            /*
             * Vérifier que l'employé est actif
             */
            if ($employee->status !== 'active') {

                abort(
                    403,
                    'Votre compte employé n’est pas actif.'
                );
            }

            /*
             * Récupérer UNIQUEMENT les activités
             * de l'employé connecté.
             */
            $recentMovements = Movement::with([
                'accessPoint',
            ])
                ->where('employee_id', $employee->id)
                ->latest('occurred_at')
                ->take(8)
                ->get();

            return view('dashboard', [
                'employeeDashboard' => true,
                'employee' => $employee,
                'recentMovements' => $recentMovements,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ESPACE ADMINISTRATEUR
        |--------------------------------------------------------------------------
        */

        $stats = [
            'employees' => Employee::where('status', 'active')->count(),
            'visitors' => Visitor::count(),
            'movements' => Movement::count(),
            'anomalies' => Anomaly::count(),
            'departments' => Department::count(),
        ];

        /*
         * L'administrateur voit les mouvements
         * de tous les employés et visiteurs.
         */
        $recentMovements = Movement::with([
            'employee',
            'visitor',
            'accessPoint',
        ])
            ->latest('occurred_at')
            ->take(8)
            ->get();

        return view('dashboard', [
            'employeeDashboard' => false,
            'stats' => $stats,
            'recentMovements' => $recentMovements,
        ]);
    }
}