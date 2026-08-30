<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anomaly extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'visitor_id',
        'movement_id',
        'type',
        'title',
        'description',
        'severity',
        'score',
        'status',
        'detected_at',
        'resolved_at',
        'resolved_by',
        'resolution_note',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
            'score' => 'integer',
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

    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'resolved_by'
        );
    }

    /**
     * Vérifie si l'anomalie est critique.
     */
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Vérifie si l'anomalie est encore active.
     */
    public function isActive(): bool
    {
        return in_array(
            $this->status,
            ['new', 'reviewing']
        );
    }
}