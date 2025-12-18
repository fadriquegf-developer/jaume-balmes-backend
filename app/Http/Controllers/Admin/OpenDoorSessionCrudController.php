<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class OpenDoorSessionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\OpenDoorSession::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/open-door-session');
        CRUD::setEntityNameStrings(__('open_doors.session'), __('open_doors.sessions'));
    }

    protected function setupListOperation()
    {
        CRUD::column('title')->label(__('open_doors.title'));
        CRUD::column('session_date')->label(__('open_doors.session_date'))->type('date');
        CRUD::column('start_time')->label(__('open_doors.start_time'))->type('time');
        CRUD::column('end_time')->label(__('open_doors.end_time'))->type('time');

        CRUD::addColumn([
            'name' => 'occupancy',
            'label' => __('open_doors.occupancy'),
            'type' => 'closure',
            'function' => function ($entry) {
                $percentage = $entry->capacity > 0
                    ? round(($entry->registered_count / $entry->capacity) * 100)
                    : 0;
                $color = $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                return "<span class='badge bg-{$color} text-white'>{$entry->registered_count}/{$entry->capacity} ({$percentage}%)</span>";
            },
            'escaped' => false,
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => __('open_doors.status'),
            'type' => 'closure',
            'function' => function ($entry) {
                $colors = [
                    'draft' => 'secondary',
                    'published' => 'success',
                    'cancelled' => 'danger',
                    'completed' => 'info',
                ];
                $labels = [
                    'draft' => __('open_doors.status_draft'),
                    'published' => __('open_doors.status_published'),
                    'cancelled' => __('open_doors.status_cancelled'),
                    'completed' => __('open_doors.status_completed'),
                ];
                return "<span class='badge bg-{$colors[$entry->status]} text-white'>{$labels[$entry->status]}</span>";
            },
            'escaped' => false,
        ]);

        // Filtres
        CRUD::addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => __('open_doors.status'),
        ], [
            'draft' => __('open_doors.status_draft'),
            'published' => __('open_doors.status_published'),
            'cancelled' => __('open_doors.status_cancelled'),
            'completed' => __('open_doors.status_completed'),
        ], function ($value) {
            CRUD::addClause('where', 'status', $value);
        });

        CRUD::orderBy('session_date', 'desc');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'title' => 'required|min:3|max:255',
            'session_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        CRUD::field('title')
            ->label(__('open_doors.title'))
            ->wrapper(['class' => 'form-group col-md-8']);

        CRUD::field('status')
            ->label(__('open_doors.status'))
            ->type('select_from_array')
            ->options([
                'draft' => __('open_doors.status_draft'),
                'published' => __('open_doors.status_published'),
                'cancelled' => __('open_doors.status_cancelled'),
                'completed' => __('open_doors.status_completed'),
            ])
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('description')
            ->label(__('open_doors.description'))
            ->type('textarea');

        CRUD::field('session_date')
            ->label(__('open_doors.session_date'))
            ->type('date')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('start_time')
            ->label(__('open_doors.start_time'))
            ->type('time')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('end_time')
            ->label(__('open_doors.end_time'))
            ->type('time')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('capacity')
            ->label(__('open_doors.capacity'))
            ->type('number')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('is_active')
            ->label(__('open_doors.is_active'))
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-6']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        CRUD::column('description')->label(__('open_doors.description'));
        CRUD::column('is_active')->label(__('open_doors.is_active'))->type('boolean');
        CRUD::column('created_at')->label(__('open_doors.created_at'));
    }
}
