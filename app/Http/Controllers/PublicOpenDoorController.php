<?php

namespace App\Http\Controllers;

use App\Models\OpenDoorSession;
use App\Models\OpenDoorRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicOpenDoorController extends Controller
{
    /**
     * Mostrar formulari d'inscripció
     */
    public function showForm()
    {
        $sessions = OpenDoorSession::available()
            ->orderBy('session_date')
            ->get();

        return view('public.open-doors.register', compact('sessions'));
    }

    /**
     * Processar inscripció (web tradicional)
     */
    public function submitForm(Request $request)
    {
        $validator = $this->validateRegistration($request);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $registration = $this->createRegistration($request);

        return redirect()
            ->route('open-doors.success')
            ->with('registration', $registration);
    }

    /**
     * API: Obtenir sessions disponibles (per JS/Moodle)
     */
    public function apiGetSessions()
    {
        $sessions = OpenDoorSession::available()
            ->orderBy('session_date')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'description' => $session->description,
                    'session_date' => $session->session_date->format('Y-m-d'),
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'available_spots' => $session->available_spots,
                    'is_full' => $session->is_full,
                ];
            });

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    /**
     * API: Processar inscripció (per JS/Moodle)
     */
    public function apiSubmit(Request $request)
    {
        $validator = $this->validateRegistration($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verificar que la sessió encara té places
        $session = OpenDoorSession::find($request->open_door_session_id);
        if (!$session || $session->is_full) {
            return response()->json([
                'success' => false,
                'errors' => ['open_door_session_id' => [__('open_doors.session_full')]],
            ], 422);
        }

        $registration = $this->createRegistration($request);

        return response()->json([
            'success' => true,
            'message' => __('open_doors.registration_success'),
            'data' => [
                'id' => $registration->id,
                'confirmation_token' => $registration->confirmation_token,
                'session' => $session->title,
                'date' => $session->session_date->format('d/m/Y'),
            ],
        ]);
    }

    /**
     * Pàgina d'èxit
     */
    public function success()
    {
        return view('public.open-doors.success');
    }

    /**
     * Validar dades d'inscripció
     */
    private function validateRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'open_door_session_id' => 'required|exists:open_door_sessions,id',
            'student_name' => 'required|max:255',
            'student_surname' => 'required|max:255',
            'student_birthdate' => 'nullable|date',
            'current_school' => 'nullable|max:255',
            'current_grade' => 'nullable|max:255',
            'tutor_name' => 'required|max:255',
            'tutor_surname' => 'required|max:255',
            'tutor_email' => 'required|email|max:255',
            'tutor_phone' => 'required|max:20',
            'tutor_relationship' => 'required|in:father,mother,tutor,other',
            'interested_grades' => 'nullable|array',
            'how_did_you_know' => 'nullable|max:255',
            'comments' => 'nullable|max:1000',
            'privacy_accepted' => 'accepted',
        ]);
    }

    /**
     * Crear registre d'inscripció
     */
    private function createRegistration(Request $request): OpenDoorRegistration
    {
        return OpenDoorRegistration::create([
            'open_door_session_id' => $request->open_door_session_id,
            'student_name' => $request->student_name,
            'student_surname' => $request->student_surname,
            'student_birthdate' => $request->student_birthdate,
            'current_school' => $request->current_school,
            'current_grade' => $request->current_grade,
            'tutor_name' => $request->tutor_name,
            'tutor_surname' => $request->tutor_surname,
            'tutor_email' => $request->tutor_email,
            'tutor_phone' => $request->tutor_phone,
            'tutor_relationship' => $request->tutor_relationship,
            'interested_grades' => $request->interested_grades,
            'how_did_you_know' => $request->how_did_you_know,
            'comments' => $request->comments,
        ]);
    }

    /**
     * Confirmar inscripció
     */
    public function confirm(string $token)
    {
        $registration = OpenDoorRegistration::where('confirmation_token', $token)->firstOrFail();

        if ($registration->status === 'pending') {
            $registration->confirm();
        }

        return view('public.open-doors.confirmed', compact('registration'));
    }

    /**
     * Cancel·lar inscripció
     */
    public function cancel(string $token)
    {
        $registration = OpenDoorRegistration::where('confirmation_token', $token)->firstOrFail();

        if (in_array($registration->status, ['pending', 'confirmed'])) {
            $registration->update(['status' => 'cancelled']);
            $registration->session->decrement('registered_count');
        }

        return view('public.open-doors.cancelled', compact('registration'));
    }
}
