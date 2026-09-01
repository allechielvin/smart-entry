<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id'
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
        ]);

        // Vérifier que le département existe et est actif
        $department = Department::where('id', $request->department_id)
            ->where('is_active', true)
            ->first();

        if (!$department) {
            throw ValidationException::withMessages([
                'department_id' => 'Le département sélectionné n’est pas disponible.',
            ]);
        }

        // Séparer le nom et le prénom
        $parts = preg_split('/\s+/', trim($request->name), 2);

        $firstName = $parts[0];
        $lastName = $parts[1] ?? '';

        // Créer automatiquement l'employé
        $employee = Employee::create([
            'department_id' => $department->id,
            'employee_number' => 'EMP-' . strtoupper(uniqid()),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'status' => 'active',
        ]);

        // Créer le compte utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'employee_id' => $employee->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}