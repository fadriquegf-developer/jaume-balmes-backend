<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Mail\OpenDoorRegistrationConfirmation;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Mail;

class OpenDoorRegistration extends Model
{
    use CrudTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'open_door_session_id',
        'student_name',
        'student_surname',
        'student_birthdate',
        'current_school',
        'current_grade',
        'tutor_name',
        'tutor_surname',
        'tutor_email',
        'tutor_phone',
        'tutor_relationship',
        'interested_grades',
        'comments',
        'how_did_you_know',
        'status',
        'confirmation_token',
        'confirmed_at',
        'attended_at',
    ];

    protected $casts = [
        'student_birthdate' => 'date',
        'interested_grades' => 'array',
        'confirmed_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    // Auto-generar token al crear
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (empty($registration->confirmation_token)) {
                $registration->confirmation_token = \Illuminate\Support\Str::uuid();
            }
        });

        static::created(function ($registration) {
            // Incrementar comptador de la sessió
            $registration->session()->increment('registered_count');

            // Enviar email de confirmació
            \Illuminate\Support\Facades\Mail::to($registration->tutor_email)
                ->queue(new \App\Mail\OpenDoorRegistrationConfirmation($registration));
        });

        static::updating(function ($registration) {
            // Si canvia a 'cancelled', decrementar comptador
            if ($registration->isDirty('status') && $registration->status === 'cancelled') {
                $originalStatus = $registration->getOriginal('status');
                // Només decrementar si abans no estava cancel·lat
                if ($originalStatus !== 'cancelled') {
                    $registration->session()->decrement('registered_count');
                }
            }
        });
    }

    // Relaciones
    public function session()
    {
        return $this->belongsTo(OpenDoorSession::class, 'open_door_session_id');
    }

    public function postVisitSurvey(): HasOne
    {
        return $this->hasOne(PostVisitSurvey::class);
    }

    // Accessors
    public function getStudentFullNameAttribute(): string
    {
        return "{$this->student_name} {$this->student_surname}";
    }

    public function getTutorFullNameAttribute(): string
    {
        return "{$this->tutor_name} {$this->tutor_surname}";
    }

    // Métodos
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsAttended(): void
    {
        $this->update([
            'status' => 'attended',
            'attended_at' => now(),
        ]);
    }
}
