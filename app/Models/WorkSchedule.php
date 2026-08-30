<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'day_of_week',
        'expected_entry',
        'expected_exit',
        'entry_tolerance_minutes',
        'exit_tolerance_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'entry_tolerance_minutes' => 'integer',
            'exit_tolerance_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Vérifie si cet horaire concerne aujourd'hui.
     */
    public function isToday(): bool
    {
        return $this->day_of_week === now()->dayOfWeekIso;
    }
}
