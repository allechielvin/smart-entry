<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')
            ->latest()
            ->paginate(10);

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Département ajouté avec succès.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Département modifié avec succès.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->exists()) {
            return redirect()
                ->route('departments.index')
                ->with('error', 'Impossible de supprimer ce département car des employés y sont rattachés.');
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Département supprimé avec succès.');
    }
}