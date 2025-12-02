<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? APP_NAME ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/favicon.png">

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
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
        }

        /* Sidebar oculto en móvil por defecto */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 80%;
                max-width: 300px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }

        /* Botón hamburguesa */
        .navbar-toggler {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 999;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        @media (min-width: 768px) {
            .navbar-toggler {
                display: none;
            }
            .sidebar {
                position: sticky;
                transform: translateX(0);
            }
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .logo-link {
            cursor: pointer;
            transition: opacity 0.3s ease;
            display: block;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .logo-link:hover {
            opacity: 0.8;
        }

        .logo-link h4,
        .logo-link small {
            pointer-events: none;
            user-select: none;
        }

        .kyoris-logo {
            width: 80px;
            height: auto;
            margin: 0 auto 1rem;
            display: block;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .kyoris-logo:hover {
            transform: scale(1.05);
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

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0 !important;
            }
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
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724 !important;
        }
        .alert-success .bi {
            color: #155724 !important;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24 !important;
        }
        .alert-danger .bi {
            color: #721c24 !important;
        }
    </style>
</head>
<body>
    <!-- Botón hamburguesa (solo móvil) -->
    <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
        <i class="bi bi-list text-white" style="font-size: 1.5rem;"></i>
    </button>

    <!-- Overlay para cerrar sidebar en móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar p-3" id="sidebar">
                <div class="text-center mb-4">
                    <a href="<?= APP_URL ?>" class="text-decoration-none logo-link">
                        <img src="<?= APP_URL ?>/assets/images/kyoris-logo.png" alt="Kyoris Logo" class="kyoris-logo">
                        <h4 class="text-white fw-bold mb-1">
                            KyoShop
                        </h4>
                        <small class="text-white-50">Sistema de Inventario</small>
                    </a>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false || $_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') ? 'active' : '' ?>" href="<?= APP_URL ?>">
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
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'variantes') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/variantes">
                            <i class="bi bi-collection"></i> Variantes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'combos') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/combos">
                            <i class="bi bi-box-seam"></i> Combos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'clientes') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/clientes">
                            <i class="bi bi-people"></i> Clientes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'ventas') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/ventas">
                            <i class="bi bi-cart-check"></i> Ventas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'historial') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/historial">
                            <i class="bi bi-clock-history"></i> Historial
                        </a>
                    </li>

                    <?php
                    // Mostrar opción de usuarios solo para admins
                    $usuarioActual = usuarioActual();
                    if ($usuarioActual && $usuarioActual['rol'] === 'admin'):
                    ?>
                        <hr class="text-white-50 my-2">
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'usuarios') !== false) ? 'active' : '' ?>" href="<?= APP_URL ?>/usuarios">
                                <i class="bi bi-people"></i> Usuarios
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <hr class="text-white-50 my-4">

                <!-- Usuario logueado -->
                <?php $usuario = usuarioActual(); ?>
                <?php if ($usuario): ?>
                    <div class="px-3 mb-3">
                        <div class="bg-white bg-opacity-10 rounded p-2">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill" style="color: #667eea; font-size: 1.5rem;"></i>
                                </div>
                                <div class="ms-2 text-white">
                                    <div class="fw-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($usuario['nombre']) ?></div>
                                    <small class="text-white-50" style="font-size: 0.75rem;"><?= htmlspecialchars($usuario['email']) ?></small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-shield-check"></i> <?= ucfirst($usuario['rol']) ?>
                                </span>
                                <a href="<?= APP_URL ?>/logout" class="btn btn-sm btn-light" title="Cerrar sesión">
                                    <i class="bi bi-box-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

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
                        if (strpos($rutaActual, 'dashboard') !== false || $rutaActual === '/' || $rutaActual === '') {
                            echo '<i class="bi bi-speedometer2"></i> Dashboard';
                        } elseif (strpos($rutaActual, 'usuarios/crear') !== false) {
                            echo '<i class="bi bi-person-plus"></i> Crear Usuario';
                        } elseif (strpos($rutaActual, 'usuarios/editar') !== false) {
                            echo '<i class="bi bi-pencil-square"></i> Editar Usuario';
                        } elseif (strpos($rutaActual, 'usuarios') !== false) {
                            echo '<i class="bi bi-people"></i> Usuarios';
                        } elseif (strpos($rutaActual, 'productos/crear') !== false) {
                            echo '<i class="bi bi-plus-circle"></i> Nuevo Producto';
                        } elseif (strpos($rutaActual, 'productos/editar') !== false) {
                            echo '<i class="bi bi-pencil-square"></i> Editar Producto';
                        } elseif (strpos($rutaActual, 'productos') !== false) {
                            echo '<i class="bi bi-box-seam"></i> Productos';
                        } elseif (strpos($rutaActual, 'variantes/seleccionar') !== false) {
                            echo '<i class="bi bi-check2-square"></i> Seleccionar Productos';
                        } elseif (strpos($rutaActual, 'variantes/configurar') !== false) {
                            echo '<i class="bi bi-gear"></i> Configurar Agrupación';
                        } elseif (strpos($rutaActual, 'variantes/ver') !== false) {
                            echo '<i class="bi bi-eye"></i> Detalles de Variante';
                        } elseif (strpos($rutaActual, 'variantes') !== false) {
                            echo '<i class="bi bi-collection"></i> Variantes';
                        } elseif (strpos($rutaActual, 'combos/crear') !== false) {
                            echo '<i class="bi bi-plus-circle"></i> Crear Combo';
                        } elseif (strpos($rutaActual, 'combos/editar') !== false) {
                            echo '<i class="bi bi-pencil-square"></i> Editar Combo';
                        } elseif (strpos($rutaActual, 'combos/ver') !== false) {
                            echo '<i class="bi bi-eye"></i> Detalle de Combo';
                        } elseif (strpos($rutaActual, 'combos') !== false) {
                            echo '<i class="bi bi-box-seam"></i> Combos';
                        } elseif (strpos($rutaActual, 'clientes/crear') !== false) {
                            echo '<i class="bi bi-person-plus"></i> Nuevo Cliente';
                        } elseif (strpos($rutaActual, 'clientes/editar') !== false) {
                            echo '<i class="bi bi-pencil-square"></i> Editar Cliente';
                        } elseif (strpos($rutaActual, 'clientes') !== false) {
                            echo '<i class="bi bi-people"></i> Clientes';
                        } elseif (strpos($rutaActual, 'ventas/crear') !== false) {
                            echo '<i class="bi bi-cart-plus"></i> Nueva Venta';
                        } elseif (strpos($rutaActual, 'ventas/ver') !== false) {
                            echo '<i class="bi bi-receipt"></i> Detalle de Venta';
                        } elseif (strpos($rutaActual, 'ventas') !== false) {
                            echo '<i class="bi bi-cart-check"></i> Ventas';
                        } elseif (strpos($rutaActual, 'historial') !== false) {
                            echo '<i class="bi bi-clock-history"></i> Historial de Movimientos';
                        }
                        ?>
                    </h1>
                    
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?php if (strpos($_SERVER['REQUEST_URI'], 'productos') !== false && strpos($_SERVER['REQUEST_URI'], 'crear') === false && strpos($_SERVER['REQUEST_URI'], 'editar') === false): ?>
                            <a href="<?= APP_URL ?>/productos/crear" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Nuevo Producto
                            </a>
                        <?php endif; ?>
                        <?php if (strpos($_SERVER['REQUEST_URI'], 'combos') !== false && strpos($_SERVER['REQUEST_URI'], 'crear') === false && strpos($_SERVER['REQUEST_URI'], 'editar') === false && strpos($_SERVER['REQUEST_URI'], 'ver') === false): ?>
                            <a href="<?= APP_URL ?>/combos/crear" class="btn btn-success">
                                <i class="bi bi-plus-lg"></i> Crear Combo
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <?php if (isset($flash)): ?>
                    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show fw-bold" role="alert">
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
        // Toggle sidebar en móvil
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            // Cerrar sidebar al hacer click en overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Cerrar sidebar al hacer click en un link (solo móvil)
            const sidebarLinks = sidebar.querySelectorAll('.nav-link');
            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });
        }

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

        // Auto-hide only dismissible alerts (flash messages) after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>