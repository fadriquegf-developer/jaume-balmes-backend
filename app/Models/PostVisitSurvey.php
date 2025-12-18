<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PostVisitSurvey extends Model
{
    use CrudTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'open_door_registration_id',
        'survey_token',
        'status',
        'overall_rating',
        'information_rating',
        'attention_rating',
        'facilities_rating',
        'doubts_resolved',
        'liked_most',
        'improvements',
        'enrollment_interest',
        'additional_comments',
        'sent_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'doubts_resolved' => 'boolean',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($survey) {
            if (empty($survey->survey_token)) {
                $survey->survey_token = Str::uuid();
            }
            if (empty($survey->expires_at)) {
                $survey->expires_at = now()->addDays(14); // 2 setmanes per respondre
            }
        });
    }

    // Relacions
    public function registration(): BelongsTo
    {
        return $this->belongsTo(OpenDoorRegistration::class, 'open_door_registration_id');
    }

    // Accessors
    public function getAverageRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->information_rating,
            $this->attention_rating,
            $this->facilities_rating,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'pending' && $this->expires_at && $this->expires_at->isPast();
    }

    public function getStudentNameAttribute(): string
    {
        return $this->registration->student_full_name ?? '';
    }

    public function getSessionDateAttribute(): ?string
    {
        return $this->registration->session->session_date?->format('d/m/Y') ?? '';
    }

    // Mètodes
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'sent_at' => now(),
        ]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }
}
