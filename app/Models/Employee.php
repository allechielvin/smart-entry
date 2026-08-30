<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    /**
     * Champs pouvant être remplis automatiquement.
     */
    protected $fillable = [
        'department_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'photo_path',
        'status',
    ];

    /**
     * Département auquel appartient l'employé.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Mouvements d'accès de l'employé.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Anomalies concernant l'employé.
     */
    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    /**
     * QR codes associés à l'employé.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Horaires de travail de l'employé.
     */
    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    /**
     * Compte utilisateur associé à l'employé.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Nom complet de l'employé.
     */
    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' . $this->last_name
        );
    }
}