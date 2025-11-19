<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Clientes</h6>
                        <h3 class="mb-0"><?= $estadisticas['total_clientes'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Con Email</h6>
                        <h3 class="mb-0"><?= $estadisticas['clientes_con_email'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-envelope-check" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Ciudades</h6>
                        <h3 class="mb-0"><?= $estadisticas['total_ciudades'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-geo-alt" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/clientes" class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold">Búsqueda</label>
                <input type="text"
                       class="form-control"
                       name="busqueda"
                       value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>"
                       placeholder="Buscar por nombre, teléfono o email...">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Ciudad</label>
                <select class="form-select" name="ciudad">
                    <option value="">Todas</option>
                    <?php foreach ($ciudades as $ciudad): ?>
                        <option value="<?= htmlspecialchars($ciudad) ?>"
                                <?= ($filtros['ciudad'] ?? '') === $ciudad ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ciudad) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Clientes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h5 class="card-title text-white mb-0">
            <i class="bi bi-list-ul"></i> Listado de Clientes
        </h5>
        <a href="<?= APP_URL ?>/clientes/crear" class="btn btn-light btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Cliente
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($clientes)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No se encontraron clientes con los filtros aplicados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="fw-bold">
                                    <i class="bi bi-person-circle text-primary"></i>
                                    <?= htmlspecialchars($cliente['nombre']) ?>
                                </td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($cliente['telefono']) ?>" class="text-decoration-none">
                                        <i class="bi bi-telephone"></i> <?= htmlspecialchars($cliente['telefono']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($cliente['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>" class="text-decoration-none">
                                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($cliente['email']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($cliente['ciudad']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= APP_URL ?>/clientes/editar/<?= $cliente['id'] ?>"
                                           class="btn btn-outline-primary"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmarEliminacion(<?= $cliente['id'] ?>, '<?= htmlspecialchars($cliente['nombre'], ENT_QUOTES) ?>')"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarEliminacion(id, nombre) {
    console.log('=== CONFIRMACIÓN DE ELIMINACIÓN ===');
    console.log('Cliente ID:', id);
    console.log('Cliente Nombre:', nombre);
    console.log('Tipo de confirmación: CLIENTE');

    if (confirm(`¿Estás seguro de que deseas eliminar al cliente "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        console.log('Usuario confirmó eliminación');

        const appUrl = '<?= APP_URL ?>';
        const url = `${appUrl}/clientes/eliminar/${id}`;

        console.log('URL de eliminación:', url);

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= generateCSRFToken() ?>';
        form.appendChild(csrfToken);

        console.log('CSRF Token:', csrfToken.value);
        console.log('Formulario creado, enviando...');

        document.body.appendChild(form);
        form.submit();
    } else {
        console.log('Usuario canceló eliminación');
    }
}
</script>
