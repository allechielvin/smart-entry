<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mouvements effectués sur ce point d'accès.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }
}