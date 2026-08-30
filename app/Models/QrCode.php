<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'visitor_id',
        'token',
        'version',
        'is_active',
        'expires_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
            'version' => 'integer',
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

    /**
     * Vérifie si le QR code est utilisable.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (
            $this->expires_at !== null &&
            $this->expires_at->isPast()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Enregistre l'utilisation du QR code.
     */
    public function markAsUsed(): void
    {
        $this->update([
            'last_used_at' => now(),
        ]);
    }
}
