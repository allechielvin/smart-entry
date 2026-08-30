<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'visitor_id',
        'access_point_id',
        'type',
        'method',
        'occurred_at',
        'device_id',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'verification_status',
        'anomaly_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'anomaly_score' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function accessPoint(): BelongsTo
    {
        return $this->belongsTo(AccessPoint::class);
    }

    public function anomaly(): HasOne
    {
        return $this->hasOne(Anomaly::class);
    }

    /**
     * Détermine si le mouvement est une entrée.
     */
    public function isEntry(): bool
    {
        return $this->type === 'entry';
    }

    /**
     * Détermine si le mouvement est une sortie.
     */
    public function isExit(): bool
    {
        return $this->type === 'exit';
    }

    /**
     * Détermine si le mouvement est suspect.
     */
    public function isSuspicious(): bool
    {
        return $this->anomaly_score >= 50;
    }
}
