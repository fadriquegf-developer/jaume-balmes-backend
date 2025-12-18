<?php

namespace App\Http\Controllers;

use App\Models\PostVisitSurvey;
use Illuminate\Http\Request;

class PostVisitSurveyController extends Controller
{
    public function show(string $token)
    {
        $survey = PostVisitSurvey::where('survey_token', $token)
            ->with(['registration.session'])
            ->firstOrFail();

        // Verificar si ja està completada
        if ($survey->status === 'completed') {
            return view('public.post-visit.already-completed', compact('survey'));
        }

        // Verificar si ha expirat
        if ($survey->is_expired) {
            $survey->update(['status' => 'expired']);
            return view('public.post-visit.expired', compact('survey'));
        }

        return view('public.post-visit.survey', compact('survey'));
    }

    public function submit(Request $request, string $token)
    {
        $survey = PostVisitSurvey::where('survey_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        // Verificar expiració
        if ($survey->is_expired) {
            $survey->update(['status' => 'expired']);
            return redirect()->route('post-visit.survey', $token)
                ->with('error', __('post_visit.survey_expired'));
        }

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'information_rating' => 'required|integer|min:1|max:5',
            'attention_rating' => 'required|integer|min:1|max:5',
            'facilities_rating' => 'required|integer|min:1|max:5',
            'doubts_resolved' => 'required|boolean',
            'liked_most' => 'nullable|string|max:1000',
            'improvements' => 'nullable|string|max:1000',
            'enrollment_interest' => 'required|in:very_high,high,medium,low,none',
            'additional_comments' => 'nullable|string|max:1000',
        ]);

        $survey->update($validated);
        $survey->markAsCompleted();

        return redirect()->route('post-visit.thanks', $token);
    }

    public function thanks(string $token)
    {
        $survey = PostVisitSurvey::where('survey_token', $token)->firstOrFail();

        return view('public.post-visit.thanks', compact('survey'));
    }
}
