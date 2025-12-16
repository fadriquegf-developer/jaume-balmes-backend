<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenDoorSession extends Model
{
    use CrudTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'session_date',
        'start_time',
        'end_time',
        'capacity',
        'registered_count',
        'status',
        'is_active',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function registrations()
    {
        return $this->hasMany(OpenDoorRegistration::class);
    }

    // Accessors
    public function getAvailableSpotsAttribute(): int
    {
        return $this->capacity - $this->registered_count;
    }

    public function getIsFullAttribute(): bool
    {
        return $this->available_spots <= 0;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', now()->toDateString());
    }

    public function scopeAvailable($query)
    {
        return $query->published()
            ->upcoming()
            ->where('is_active', true)
            ->whereColumn('registered_count', '<', 'capacity');
    }
}
