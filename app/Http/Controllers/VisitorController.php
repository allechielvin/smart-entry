<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitorController extends Controller
{
    /**
     * Liste des visiteurs.
     */
    public function index()
    {
        $visitors = Visitor::with('hostEmployee')
            ->latest()
            ->paginate(15);

        return view('visitors.index', compact('visitors'));
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

        return view('visitors.create', compact('employees'));
    }

    /**
     * Enregistrer un visiteur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'company' => [
                'nullable',
                'string',
                'max:150',
            ],

            'id_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'id_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'purpose' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'host_employee_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'expected_arrival' => [
                'nullable',
                'date',
            ],

            'expected_departure' => [
                'nullable',
                'date',
                'after_or_equal:expected_arrival',
            ],

            'status' => [
                'required',
                'in:expected,inside,completed,blocked',
            ],
        ]);

        /*
         * Génération automatique du code visiteur.
         */
        $validated['visitor_code'] = 'VIS-' . strtoupper(
            Str::random(8)
        );

        Visitor::create($validated);

        return redirect()
            ->route('visitors.index')
            ->with('success', 'Visiteur ajouté avec succès.');
    }

    /**
     * Afficher un visiteur.
     */
    public function show(Visitor $visitor)
    {
        $visitor->load([
            'hostEmployee',
            'movements',
            'qrCodes',
        ]);

        return view('visitors.show', compact('visitor'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Visitor $visitor)
    {
        $employees = Employee::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('visitors.edit', compact(
            'visitor',
            'employees'
        ));
    }

    /**
     * Modifier un visiteur.
     */
    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
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

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'company' => [
                'nullable',
                'string',
                'max:150',
            ],

            'id_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'id_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'purpose' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'host_employee_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'expected_arrival' => [
                'nullable',
                'date',
            ],

            'expected_departure' => [
                'nullable',
                'date',
                'after_or_equal:expected_arrival',
            ],

            'actual_departure' => [
                'nullable',
                'date',
                'after_or_equal:expected_arrival',
            ],

            'status' => [
                'required',
                'in:expected,inside,completed,blocked',
            ],
        ]);

        $visitor->update($validated);

        return redirect()
            ->route('visitors.show', $visitor)
            ->with('success', 'Visiteur modifié avec succès.');
    }

    /**
     * Supprimer un visiteur.
     */
    public function destroy(Visitor $visitor)
    {
        $visitor->delete();

        return redirect()
            ->route('visitors.index')
            ->with('success', 'Visiteur supprimé avec succès.');
    }
}
