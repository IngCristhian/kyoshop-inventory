<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mt-5">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 6rem;"></i>
                    <h1 class="display-4 text-primary">404</h1>
                    <h2 class="text-muted">Página no encontrada</h2>
                    <p class="text-muted mb-4">
                        Lo sentimos, la página que buscas no existe o ha sido movida.
                    </p>
                    <a href="<?= APP_URL ?>" class="btn btn-primary">
                        <i class="bi bi-house"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>