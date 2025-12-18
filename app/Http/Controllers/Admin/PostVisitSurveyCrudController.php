<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\SendSurveyOperation;
use App\Models\PostVisitSurvey;
use App\Models\OpenDoorRegistration;
use App\Mail\PostVisitSurveyInvitation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;

class PostVisitSurveyCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(PostVisitSurvey::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/post-visit-survey');
        CRUD::setEntityNameStrings(
            __('post_visit.survey'),
            __('post_visit.surveys')
        );

        // Botó per enviar enquestes
        CRUD::addButtonFromView('top', 'send_surveys', 'send_surveys_button', 'beginning');
    }

    protected function setupListOperation()
    {
        // Filtres
        CRUD::addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => __('post_visit.status'),
        ], [
            'pending' => __('post_visit.status_pending'),
            'completed' => __('post_visit.status_completed'),
            'expired' => __('post_visit.status_expired'),
        ], function ($value) {
            CRUD::addClause('where', 'status', $value);
        });

        CRUD::addFilter([
            'name' => 'session',
            'type' => 'select2',
            'label' => __('open_doors.session'),
        ], function () {
            return \App\Models\OpenDoorSession::pluck('title', 'id')->toArray();
        }, function ($value) {
            CRUD::addClause('whereHas', 'registration', function ($query) use ($value) {
                $query->where('open_door_session_id', $value);
            });
        });

        // Columnes
        CRUD::addColumn([
            'name' => 'registration',
            'label' => __('open_doors.registration'),
            'type' => 'relationship',
            'attribute' => 'student_full_name',
            'wrapper' => [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('open-door-registration/' . $entry->open_door_registration_id . '/show');
                },
            ],
        ]);

        CRUD::addColumn([
            'name' => 'session_date',
            'label' => __('open_doors.session_date'),
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => __('post_visit.status'),
            'type' => 'custom_html',
            'value' => function ($entry) {
                $colors = [
                    'pending' => 'warning',
                    'completed' => 'success',
                    'expired' => 'secondary',
                ];
                $color = $colors[$entry->status] ?? 'secondary';
                $label = __('post_visit.status_' . $entry->status);
                return "<span class='badge bg-{$color} text-white'>{$label}</span>";
            },
        ]);

        CRUD::addColumn([
            'name' => 'average_rating',
            'label' => __('post_visit.average_rating'),
            'type' => 'custom_html',
            'value' => function ($entry) {
                if (!$entry->average_rating) return '-';
                $stars = str_repeat('★', round($entry->average_rating));
                $empty = str_repeat('☆', 5 - round($entry->average_rating));
                return "<span class='text-warning'>{$stars}{$empty}</span> ({$entry->average_rating})";
            },
        ]);

        CRUD::addColumn([
            'name' => 'enrollment_interest',
            'label' => __('post_visit.enrollment_interest'),
            'type' => 'custom_html',
            'value' => function ($entry) {
                if (!$entry->enrollment_interest) return '-';
                $colors = [
                    'very_high' => 'success',
                    'high' => 'info',
                    'medium' => 'warning',
                    'low' => 'secondary',
                    'none' => 'danger',
                ];
                $color = $colors[$entry->enrollment_interest] ?? 'secondary';
                $label = __('post_visit.interest_' . $entry->enrollment_interest);
                return "<span class='badge bg-{$color} text-white'>{$label}</span>";
            },
        ]);

        CRUD::addColumn([
            'name' => 'sent_at',
            'label' => __('post_visit.sent_at'),
            'type' => 'datetime',
        ]);

        CRUD::addColumn([
            'name' => 'completed_at',
            'label' => __('post_visit.completed_at'),
            'type' => 'datetime',
        ]);

        CRUD::orderBy('created_at', 'desc');
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        // Afegir camps de resposta
        CRUD::addColumn([
            'name' => 'overall_rating',
            'label' => __('post_visit.overall_rating'),
        ]);
        CRUD::addColumn([
            'name' => 'information_rating',
            'label' => __('post_visit.information_rating'),
        ]);
        CRUD::addColumn([
            'name' => 'attention_rating',
            'label' => __('post_visit.attention_rating'),
        ]);
        CRUD::addColumn([
            'name' => 'facilities_rating',
            'label' => __('post_visit.facilities_rating'),
        ]);
        CRUD::addColumn([
            'name' => 'doubts_resolved',
            'label' => __('post_visit.doubts_resolved'),
            'type' => 'boolean',
        ]);
        CRUD::addColumn([
            'name' => 'liked_most',
            'label' => __('post_visit.liked_most'),
            'type' => 'textarea',
        ]);
        CRUD::addColumn([
            'name' => 'improvements',
            'label' => __('post_visit.improvements'),
            'type' => 'textarea',
        ]);
        CRUD::addColumn([
            'name' => 'additional_comments',
            'label' => __('post_visit.additional_comments'),
            'type' => 'textarea',
        ]);
    }

    /**
     * Enviar enquestes a assistents que encara no en tenen
     */
    public function sendSurveys()
    {
        // Buscar inscripcions amb status 'attended' que no tenen enquesta
        $registrations = OpenDoorRegistration::where('status', 'attended')
            ->whereDoesntHave('postVisitSurvey')
            ->with('session')
            ->get();

        $count = 0;
        foreach ($registrations as $registration) {
            // Crear enquesta
            $survey = PostVisitSurvey::create([
                'open_door_registration_id' => $registration->id,
            ]);

            // Enviar email
            Mail::to($registration->tutor_email)->send(new PostVisitSurveyInvitation($survey));
            $survey->markAsSent();

            $count++;
        }

        return redirect()->back()->with('success', "S'han enviat {$count} enquestes.");
    }
}
