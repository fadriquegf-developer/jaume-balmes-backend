<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use Illuminate\Support\Facades\DB;

class OpenDoorsDashboardController extends Controller
{
    public function index()
    {
        // Estadístiques generals
        $stats = [
            'total_sessions' => OpenDoorSession::count(),
            'active_sessions' => OpenDoorSession::available()->count(),
            'total_registrations' => OpenDoorRegistration::count(),
            'pending_registrations' => OpenDoorRegistration::where('status', 'pending')->count(),
            'confirmed_registrations' => OpenDoorRegistration::where('status', 'confirmed')->count(),
            'attended_registrations' => OpenDoorRegistration::where('status', 'attended')->count(),
            'cancelled_registrations' => OpenDoorRegistration::where('status', 'cancelled')->count(),
        ];

        // Properes sessions
        $upcomingSessions = OpenDoorSession::with(['registrations' => function ($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            }])
            ->where('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->take(5)
            ->get();

        // Últimes inscripcions
        $latestRegistrations = OpenDoorRegistration::with('session')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Inscripcions per dia (últims 30 dies)
        $registrationsPerDay = OpenDoorRegistration::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Inscripcions per cicle d'interès
        $registrationsByGrade = $this->getRegistrationsByGrade();

        // Inscripcions per font (com ens han conegut)
        $registrationsBySource = OpenDoorRegistration::select('how_did_you_know', DB::raw('COUNT(*) as total'))
            ->whereNotNull('how_did_you_know')
            ->groupBy('how_did_you_know')
            ->pluck('total', 'how_did_you_know')
            ->toArray();

        // Taxa de conversió (confirmats/total)
        $conversionRate = $stats['total_registrations'] > 0
            ? round(($stats['confirmed_registrations'] + $stats['attended_registrations']) / $stats['total_registrations'] * 100, 1)
            : 0;

        // Taxa d'assistència (assistits/confirmats)
        $attendanceRate = ($stats['confirmed_registrations'] + $stats['attended_registrations']) > 0
            ? round($stats['attended_registrations'] / ($stats['confirmed_registrations'] + $stats['attended_registrations']) * 100, 1)
            : 0;

        return view('admin.open-doors.dashboard', compact(
            'stats',
            'upcomingSessions',
            'latestRegistrations',
            'registrationsPerDay',
            'registrationsByGrade',
            'registrationsBySource',
            'conversionRate',
            'attendanceRate'
        ));
    }

    private function getRegistrationsByGrade(): array
    {
        $grades = [
            'eso' => 0,
            'batxillerat' => 0,
            'cfgm' => 0,
            'cfgs' => 0,
        ];

        $registrations = OpenDoorRegistration::whereNotNull('interested_grades')->get();

        foreach ($registrations as $registration) {
            if (is_array($registration->interested_grades)) {
                foreach ($registration->interested_grades as $grade) {
                    if (isset($grades[$grade])) {
                        $grades[$grade]++;
                    }
                }
            }
        }

        return $grades;
    }
}