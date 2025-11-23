<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayOfficial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'position_order',
        'email',
        'phone',
        'photo_url',
        'description',
        'is_active',
    ];

    protected $casts = [
        'position_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Check if official is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Scope to get only active officials
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position_order');
    }
}
