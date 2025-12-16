<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('open_doors.confirmed_title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .container-box {
            max-width: 600px;
            margin: 4rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .title {
            color: #198754;
        }
    </style>
</head>

<body>
    <div class="container-box">
        <div class="icon">✅</div>
        <h1 class="title">{{ __('open_doors.confirmed_title') }}</h1>
        <p>{{ __('open_doors.confirmed_message') }}</p>

        <div class="alert alert-light mt-4">
            <strong>📅 {{ $registration->session->session_date->format('d/m/Y') }}</strong><br>
            🕐 {{ substr($registration->session->start_time, 0, 5) }} -
            {{ substr($registration->session->end_time, 0, 5) }}<br>
            👤 {{ $registration->student_full_name }}
        </div>

        <p class="text-muted mt-3">{{ __('open_doors.confirmed_see_you') }}</p>
    </div>
</body>

</html>
