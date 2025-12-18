<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistència Confirmada - Centre d'Estudis Jaume Balmes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <span class="display-1 text-success">✓</span>
                        </div>
                        <h2 class="text-success mb-3">Assistència Confirmada!</h2>
                        <p class="text-muted mb-4">
                            Gràcies per confirmar la teva assistència a la jornada de Portes Obertes.
                        </p>
                        <div class="alert alert-info text-start">
                            <strong>Detalls de la visita:</strong><br>
                            <i class="la la-calendar"></i> Data:
                            {{ $registration->session->session_date->format('d/m/Y') }}<br>
                            <i class="la la-clock"></i> Hora: {{ substr($registration->session->start_time, 0, 5) }} -
                            {{ substr($registration->session->end_time, 0, 5) }}<br>
                            <i class="la la-user"></i> Alumne/a: {{ $registration->student_full_name }}
                        </div>
                        <p class="small text-muted">
                            Et recomanem arribar 10 minuts abans de l'hora d'inici.
                        </p>
                        <a href="https://www.jaumebalmes.com" class="btn btn-outline-primary mt-3">
                            Tornar a la web
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
