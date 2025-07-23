<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
    
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .badge {
            border-radius: 15px;
        }
        .alert {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white fw-bold">
                        <i class="bi bi-shop"></i> KyoShop
                    </h4>
                    <small class="text-white-50">Sistema de Inventario</small>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false || $_SERVER['REQUEST_URI'] === '/kyoshop-inventory/' || $_SERVER['REQUEST_URI'] === '/kyoshop-inventory') ? 'active' : '' ?>" href="<?= APP_URL ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'productos') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/productos">
                            <i class="bi bi-box-seam"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/productos?stock_bajo=1">
                            <i class="bi bi-exclamation-triangle"></i> Stock Bajo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/productos/crear">
                            <i class="bi bi-plus-circle"></i> Nuevo Producto
                        </a>
                    </li>
                </ul>
                
                <hr class="text-white-50 my-4">
                
                <div class="text-center">
                    <small class="text-white-50">
                        <?= APP_NAME ?> v<?= APP_VERSION ?>
                    </small>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2 text-dark">
                        <?php
                        $rutaActual = $_SERVER['REQUEST_URI'];
                        if (strpos($rutaActual, 'dashboard') !== false || $rutaActual === '/kyoshop-inventory/' || $rutaActual === '/kyoshop-inventory') {
                            echo '<i class="bi bi-speedometer2"></i> Dashboard';
                        } elseif (strpos($rutaActual, 'productos/crear') !== false) {
                            echo '<i class="bi bi-plus-circle"></i> Nuevo Producto';
                        } elseif (strpos($rutaActual, 'productos/editar') !== false) {
                            echo '<i class="bi bi-pencil-square"></i> Editar Producto';
                        } elseif (strpos($rutaActual, 'productos') !== false) {
                            echo '<i class="bi bi-box-seam"></i> Productos';
                        }
                        ?>
                    </h1>
                    
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?php if (strpos($_SERVER['REQUEST_URI'], 'productos') !== false && strpos($_SERVER['REQUEST_URI'], 'crear') === false && strpos($_SERVER['REQUEST_URI'], 'editar') === false): ?>
                            <a href="<?= APP_URL ?>/productos/crear" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Nuevo Producto
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <?php if (isset($flash)): ?>
                    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-<?= $flash['type'] === 'error' ? 'exclamation-triangle' : 'check-circle' ?>"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Error Messages -->
                <?php if (isset($errores) && !empty($errores)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Errores encontrados:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errores as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Page Content -->
                <div class="content">
                    <?= $contenido ?>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    
    <script>
        // Confirmar eliminación
        function confirmarEliminacion(id, nombre) {
            if (confirm(`¿Estás seguro de que deseas eliminar el producto "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= APP_URL ?>/productos/eliminar/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = 'csrf_token';
                csrfToken.value = '<?= generateCSRFToken() ?>';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>