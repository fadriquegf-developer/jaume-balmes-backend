@extends(backpack_view('blank'))

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">
                    <i class="la la-tachometer-alt"></i> {{ __('dashboard.title') }}
                </h2>
                <p class="text-muted">{{ __('dashboard.subtitle') }}</p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row">
            {{-- Sessions actives --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-info mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-info">{{ $stats['active_sessions'] }}</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('dashboard.active_sessions') }}</div>
                            </div>
                            <div class="text-info">
                                <i class="la la-calendar la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inscripcions pendents --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-warning mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-warning">{{ $stats['pending_registrations'] }}</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('dashboard.pending_registrations') }}</div>
                            </div>
                            <div class="text-warning">
                                <i class="la la-clock la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inscripcions confirmades --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-success mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-success">{{ $stats['confirmed_registrations'] }}</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('dashboard.confirmed_registrations') }}</div>
                            </div>
                            <div class="text-success">
                                <i class="la la-check-circle la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Taxa conversió --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-primary mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-primary">{{ $conversionRate }}%</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('dashboard.conversion_rate') }}</div>
                            </div>
                            <div class="text-primary">
                                <i class="la la-chart-line la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Gràfic d'inscripcions --}}
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-chart-area"></i> {{ __('dashboard.registrations_last_30_days') }}
                    </div>
                    <div class="card-body">
                        <canvas id="registrationsChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            {{-- Distribució per estat --}}
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-pie-chart"></i> {{ __('dashboard.status_distribution') }}
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Properes sessions --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="la la-calendar-alt"></i> {{ __('dashboard.upcoming_sessions') }}</span>
                        <a href="{{ backpack_url('open-door-session') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('dashboard.date') }}</th>
                                        <th>{{ __('dashboard.session') }}</th>
                                        <th>{{ __('dashboard.occupancy') }}</th>
                                        <th>{{ __('dashboard.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($upcomingSessions as $session)
                                        <tr>
                                            <td>
                                                <strong>{{ $session->session_date->format('d/m') }}</strong><br>
                                                <small class="text-muted">{{ substr($session->start_time, 0, 5) }}</small>
                                            </td>
                                            <td>{{ $session->title }}</td>
                                            <td>
                                                @php
                                                    $percentage =
                                                        $session->capacity > 0
                                                            ? round(
                                                                ($session->registered_count / $session->capacity) * 100,
                                                            )
                                                            : 0;
                                                    $color =
                                                        $percentage >= 90
                                                            ? 'danger'
                                                            : ($percentage >= 70
                                                                ? 'warning'
                                                                : 'success');
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $color }}"
                                                        style="width: {{ $percentage }}%">
                                                        {{ $session->registered_count }}/{{ $session->capacity }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($session->is_full)
                                                    <span class="badge bg-danger text-white">{{ __('dashboard.full') }}</span>
                                                @else
                                                    <span class="badge bg-success text-white">{{ $session->available_spots }}
                                                        {{ __('dashboard.spots') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                {{ __('dashboard.no_upcoming_sessions') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Últimes inscripcions --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="la la-users"></i> {{ __('dashboard.latest_registrations') }}</span>
                        <a href="{{ backpack_url('open-door-registration') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('dashboard.student') }}</th>
                                        <th>{{ __('dashboard.session') }}</th>
                                        <th>{{ __('dashboard.status') }}</th>
                                        <th>{{ __('dashboard.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestRegistrations as $registration)
                                        <tr>
                                            <td>
                                                <strong>{{ $registration->student_full_name }}</strong><br>
                                                <small class="text-muted">{{ $registration->tutor_email }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $registration->session->session_date->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'attended' => 'success',
                                                        'no_show' => 'danger',
                                                        'cancelled' => 'secondary',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$registration->status] }} text-white">
                                                    {{ __('open_doors.reg_status_' . $registration->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $registration->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                {{ __('dashboard.no_registrations') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Interès per cicles --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-graduation-cap"></i> {{ __('dashboard.interest_by_grade') }}
                    </div>
                    <div class="card-body">
                        <canvas id="gradesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Fonts de captació --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-bullhorn"></i> {{ __('dashboard.acquisition_sources') }}
                    </div>
                    <div class="card-body">
                        <canvas id="sourcesChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dades
            const registrationsData = @json($registrationsPerDay);
            const gradesData = @json($registrationsByGrade);
            const sourcesData = @json($registrationsBySource);
            const statsData = @json($stats);

            // Gràfic d'inscripcions per dia
            const last30Days = [];
            const registrationsCounts = [];
            for (let i = 29; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                last30Days.push(date.toLocaleDateString('ca-ES', {
                    day: '2-digit',
                    month: '2-digit'
                }));
                registrationsCounts.push(registrationsData[dateStr] || 0);
            }

            new Chart(document.getElementById('registrationsChart'), {
                type: 'line',
                data: {
                    labels: last30Days,
                    datasets: [{
                        label: '{{ __('dashboard.registrations') }}',
                        data: registrationsCounts,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Gràfic d'estat
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{ __('open_doors.reg_status_pending') }}',
                        '{{ __('open_doors.reg_status_confirmed') }}',
                        '{{ __('open_doors.reg_status_attended') }}',
                        '{{ __('open_doors.reg_status_cancelled') }}'
                    ],
                    datasets: [{
                        data: [
                            statsData.pending_registrations,
                            statsData.confirmed_registrations,
                            statsData.attended_registrations,
                            statsData.cancelled_registrations
                        ],
                        backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#6c757d']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Gràfic de cicles
            new Chart(document.getElementById('gradesChart'), {
                type: 'bar',
                data: {
                    labels: [
                        '{{ __('open_doors.grade_eso') }}',
                        '{{ __('open_doors.grade_batxillerat') }}',
                        '{{ __('open_doors.grade_cfgm') }}',
                        '{{ __('open_doors.grade_cfgs') }}'
                    ],
                    datasets: [{
                        label: '{{ __('dashboard.interested') }}',
                        data: [gradesData.eso, gradesData.batxillerat, gradesData.cfgm, gradesData
                            .cfgs
                        ],
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Gràfic de fonts
            const sourceLabels = {
                'web': '{{ __('open_doors.source_web') }}',
                'social_media': '{{ __('open_doors.source_social_media') }}',
                'friends': '{{ __('open_doors.source_friends') }}',
                'school': '{{ __('open_doors.source_school') }}',
                'press': '{{ __('open_doors.source_press') }}',
                'other': '{{ __('open_doors.source_other') }}'
            };

            new Chart(document.getElementById('sourcesChart'), {
                type: 'pie',
                data: {
                    labels: Object.keys(sourcesData).map(k => sourceLabels[k] || k),
                    datasets: [{
                        data: Object.values(sourcesData),
                        backgroundColor: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545',
                            '#fd7e14'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endpush
