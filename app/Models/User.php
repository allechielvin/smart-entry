<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Champs pouvant être remplis automatiquement.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
    ];

    /**
     * Champs cachés.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversion des attributs.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Employé associé au compte.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est un employé.
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Vérifie si l'utilisateur est un visiteur.
     */
    public function isVisitor(): bool
    {
        return $this->role === 'visitor';
    }
}