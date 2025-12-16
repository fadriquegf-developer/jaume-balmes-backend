<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs globals (placeholder per als altres mòduls)
        $kpis = [
            // Mòdul 2: Inscripcions
            'pending_registrations' => OpenDoorRegistration::where('status', 'pending')->count(),
            'upcoming_sessions' => OpenDoorSession::available()->count(),

            // Mòdul 1: Chatbots (placeholder)
            'chatbot_conversations_today' => 0,

            // Mòdul 3: Substitucions (placeholder)
            'pending_substitutions' => 0,

            // Mòdul 4: Satisfacció (placeholder)
            'surveys_completed_month' => 0,

            // Mòdul 5: Reclamacions (placeholder)
            'pending_complaints' => 0,
        ];

        // Alertes
        $alerts = [];

        // Alertes Portes Obertes
        $sessionsWithoutConfirmations = OpenDoorSession::where('session_date', '<=', now()->addDays(7))
            ->where('session_date', '>=', now())
            ->where('status', 'published')
            ->whereHas('registrations', function ($q) {
                $q->where('status', 'pending');
            })
            ->count();

        if ($sessionsWithoutConfirmations > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'la-door-open',
                'message' => __('dashboard.alert_pending_confirmations', ['count' => $sessionsWithoutConfirmations]),
                'link' => backpack_url('open-door-registration') . '?status=pending',
            ];
        }

        return view('admin.dashboard', compact('kpis', 'alerts'));
    }
}
