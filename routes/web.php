<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Accueil
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Utilisateurs connectés
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | POINTAGE PERSONNEL
    |--------------------------------------------------------------------------
    |
    | Employés et visiteurs peuvent accéder à leur espace.
    |
    */

    Route::middleware('role:employee,visitor')->group(function () {

        Route::get('/my-attendance', [
            MovementController::class,
            'myAttendance',
        ])->name('my_attendance');

        Route::post('/my-attendance/entry', [
            MovementController::class,
            'clockIn',
        ])->name('my_attendance.entry');

        Route::post('/my-attendance/exit', [
            MovementController::class,
            'clockOut',
        ])->name('my_attendance.exit');

    });


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATION
    |--------------------------------------------------------------------------
    |
    | Seul l'administrateur peut gérer et consulter toutes les données.
    |
    */

    Route::middleware('role:admin')->group(function () {

        /*
        |----------------------------------------------------------------------
        | Employés
        |----------------------------------------------------------------------
        */

        Route::resource('employees', EmployeeController::class);


        /*
        |----------------------------------------------------------------------
        | Départements
        |----------------------------------------------------------------------
        */

        Route::resource('departments', DepartmentController::class)
            ->except(['show']);


        /*
        |----------------------------------------------------------------------
        | Mouvements
        |----------------------------------------------------------------------
        */

        Route::resource('movements', MovementController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
            ]);

        Route::post('/movements/scan', [
            MovementController::class,
            'scan',
        ])->name('movements.scan');


        /*
        |----------------------------------------------------------------------
        | Visiteurs
        |----------------------------------------------------------------------
        */

        Route::resource('visitors', VisitorController::class);


        /*
        |----------------------------------------------------------------------
        | QR Codes
        |----------------------------------------------------------------------
        */

        Route::resource('qr_codes', QrCodeController::class)
            ->only([
                'index',
                'create',
                'store',
                'show',
                'destroy',
            ]);

        Route::get('/qr_codes/scan', [
            QrCodeController::class,
            'scan',
        ])->name('qr_codes.scan');

        Route::patch('/qr_codes/{qrCode}/toggle', [
            QrCodeController::class,
            'toggle',
        ])->name('qr_codes.toggle');

    });


    /*
    |--------------------------------------------------------------------------
    | Profil
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';