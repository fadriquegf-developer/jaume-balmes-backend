<div class="btn-group" role="group">
    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="la la-file-excel"></i> {{ __('open_doors.export') }}
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('open-door-registration.export') }}">
                <i class="la la-download"></i> {{ __('open_doors.export_all') }}
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="la la-filter"></i> {{ __('open_doors.export_filtered') }}
            </a>
        </li>
    </ul>
</div>

<!-- Modal d'exportació amb filtres -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('open-door-registration.export') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="la la-file-excel"></i> {{ __('open_doors.export_filtered') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('open_doors.session') }}</label>
                        <select name="open_door_session_id" class="form-select">
                            <option value="">{{ __('open_doors.all_sessions') }}</option>
                            @foreach (\App\Models\OpenDoorSession::orderBy('session_date', 'desc')->get() as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->session_date->format('d/m/Y') }} - {{ $session->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('open_doors.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('open_doors.all_statuses') }}</option>
                            <option value="pending">{{ __('open_doors.reg_status_pending') }}</option>
                            <option value="confirmed">{{ __('open_doors.reg_status_confirmed') }}</option>
                            <option value="attended">{{ __('open_doors.reg_status_attended') }}</option>
                            <option value="no_show">{{ __('open_doors.reg_status_no_show') }}</option>
                            <option value="cancelled">{{ __('open_doors.reg_status_cancelled') }}</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.date_from') }}</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('open_doors.date_to') }}</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('open_doors.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="la la-download"></i> {{ __('open_doors.download_excel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
