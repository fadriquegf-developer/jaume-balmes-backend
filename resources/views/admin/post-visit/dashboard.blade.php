@extends(backpack_view('blank'))

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">
                    <i class="la la-clipboard-check"></i> {{ __('post_visit_dashboard.title') }}
                </h2>
                <p class="text-muted">{{ __('post_visit_dashboard.subtitle') }}</p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row">
            {{-- Mitjana global --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-success mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-success">{{ $globalAverage }}/5</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('post_visit_dashboard.global_average') }}</div>
                            </div>
                            <div class="text-success">
                                <i class="la la-star la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Taxa resposta --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-info mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-info">{{ $stats['response_rate'] }}%</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('post_visit_dashboard.response_rate') }}</div>
                            </div>
                            <div class="text-info">
                                <i class="la la-chart-pie la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Completades --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-primary mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-primary">{{ $stats['completed'] }}</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('post_visit_dashboard.completed_surveys') }}</div>
                            </div>
                            <div class="text-primary">
                                <i class="la la-check-circle la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pendents --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card border-start border-start-4 border-start-warning mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fs-4 fw-semibold text-warning">{{ $stats['pending'] }}</div>
                                <div class="text-medium-emphasis small text-uppercase fw-semibold">
                                    {{ __('post_visit_dashboard.pending_surveys') }}</div>
                            </div>
                            <div class="text-warning">
                                <i class="la la-clock la-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gràfics de valoracions --}}
        <div class="row">
            {{-- Mitjanes per categoria --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-chart-bar"></i> {{ __('post_visit_dashboard.ratings_by_category') }}
                    </div>
                    <div class="card-body">
                        <canvas id="ratingsChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Distribució valoració general --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-star"></i> {{ __('post_visit_dashboard.rating_distribution') }}
                    </div>
                    <div class="card-body">
                        <canvas id="ratingDistributionChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Interès en matrícula --}}
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-graduation-cap"></i> {{ __('post_visit_dashboard.enrollment_interest') }}
                    </div>
                    <div class="card-body">
                        <canvas id="interestChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Dubtes resolts --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="la la-question-circle"></i> {{ __('post_visit_dashboard.doubts_resolved') }}
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <canvas id="doubtsChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $totalDoubts = array_sum($doubtsResolved);
                                    $resolvedPercent =
                                        $totalDoubts > 0
                                            ? round((($doubtsResolved[1] ?? 0) / $totalDoubts) * 100, 1)
                                            : 0;
                                @endphp
                                <div class="text-center">
                                    <div class="display-4 fw-bold text-success">{{ $resolvedPercent }}%</div>
                                    <div class="text-muted">{{ __('post_visit_dashboard.doubts_resolved_yes') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estadístiques per sessió --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="la la-calendar"></i> {{ __('post_visit_dashboard.stats_by_session') }}</span>
                        <a href="{{ backpack_url('post-visit-survey') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('post_visit_dashboard.view_all_surveys') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('post_visit_dashboard.session') }}</th>
                                        <th>{{ __('post_visit_dashboard.date') }}</th>
                                        <th class="text-center">{{ __('post_visit_dashboard.attended') }}</th>
                                        <th class="text-center">{{ __('post_visit_dashboard.surveys_sent') }}</th>
                                        <th class="text-center">{{ __('post_visit_dashboard.surveys_completed') }}</th>
                                        <th class="text-center">{{ __('post_visit_dashboard.response_rate') }}</th>
                                        <th class="text-center">{{ __('post_visit_dashboard.avg_rating') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sessionStats as $session)
                                        <tr>
                                            <td>{{ $session['title'] }}</td>
                                            <td>{{ $session['date'] }}</td>
                                            <td class="text-center">{{ $session['attended'] }}</td>
                                            <td class="text-center">{{ $session['surveys_sent'] }}</td>
                                            <td class="text-center">{{ $session['surveys_completed'] }}</td>
                                            <td class="text-center">
                                                @if ($session['response_rate'] >= 70)
                                                    <span class="badge bg-success text-white">{{ $session['response_rate'] }}%</span>
                                                @elseif ($session['response_rate'] >= 40)
                                                    <span class="badge bg-warning">{{ $session['response_rate'] }}%</span>
                                                @else
                                                    <span class="badge bg-danger text-white">{{ $session['response_rate'] }}%</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($session['avg_rating'])
                                                    <span class="text-warning">★</span> {{ $session['avg_rating'] }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('post_visit_dashboard.no_data') }}
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

        {{-- Últims comentaris --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="la la-thumbs-up me-1"></i> {{ __('post_visit_dashboard.recent_liked') }}
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentFeedback->filter(fn($s) => $s->liked_most)->take(5) as $survey)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <small
                                            class="text-muted">{{ $survey->registration->session->session_date->format('d/m/Y') }}</small>
                                        <span class="text-warning">{{ str_repeat('★', $survey->overall_rating) }}</span>
                                    </div>
                                    <p class="mb-0 mt-1">"{{ Str::limit($survey->liked_most, 150) }}"</p>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">
                                    {{ __('post_visit_dashboard.no_comments') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="la la-lightbulb me-1"></i> {{ __('post_visit_dashboard.recent_improvements') }}
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentFeedback->filter(fn($s) => $s->improvements)->take(5) as $survey)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <small
                                            class="text-muted">{{ $survey->registration->session->session_date->format('d/m/Y') }}</small>
                                        <span class="text-warning">{{ str_repeat('★', $survey->overall_rating) }}</span>
                                    </div>
                                    <p class="mb-0 mt-1">"{{ Str::limit($survey->improvements, 150) }}"</p>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">
                                    {{ __('post_visit_dashboard.no_comments') }}</li>
                            @endforelse
                        </ul>
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
            const averages = @json($averages);
            const ratingDistribution = @json($ratingDistribution);
            const interestDistribution = @json($interestDistribution);
            const doubtsResolved = @json($doubtsResolved);

            // Gràfic de mitjanes per categoria
            new Chart(document.getElementById('ratingsChart'), {
                type: 'bar',
                data: {
                    labels: [
                        '{{ __('post_visit.overall_rating') }}',
                        '{{ __('post_visit.information_rating') }}',
                        '{{ __('post_visit.attention_rating') }}',
                        '{{ __('post_visit.facilities_rating') }}'
                    ],
                    datasets: [{
                        label: '{{ __('post_visit_dashboard.average') }}',
                        data: [averages.overall, averages.information, averages.attention, averages
                            .facilities
                        ],
                        backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#6f42c1']
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
                            max: 5
                        }
                    }
                }
            });

            // Gràfic distribució valoracions
            new Chart(document.getElementById('ratingDistributionChart'), {
                type: 'bar',
                data: {
                    labels: ['1 ★', '2 ★', '3 ★', '4 ★', '5 ★'],
                    datasets: [{
                        label: '{{ __('post_visit_dashboard.responses') }}',
                        data: [
                            ratingDistribution[1] || 0,
                            ratingDistribution[2] || 0,
                            ratingDistribution[3] || 0,
                            ratingDistribution[4] || 0,
                            ratingDistribution[5] || 0
                        ],
                        backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754']
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

            // Gràfic interès
            const interestLabels = {
                'very_high': '{{ __('post_visit.interest_very_high') }}',
                'high': '{{ __('post_visit.interest_high') }}',
                'medium': '{{ __('post_visit.interest_medium') }}',
                'low': '{{ __('post_visit.interest_low') }}',
                'none': '{{ __('post_visit.interest_none') }}'
            };
            const interestOrder = ['very_high', 'high', 'medium', 'low', 'none'];

            new Chart(document.getElementById('interestChart'), {
                type: 'doughnut',
                data: {
                    labels: interestOrder.map(k => interestLabels[k]),
                    datasets: [{
                        data: interestOrder.map(k => interestDistribution[k] || 0),
                        backgroundColor: ['#198754', '#20c997', '#ffc107', '#fd7e14', '#dc3545']
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

            // Gràfic dubtes
            new Chart(document.getElementById('doubtsChart'), {
                type: 'pie',
                data: {
                    labels: ['{{ __('post_visit.yes') }}', '{{ __('post_visit.no') }}'],
                    datasets: [{
                        data: [doubtsResolved[1] || 0, doubtsResolved[0] || 0],
                        backgroundColor: ['#198754', '#dc3545']
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
