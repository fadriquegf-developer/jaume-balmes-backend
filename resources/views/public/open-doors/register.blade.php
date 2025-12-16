<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('open_doors.registration_form_title') }} - Centre d'Estudis Jaume Balmes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .form-header h1 {
            color: #333;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .session-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .session-card:hover {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }

        .session-card.selected {
            border-color: var(--primary-color);
            background: #e7f1ff;
        }

        .session-card.full {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .session-date {
            font-weight: 600;
            color: #333;
        }

        .session-time {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .session-spots {
            font-size: 0.85rem;
        }

        .session-spots.warning {
            color: #ffc107;
        }

        .session-spots.danger {
            color: #dc3545;
        }

        .session-spots.success {
            color: #198754;
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
        }

        /* Estils per iframe/Moodle */
        body.embedded {
            background: transparent;
        }

        body.embedded .form-container {
            margin: 0;
            box-shadow: none;
            border-radius: 0;
        }
    </style>
</head>

<body class="{{ request()->has('embedded') ? 'embedded' : '' }}">
    <div class="form-container">
        <div class="form-header">
            <h1>{{ __('open_doors.registration_form_title') }}</h1>
            <p class="text-muted">{{ __('open_doors.registration_form_subtitle') }}</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($sessions->isEmpty())
            <div class="alert alert-info">
                {{ __('open_doors.no_sessions_available') }}
            </div>
        @else
            <form method="POST" action="{{ route('open-doors.submit') }}" id="registration-form">
                @csrf

                {{-- Selecció de sessió --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span>📅</span> {{ __('open_doors.select_session') }}
                    </div>

                    <input type="hidden" name="open_door_session_id" id="session_id"
                        value="{{ old('open_door_session_id') }}" required>

                    <div class="sessions-list">
                        @foreach ($sessions as $session)
                            <div class="session-card {{ $session->is_full ? 'full' : '' }} {{ old('open_door_session_id') == $session->id ? 'selected' : '' }}"
                                data-session-id="{{ $session->id }}" {{ $session->is_full ? 'data-full=true' : '' }}>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="session-date">{{ $session->session_date->format('d/m/Y') }} -
                                            {{ $session->title }}</div>
                                        <div class="session-time">{{ substr($session->start_time, 0, 5) }} -
                                            {{ substr($session->end_time, 0, 5) }}</div>
                                    </div>
                                    <div
                                        class="session-spots {{ $session->available_spots <= 5 ? ($session->is_full ? 'danger' : 'warning') : 'success' }}">
                                        @if ($session->is_full)
                                            {{ __('open_doors.session_full') }}
                                        @else
                                            {{ $session->available_spots }} {{ __('open_doors.spots_available') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Dades alumne --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span>🎓</span> {{ __('open_doors.student_data') }}
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.student_name') }} *</label>
                            <input type="text" name="student_name" class="form-control"
                                value="{{ old('student_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.student_surname') }} *</label>
                            <input type="text" name="student_surname" class="form-control"
                                value="{{ old('student_surname') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('open_doors.student_birthdate') }}</label>
                            <input type="date" name="student_birthdate" class="form-control"
                                value="{{ old('student_birthdate') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('open_doors.current_school') }}</label>
                            <input type="text" name="current_school" class="form-control"
                                value="{{ old('current_school') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('open_doors.current_grade') }}</label>
                            <input type="text" name="current_grade" class="form-control"
                                value="{{ old('current_grade') }}">
                        </div>
                    </div>
                </div>

                {{-- Dades tutor --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span>👨‍👩‍👧</span> {{ __('open_doors.tutor_data') }}
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.tutor_name') }} *</label>
                            <input type="text" name="tutor_name" class="form-control"
                                value="{{ old('tutor_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.tutor_surname') }} *</label>
                            <input type="text" name="tutor_surname" class="form-control"
                                value="{{ old('tutor_surname') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('open_doors.tutor_email') }} *</label>
                            <input type="email" name="tutor_email" class="form-control"
                                value="{{ old('tutor_email') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('open_doors.tutor_phone') }} *</label>
                            <input type="tel" name="tutor_phone" class="form-control"
                                value="{{ old('tutor_phone') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('open_doors.tutor_relationship') }} *</label>
                            <select name="tutor_relationship" class="form-select" required>
                                <option value="">{{ __('open_doors.select_option') }}</option>
                                <option value="father" {{ old('tutor_relationship') == 'father' ? 'selected' : '' }}>
                                    {{ __('open_doors.relationship_father') }}</option>
                                <option value="mother" {{ old('tutor_relationship') == 'mother' ? 'selected' : '' }}>
                                    {{ __('open_doors.relationship_mother') }}</option>
                                <option value="tutor" {{ old('tutor_relationship') == 'tutor' ? 'selected' : '' }}>
                                    {{ __('open_doors.relationship_tutor') }}</option>
                                <option value="other" {{ old('tutor_relationship') == 'other' ? 'selected' : '' }}>
                                    {{ __('open_doors.relationship_other') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Interessos --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span>📚</span> {{ __('open_doors.interests') }}
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.interested_grades') }}</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="interested_grades[]"
                                    value="eso" id="grade_eso">
                                <label class="form-check-label"
                                    for="grade_eso">{{ __('open_doors.grade_eso') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="interested_grades[]"
                                    value="batxillerat" id="grade_batx">
                                <label class="form-check-label"
                                    for="grade_batx">{{ __('open_doors.grade_batxillerat') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="interested_grades[]"
                                    value="cfgm" id="grade_cfgm">
                                <label class="form-check-label"
                                    for="grade_cfgm">{{ __('open_doors.grade_cfgm') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="interested_grades[]"
                                    value="cfgs" id="grade_cfgs">
                                <label class="form-check-label"
                                    for="grade_cfgs">{{ __('open_doors.grade_cfgs') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.how_did_you_know') }}</label>
                            <select name="how_did_you_know" class="form-select">
                                <option value="">{{ __('open_doors.select_option') }}</option>
                                <option value="web" {{ old('how_did_you_know') == 'web' ? 'selected' : '' }}>
                                    {{ __('open_doors.source_web') }}</option>
                                <option value="social_media"
                                    {{ old('how_did_you_know') == 'social_media' ? 'selected' : '' }}>
                                    {{ __('open_doors.source_social_media') }}</option>
                                <option value="friends" {{ old('how_did_you_know') == 'friends' ? 'selected' : '' }}>
                                    {{ __('open_doors.source_friends') }}</option>
                                <option value="school" {{ old('how_did_you_know') == 'school' ? 'selected' : '' }}>
                                    {{ __('open_doors.source_school') }}</option>
                                <option value="press" {{ old('how_did_you_know') == 'press' ? 'selected' : '' }}>
                                    {{ __('open_doors.source_press') }}</option>
                                <option value="other" {{ old('how_did_you_know') == 'other' ? 'selected' : '' }}>
                                    {{ __('open_doors.source_other') }}</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('open_doors.comments') }}</label>
                            <textarea name="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Privacitat --}}
                <div class="form-section">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="privacy_accepted" id="privacy"
                            required {{ old('privacy_accepted') ? 'checked' : '' }}>
                        <label class="form-check-label" for="privacy">
                            {{ __('open_doors.privacy_text') }} *
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-submit">
                    {{ __('open_doors.submit_registration') }}
                </button>
            </form>
        @endif
    </div>

    <script>
        document.querySelectorAll('.session-card:not([data-full])').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.session-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('session_id').value = this.dataset.sessionId;
            });
        });
    </script>
</body>

</html>
