<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Liste des employés.
     */
    public function index(): View
    {
        $employees = Employee::with([
            'department',
            'user',
        ])
            ->latest()
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.create', compact('departments'));
    }

    /**
     * Enregistrer un nouvel employé
     * et éventuellement son compte de connexion.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => [
                'required',
                'exists:departments,id',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'position' => [
                'nullable',
                'string',
                'max:150',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'create_account' => [
                'nullable',
                'boolean',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification du département
        |--------------------------------------------------------------------------
        */

        $department = Department::findOrFail(
            $validated['department_id']
        );

        if (!$department->is_active) {
            return back()
                ->withInput()
                ->withErrors([
                    'department_id' =>
                        'Ce département est actuellement inactif.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification du compte
        |--------------------------------------------------------------------------
        */

        $createAccount = $request->boolean('create_account');

        if ($createAccount) {

            if (empty($validated['email'])) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'email' =>
                            'Une adresse e-mail est obligatoire pour créer un compte.',
                    ]);
            }

            if (empty($validated['password'])) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'password' =>
                            'Un mot de passe est obligatoire pour créer un compte.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Création employé + compte
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $createAccount
        ) {

            $employee = Employee::create([
                'department_id' => $validated['department_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
                'status' => $validated['status'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Création du compte utilisateur
            |--------------------------------------------------------------------------
            */

            if ($createAccount) {

                $user = User::create([
                    'name' =>
                        $employee->first_name .
                        ' ' .
                        $employee->last_name,

                    'email' => $validated['email'],

                    'password' =>
                        Hash::make($validated['password']),

                    'role' => 'employee',

                    'employee_id' => $employee->id,
                ]);
            }
        });

        return redirect()
            ->route('employees.index')
            ->with(
                'success',
                $createAccount
                    ? 'Employé et compte de connexion créés avec succès.'
                    : 'Employé ajouté avec succès.'
            );
    }

    /**
     * Afficher la fiche d'un employé.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'user',
            'movements',
            'anomalies',
            'qrCodes',
            'workSchedules',
        ]);

        return view(
            'employees.show',
            compact('employee')
        );
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Employee $employee): View
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'employees.edit',
            compact(
                'employee',
                'departments'
            )
        );
    }

    /**
     * Modifier un employé.
     */
    public function update(
        Request $request,
        Employee $employee
    ): RedirectResponse {

        $validated = $request->validate([
            'department_id' => [
                'required',
                'exists:departments,id',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email,' .
                    optional($employee->user)->id,
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'position' => [
                'nullable',
                'string',
                'max:150',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $department = Department::findOrFail(
            $validated['department_id']
        );

        if (!$department->is_active) {
            return back()
                ->withInput()
                ->withErrors([
                    'department_id' =>
                        'Ce département est actuellement inactif.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $employee
        ) {

            $employee->update($validated);

            /*
            |--------------------------------------------------------------------------
            | Synchroniser le compte utilisateur
            |--------------------------------------------------------------------------
            */

            if ($employee->user) {

                $employee->user->update([
                    'name' =>
                        $employee->first_name .
                        ' ' .
                        $employee->last_name,

                    'email' => $employee->email,

                    'role' => 'employee',
                ]);
            }
        });

        return redirect()
            ->route(
                'employees.show',
                $employee
            )
            ->with(
                'success',
                'Employé modifié avec succès.'
            );
    }

    /**
     * Supprimer un employé.
     */
    public function destroy(
        Employee $employee
    ): RedirectResponse {

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with(
                'success',
                'Employé supprimé avec succès.'
            );
    }
}