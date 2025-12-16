@extends(backpack_view('blank'))

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">
                    <i class="la la-home"></i> {{ __('dashboard.general_title') }}
                </h2>
                <p class="text-muted">{{ __('dashboard.general_subtitle') }}</p>
            </div>
        </div>

        {{-- Alertes --}}
        @if (count($alerts) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    @foreach ($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} d-flex align-items-center" role="alert">
                            <i class="la {{ $alert['icon'] }} la-2x me-3"></i>
                            <div class="flex-grow-1">{{ $alert['message'] }}</div>
                            @if (isset($alert['link']))
                                <a href="{{ $alert['link'] }}" class="btn btn-sm btn-{{ $alert['type'] }}">
                                    {{ __('dashboard.view') }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="row">
            {{-- Inscripcions pendents --}}
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <div class="card border-start border-start-4 border-start-warning mb-4">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold text-warning">{{ $kpis['pending_registrations'] }}</div>
                        <div class="text-medium-emphasis small text-uppercase fw-semibold text-truncate">
                            {{ __('dashboard.kpi_pending_registrations') }}
                        </div>
                    </div>
                    <a href="{{ backpack_url('open-door-registration') }}?status=pending"
                        class="card-footer text-decoration-none text-muted small">
                        {{ __('dashboard.view_details') }} <i class="la la-arrow-right"></i>
                    </a>
                </div>
            </div>

            {{-- Sessions actives --}}
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <div class="card border-start border-start-4 border-start-success mb-4">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold text-success">{{ $kpis['upcoming_sessions'] }}</div>
                        <div class="text-medium-emphasis small text-uppercase fw-semibold text-truncate">
                            {{ __('dashboard.kpi_upcoming_sessions') }}
                        </div>
                    </div>
                    <a href="{{ backpack_url('open-door-session') }}"
                        class="card-footer text-decoration-none text-muted small">
                        {{ __('dashboard.view_details') }} <i class="la la-arrow-right"></i>
                    </a>
                </div>
            </div>

            {{-- Converses chatbot (placeholder) --}}
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <div class="card border-start border-start-4 border-start-info mb-4 opacity-50">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold text-info">{{ $kpis['chatbot_conversations_today'] }}</div>
                        <div class="text-medium-emphasis small text-uppercase fw-semibold text-truncate">
                            {{ __('dashboard.kpi_chatbot_today') }}
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="la la-clock"></i> {{ __('dashboard.coming_soon') }}
                    </div>
                </div>
            </div>

            {{-- Substitucions pendents (placeholder) --}}
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <div class="card border-start border-start-4 border-start-danger mb-4 opacity-50">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold text-danger">{{ $kpis['pending_substitutions'] }}</div>
                        <div class="text-medium-emphasis small text-uppercase fw-semibold text-truncate">
                            {{ __('dashboard.kpi_pending_substitutions') }}
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="la la-clock"></i> {{ __('dashboard.coming_soon') }}
                    </div>
                </div>
            </div>

            {{-- Enquestes mes (placeholder) --}}
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <div class="card border-start border-start-4 border-start-primary mb-4 opacity-50">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold text-primary">{{ $kpis['surveys_completed_month'] }}</div>
                        <div class="text-medium-emphasis small text-uppercase fw-semibold text-truncate">
                            {{ __('dashboard.kpi_surveys_month') }}
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="la la-clock"></i> {{ __('dashboard.coming_soon') }}
                    </div>
                </div>
            </div>

            {{-- Reclamacions pendents (placeholder) --}}
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <div class="card border-start border-start-4 border-start-secondary mb-4 opacity-50">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold text-secondary">{{ $kpis['pending_complaints'] }}</div>
                        <div class="text-medium-emphasis small text-uppercase fw-semibold text-truncate">
                            {{ __('dashboard.kpi_pending_complaints') }}
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="la la-clock"></i> {{ __('dashboard.coming_soon') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Accés ràpid als mòduls --}}
        <div class="row mt-4">
            <div class="col-12">
                <h5><i class="la la-th-large"></i> {{ __('dashboard.quick_access') }}</h5>
            </div>
        </div>

        <div class="row">
            {{-- Mòdul Inscripcions --}}
            <div class="col-md-6 col-lg-4 col-xl-2">
                <a href="{{ backpack_url('open-doors/dashboard') }}" class="text-decoration-none">
                    <div class="card text-center mb-4 h-100 hover-shadow">
                        <div class="card-body">
                            <i class="la la-door-open la-3x text-warning mb-2"></i>
                            <h6 class="card-title mb-0">{{ __('menu.inscriptions') }}</h6>
                            <small class="text-muted">{{ __('dashboard.module_active') }}</small>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Mòdul Chatbots --}}
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card text-center mb-4 h-100 opacity-50">
                    <div class="card-body">
                        <i class="la la-comments la-3x text-info mb-2"></i>
                        <h6 class="card-title mb-0">{{ __('menu.chatbots') }}</h6>
                        <small class="text-muted">{{ __('dashboard.coming_soon') }}</small>
                    </div>
                </div>
            </div>

            {{-- Mòdul Substitucions --}}
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card text-center mb-4 h-100 opacity-50">
                    <div class="card-body">
                        <i class="la la-user-clock la-3x text-danger mb-2"></i>
                        <h6 class="card-title mb-0">{{ __('menu.substitutions') }}</h6>
                        <small class="text-muted">{{ __('dashboard.coming_soon') }}</small>
                    </div>
                </div>
            </div>

            {{-- Mòdul Satisfacció --}}
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card text-center mb-4 h-100 opacity-50">
                    <div class="card-body">
                        <i class="la la-smile la-3x text-primary mb-2"></i>
                        <h6 class="card-title mb-0">{{ __('menu.satisfaction') }}</h6>
                        <small class="text-muted">{{ __('dashboard.coming_soon') }}</small>
                    </div>
                </div>
            </div>

            {{-- Mòdul Reclamacions --}}
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card text-center mb-4 h-100 opacity-50">
                    <div class="card-body">
                        <i class="la la-exclamation-triangle la-3x text-secondary mb-2"></i>
                        <h6 class="card-title mb-0">{{ __('menu.complaints') }}</h6>
                        <small class="text-muted">{{ __('dashboard.coming_soon') }}</small>
                    </div>
                </div>
            </div>

            {{-- Informes --}}
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card text-center mb-4 h-100 opacity-50">
                    <div class="card-body">
                        <i class="la la-file-pdf la-3x text-success mb-2"></i>
                        <h6 class="card-title mb-0">{{ __('menu.reports') }}</h6>
                        <small class="text-muted">{{ __('dashboard.coming_soon') }}</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
            transition: all 0.2s;
        }
    </style>
@endsection
