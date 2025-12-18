<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostVisitSurvey;
use App\Models\OpenDoorSession;
use Illuminate\Support\Facades\DB;

class PostVisitSurveyDashboardController extends Controller
{
    public function index()
    {
        // Estadístiques generals
        $stats = [
            'total_surveys' => PostVisitSurvey::count(),
            'completed' => PostVisitSurvey::where('status', 'completed')->count(),
            'pending' => PostVisitSurvey::where('status', 'pending')->count(),
            'expired' => PostVisitSurvey::where('status', 'expired')->count(),
        ];

        // Taxa de resposta
        $stats['response_rate'] = $stats['total_surveys'] > 0
            ? round(($stats['completed'] / $stats['total_surveys']) * 100, 1)
            : 0;

        // Mitjanes de valoració
        $averages = PostVisitSurvey::where('status', 'completed')
            ->selectRaw('
                ROUND(AVG(overall_rating), 2) as overall,
                ROUND(AVG(information_rating), 2) as information,
                ROUND(AVG(attention_rating), 2) as attention,
                ROUND(AVG(facilities_rating), 2) as facilities
            ')
            ->first();

        // Mitjana global
        $globalAverage = $averages->overall ? round(
            ($averages->overall + $averages->information + $averages->attention + $averages->facilities) / 4,
            2
        ) : 0;

        // Distribució d'interès
        $interestDistribution = PostVisitSurvey::where('status', 'completed')
            ->whereNotNull('enrollment_interest')
            ->select('enrollment_interest', DB::raw('COUNT(*) as total'))
            ->groupBy('enrollment_interest')
            ->pluck('total', 'enrollment_interest')
            ->toArray();

        // Dubtes resolts
        $doubtsResolved = PostVisitSurvey::where('status', 'completed')
            ->whereNotNull('doubts_resolved')
            ->select('doubts_resolved', DB::raw('COUNT(*) as total'))
            ->groupBy('doubts_resolved')
            ->pluck('total', 'doubts_resolved')
            ->toArray();

        // Distribució de valoracions (per gràfic de barres)
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = PostVisitSurvey::where('status', 'completed')
                ->where('overall_rating', $i)
                ->count();
        }

        // Estadístiques per sessió
        $sessionStats = OpenDoorSession::where('status', 'completed')
            ->withCount(['registrations as attended_count' => function ($query) {
                $query->where('status', 'attended');
            }])
            ->with(['registrations' => function ($query) {
                $query->where('status', 'attended')->with('postVisitSurvey');
            }])
            ->orderBy('session_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($session) {
                $surveys = $session->registrations->pluck('postVisitSurvey')->filter();
                $completed = $surveys->where('status', 'completed');

                return [
                    'title' => $session->title,
                    'date' => $session->session_date->format('d/m/Y'),
                    'attended' => $session->attended_count,
                    'surveys_sent' => $surveys->count(),
                    'surveys_completed' => $completed->count(),
                    'response_rate' => $surveys->count() > 0
                        ? round(($completed->count() / $surveys->count()) * 100, 1)
                        : 0,
                    'avg_rating' => $completed->avg('overall_rating')
                        ? round($completed->avg('overall_rating'), 1)
                        : null,
                ];
            });

        // Últims comentaris (liked_most i improvements)
        $recentFeedback = PostVisitSurvey::where('status', 'completed')
            ->where(function ($query) {
                $query->whereNotNull('liked_most')
                    ->orWhereNotNull('improvements');
            })
            ->with('registration.session')
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Evolució mensual
        $monthlyStats = PostVisitSurvey::where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(completed_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('ROUND(AVG(overall_rating), 2) as avg_rating')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        return view('admin.post-visit.dashboard', compact(
            'stats',
            'averages',
            'globalAverage',
            'interestDistribution',
            'doubtsResolved',
            'ratingDistribution',
            'sessionStats',
            'recentFeedback',
            'monthlyStats'
        ));
    }
}
