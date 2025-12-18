<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('post_visit.survey_title') }} - Centre d'Estudis Jaume Balmes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .survey-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .rating-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .rating-option {
            flex: 1;
            min-width: 50px;
        }

        .rating-option input {
            display: none;
        }

        .rating-option label {
            display: block;
            text-align: center;
            padding: 15px 10px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .rating-option label:hover {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }

        .rating-option input:checked+label {
            border-color: #0d6efd;
            background-color: #0d6efd;
            color: white;
        }

        .rating-number {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .rating-text {
            font-size: 0.7rem;
        }

        .interest-option label {
            padding: 10px 15px;
        }

        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="survey-container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="mb-1">{{ __('post_visit.survey_title') }}</h3>
                <p class="mb-0 opacity-75">Centre d'Estudis Jaume Balmes</p>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-info mb-4">
                    <i class="la la-info-circle"></i>
                    {{ __('post_visit.survey_intro') }}
                    <br><small class="text-muted">
                        {{ __('post_visit.survey_session') }}:
                        {{ $survey->registration->session->session_date->format('d/m/Y') }}
                    </small>
                </div>

                <form action="{{ route('post-visit.submit', $survey->survey_token) }}" method="POST">
                    @csrf

                    {{-- Valoracions --}}
                    <div class="section-title">
                        <i class="la la-star"></i> {{ __('post_visit.section_ratings') }}
                    </div>

                    {{-- Valoració general --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('post_visit.overall_rating') }} <span
                                class="text-danger">*</span></label>
                        <div class="rating-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="rating-option">
                                    <input type="radio" name="overall_rating" id="overall_{{ $i }}"
                                        value="{{ $i }}" {{ old('overall_rating') == $i ? 'checked' : '' }}
                                        required>
                                    <label for="overall_{{ $i }}">
                                        <div class="rating-number">{{ $i }}</div>
                                        <div class="rating-text">
                                            @if ($i == 1)
                                                {{ __('post_visit.rating_1') }}
                                            @elseif ($i == 2)
                                                {{ __('post_visit.rating_2') }}
                                            @elseif ($i == 3)
                                                {{ __('post_visit.rating_3') }}
                                            @elseif ($i == 4)
                                                {{ __('post_visit.rating_4') }}
                                            @else
                                                {{ __('post_visit.rating_5') }}
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        @error('overall_rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Valoració informació --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('post_visit.information_rating') }} <span
                                class="text-danger">*</span></label>
                        <div class="rating-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="rating-option">
                                    <input type="radio" name="information_rating" id="info_{{ $i }}"
                                        value="{{ $i }}"
                                        {{ old('information_rating') == $i ? 'checked' : '' }} required>
                                    <label for="info_{{ $i }}">
                                        <div class="rating-number">{{ $i }}</div>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        @error('information_rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Valoració atenció --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('post_visit.attention_rating') }} <span
                                class="text-danger">*</span></label>
                        <div class="rating-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="rating-option">
                                    <input type="radio" name="attention_rating" id="attention_{{ $i }}"
                                        value="{{ $i }}"
                                        {{ old('attention_rating') == $i ? 'checked' : '' }} required>
                                    <label for="attention_{{ $i }}">
                                        <div class="rating-number">{{ $i }}</div>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        @error('attention_rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Valoració instal·lacions --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('post_visit.facilities_rating') }} <span
                                class="text-danger">*</span></label>
                        <div class="rating-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="rating-option">
                                    <input type="radio" name="facilities_rating" id="facilities_{{ $i }}"
                                        value="{{ $i }}"
                                        {{ old('facilities_rating') == $i ? 'checked' : '' }} required>
                                    <label for="facilities_{{ $i }}">
                                        <div class="rating-number">{{ $i }}</div>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        @error('facilities_rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dubtes resolts --}}
                    <div class="section-title">
                        <i class="la la-question-circle"></i> {{ __('post_visit.section_feedback') }}
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('post_visit.doubts_resolved') }} <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="doubts_resolved" id="doubts_yes"
                                    value="1" {{ old('doubts_resolved') == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="doubts_yes">{{ __('post_visit.yes') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="doubts_resolved" id="doubts_no"
                                    value="0" {{ old('doubts_resolved') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="doubts_no">{{ __('post_visit.no') }}</label>
                            </div>
                        </div>
                        @error('doubts_resolved')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Què t'ha agradat més --}}
                    <div class="mb-4">
                        <label for="liked_most" class="form-label fw-bold">{{ __('post_visit.liked_most') }}</label>
                        <textarea class="form-control" id="liked_most" name="liked_most" rows="3"
                            placeholder="{{ __('post_visit.liked_most_placeholder') }}">{{ old('liked_most') }}</textarea>
                        @error('liked_most')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Què milloraries --}}
                    <div class="mb-4">
                        <label for="improvements"
                            class="form-label fw-bold">{{ __('post_visit.improvements') }}</label>
                        <textarea class="form-control" id="improvements" name="improvements" rows="3"
                            placeholder="{{ __('post_visit.improvements_placeholder') }}">{{ old('improvements') }}</textarea>
                        @error('improvements')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Interès en inscripció --}}
                    <div class="section-title">
                        <i class="la la-graduation-cap"></i> {{ __('post_visit.section_interest') }}
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('post_visit.enrollment_interest') }} <span
                                class="text-danger">*</span></label>
                        <div class="rating-group">
                            @foreach (['very_high' => __('post_visit.interest_very_high'), 'high' => __('post_visit.interest_high'), 'medium' => __('post_visit.interest_medium'), 'low' => __('post_visit.interest_low'), 'none' => __('post_visit.interest_none')] as $value => $label)
                                <div class="rating-option interest-option">
                                    <input type="radio" name="enrollment_interest"
                                        id="interest_{{ $value }}" value="{{ $value }}"
                                        {{ old('enrollment_interest') == $value ? 'checked' : '' }} required>
                                    <label for="interest_{{ $value }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('enrollment_interest')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Comentaris addicionals --}}
                    <div class="mb-4">
                        <label for="additional_comments"
                            class="form-label fw-bold">{{ __('post_visit.additional_comments') }}</label>
                        <textarea class="form-control" id="additional_comments" name="additional_comments" rows="3"
                            placeholder="{{ __('post_visit.additional_comments_placeholder') }}">{{ old('additional_comments') }}</textarea>
                        @error('additional_comments')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="la la-paper-plane"></i> {{ __('post_visit.submit_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
