<?php

namespace App\Http\Controllers\Admin;

use App\Models\OpenDoorSession;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class OpenDoorRegistrationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\OpenDoorRegistration::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/open-door-registration');
        CRUD::setEntityNameStrings(__('open_doors.registration'), __('open_doors.registrations'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'session',
            'label' => __('open_doors.session'),
            'type' => 'relationship',
            'attribute' => 'title',
        ]);

        CRUD::addColumn([
            'name' => 'student_full_name',
            'label' => __('open_doors.student_data'),
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'tutor_full_name',
            'label' => __('open_doors.tutor_data'),
            'type' => 'text',
        ]);

        CRUD::column('tutor_email')->label(__('open_doors.tutor_email'));
        CRUD::column('tutor_phone')->label(__('open_doors.tutor_phone'));

        CRUD::addColumn([
            'name' => 'status',
            'label' => __('open_doors.status'),
            'type' => 'closure',
            'function' => function ($entry) {
                $colors = [
                    'pending' => 'warning',
                    'confirmed' => 'info',
                    'attended' => 'success',
                    'no_show' => 'danger',
                    'cancelled' => 'secondary',
                ];
                $labels = [
                    'pending' => __('open_doors.reg_status_pending'),
                    'confirmed' => __('open_doors.reg_status_confirmed'),
                    'attended' => __('open_doors.reg_status_attended'),
                    'no_show' => __('open_doors.reg_status_no_show'),
                    'cancelled' => __('open_doors.reg_status_cancelled'),
                ];
                return "<span class='badge bg-{$colors[$entry->status]}'>{$labels[$entry->status]}</span>";
            },
            'escaped' => false,
        ]);

        CRUD::column('created_at')->label(__('open_doors.registration_date'))->type('datetime');

        // Filtres
        CRUD::addFilter([
            'name' => 'open_door_session_id',
            'type' => 'select2',
            'label' => __('open_doors.session'),
        ], function () {
            return OpenDoorSession::pluck('title', 'id')->toArray();
        }, function ($value) {
            CRUD::addClause('where', 'open_door_session_id', $value);
        });

        CRUD::addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => __('open_doors.status'),
        ], [
            'pending' => __('open_doors.reg_status_pending'),
            'confirmed' => __('open_doors.reg_status_confirmed'),
            'attended' => __('open_doors.reg_status_attended'),
            'no_show' => __('open_doors.reg_status_no_show'),
            'cancelled' => __('open_doors.reg_status_cancelled'),
        ], function ($value) {
            CRUD::addClause('where', 'status', $value);
        });

        CRUD::orderBy('created_at', 'desc');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'open_door_session_id' => 'required|exists:open_door_sessions,id',
            'student_name' => 'required|max:255',
            'student_surname' => 'required|max:255',
            'tutor_name' => 'required|max:255',
            'tutor_surname' => 'required|max:255',
            'tutor_email' => 'required|email|max:255',
            'tutor_phone' => 'required|max:20',
        ]);

        // Sessió
        CRUD::addField([
            'name' => 'open_door_session_id',
            'label' => __('open_doors.session'),
            'type' => 'select2',
            'entity' => 'session',
            'attribute' => 'title',
            'model' => OpenDoorSession::class,
            'options' => (function ($query) {
                return $query->where('status', 'published')
                    ->where('is_active', true)
                    ->orderBy('session_date')
                    ->get();
            }),
        ]);

        // Separador alumne
        CRUD::addField([
            'name' => 'separator_student',
            'type' => 'custom_html',
            'value' => '<h5 class="mt-4 mb-3"><i class="la la-user-graduate"></i> ' . __('open_doors.student_data') . '</h5><hr>',
        ]);

        CRUD::field('student_name')
            ->label(__('open_doors.student_name'))
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('student_surname')
            ->label(__('open_doors.student_surname'))
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('student_birthdate')
            ->label(__('open_doors.student_birthdate'))
            ->type('date')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('current_school')
            ->label(__('open_doors.current_school'))
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('current_grade')
            ->label(__('open_doors.current_grade'))
            ->wrapper(['class' => 'form-group col-md-4']);

        // Separador tutor
        CRUD::addField([
            'name' => 'separator_tutor',
            'type' => 'custom_html',
            'value' => '<h5 class="mt-4 mb-3"><i class="la la-users"></i> ' . __('open_doors.tutor_data') . '</h5><hr>',
        ]);

        CRUD::field('tutor_name')
            ->label(__('open_doors.tutor_name'))
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('tutor_surname')
            ->label(__('open_doors.tutor_surname'))
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('tutor_email')
            ->label(__('open_doors.tutor_email'))
            ->type('email')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('tutor_phone')
            ->label(__('open_doors.tutor_phone'))
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::addField([
            'name' => 'tutor_relationship',
            'label' => __('open_doors.tutor_relationship'),
            'type' => 'select_from_array',
            'options' => [
                'father' => __('open_doors.relationship_father'),
                'mother' => __('open_doors.relationship_mother'),
                'tutor' => __('open_doors.relationship_tutor'),
                'other' => __('open_doors.relationship_other'),
            ],
            'wrapper' => ['class' => 'form-group col-md-4'],
        ]);

        // Separador interessos
        CRUD::addField([
            'name' => 'separator_interests',
            'type' => 'custom_html',
            'value' => '<h5 class="mt-4 mb-3"><i class="la la-graduation-cap"></i> ' . __('open_doors.interests') . '</h5><hr>',
        ]);

        CRUD::addField([
            'name' => 'interested_grades',
            'label' => __('open_doors.interested_grades'),
            'type' => 'select2_from_array',
            'options' => [
                'eso' => __('open_doors.grade_eso'),
                'batxillerat' => __('open_doors.grade_batxillerat'),
                'cfgm' => __('open_doors.grade_cfgm'),
                'cfgs' => __('open_doors.grade_cfgs'),
            ],
            'allows_multiple' => true,
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::addField([
            'name' => 'how_did_you_know',
            'label' => __('open_doors.how_did_you_know'),
            'type' => 'select_from_array',
            'options' => [
                'web' => __('open_doors.source_web'),
                'social_media' => __('open_doors.source_social_media'),
                'friends' => __('open_doors.source_friends'),
                'school' => __('open_doors.source_school'),
                'press' => __('open_doors.source_press'),
                'other' => __('open_doors.source_other'),
            ],
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::field('comments')
            ->label(__('open_doors.comments'))
            ->type('textarea');

        // Estat
        CRUD::addField([
            'name' => 'status',
            'label' => __('open_doors.status'),
            'type' => 'select_from_array',
            'options' => [
                'pending' => __('open_doors.reg_status_pending'),
                'confirmed' => __('open_doors.reg_status_confirmed'),
                'attended' => __('open_doors.reg_status_attended'),
                'no_show' => __('open_doors.reg_status_no_show'),
                'cancelled' => __('open_doors.reg_status_cancelled'),
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        CRUD::column('student_birthdate')->label(__('open_doors.student_birthdate'));
        CRUD::column('current_school')->label(__('open_doors.current_school'));
        CRUD::column('current_grade')->label(__('open_doors.current_grade'));
        CRUD::column('interested_grades')->label(__('open_doors.interested_grades'))->type('array');
        CRUD::column('how_did_you_know')->label(__('open_doors.how_did_you_know'));
        CRUD::column('comments')->label(__('open_doors.comments'));
        CRUD::column('confirmed_at')->label(__('open_doors.confirmed_at'));
        CRUD::column('attended_at')->label(__('open_doors.attended_at'));
    }
}
