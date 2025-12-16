<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('open_doors.cancelled_title') }}</title>
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
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container-box">
        <div class="icon">❌</div>
        <h1 class="title">{{ __('open_doors.cancelled_title') }}</h1>
        <p>{{ __('open_doors.cancelled_message') }}</p>
        <a href="{{ route('open-doors.form') }}" class="btn btn-primary mt-3">
            {{ __('open_doors.cancelled_new_registration') }}
        </a>
    </div>
</body>

</html>
