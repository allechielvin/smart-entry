<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_code',
        'first_name',
        'last_name',
        'phone',
        'email',
        'company',
        'id_type',
        'id_number',
        'photo',
        'purpose',
        'host_employee_id',
        'expected_arrival',
        'expected_departure',
        'actual_departure',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expected_arrival' => 'datetime',
            'expected_departure' => 'datetime',
            'actual_departure' => 'datetime',
        ];
    }

    /**
     * Employé qui reçoit le visiteur.
     */
    public function hostEmployee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'host_employee_id'
        );
    }

    /**
     * Mouvements du visiteur.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * QR codes du visiteur.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Nom complet du visiteur.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}