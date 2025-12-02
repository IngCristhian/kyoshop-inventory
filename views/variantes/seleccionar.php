<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-dark">
                <i class="bi bi-check2-square"></i>
                Seleccionar Productos para Agrupar
            </h1>
            <p class="text-secondary">Paso 1: Selecciona los productos que deseas agrupar como variantes</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= APP_URL ?>/variantes" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= APP_URL ?>/variantes/seleccionar">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                   name="busqueda"
                                   class="form-control"
                                   placeholder="Buscar productos por nombre, código o categoría..."
                                   value="<?= htmlspecialchars($busqueda ?? '') ?>">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <?php if (!empty($busqueda)): ?>
                                <a href="<?= APP_URL ?>/variantes/seleccionar" class="btn btn-outline-secondary">Limpiar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de selección -->
    <form method="POST" action="<?= APP_URL ?>/variantes/configurar" id="formSeleccion">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check"></i>
                            Productos Disponibles (<?= count($productos) ?>)
                        </h5>
                        <div>
                            <span class="badge bg-primary" id="contadorSeleccionados">0 seleccionados</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($productos)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <?php if (empty($busqueda)): ?>
                                    No hay productos disponibles para agrupar. Todos los productos ya están agrupados o no hay productos en el inventario.
                                <?php else: ?>
                                    No se encontraron productos que coincidan con tu búsqueda: "<?= htmlspecialchars($busqueda) ?>"
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodos()">
                                    <i class="bi bi-check-all"></i> Seleccionar Todos
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodos()">
                                    <i class="bi bi-x"></i> Deseleccionar Todos
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" onchange="toggleTodos(this)">
                                            </th>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Talla</th>
                                            <th>Color</th>
                                            <th>Categoría</th>
                                            <th>Stock</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $producto): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox"
                                                           name="productos[]"
                                                           value="<?= $producto['id'] ?>"
                                                           class="producto-checkbox"
                                                           onchange="actualizarContador()">
                                                </td>
                                                <td>
                                                    <?php if (!empty($producto['imagen'])): ?>
                                                        <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                                             alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                             class="img-thumbnail"
                                                             style="max-width: 50px; max-height: 50px;">
                                                    <?php else: ?>
                                                        <div class="bg-light text-center p-2" style="width: 50px; height: 50px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                                    <?php if (!empty($producto['codigo_producto'])): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($producto['codigo_producto']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($producto['talla'] ?? '-') ?></span></td>
                                                <td><?= htmlspecialchars($producto['color'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($producto['categoria'] ?? '-') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $producto['stock'] > 10 ? 'success' : ($producto['stock'] > 5 ? 'warning' : 'danger') ?>">
                                                        <?= $producto['stock'] ?>
                                                    </span>
                                                </td>
                                                <td><?= formatPrice($producto['precio']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($productos)): ?>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-info-circle text-muted"></i>
                                    <small class="text-muted">Selecciona al menos 2 productos para continuar</small>
                                </div>
                                <button type="submit" class="btn btn-primary" id="btnContinuar" disabled>
                                    Siguiente: Configurar
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Contador de productos seleccionados
function actualizarContador() {
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    const contador = checkboxes.length;
    const badge = document.getElementById('contadorSeleccionados');
    const btnContinuar = document.getElementById('btnContinuar');

    badge.textContent = contador + ' seleccionado' + (contador !== 1 ? 's' : '');

    // Habilitar botón solo si hay al menos 2 productos seleccionados
    if (contador >= 2) {
        btnContinuar.disabled = false;
        badge.classList.remove('bg-primary');
        badge.classList.add('bg-success');
    } else {
        btnContinuar.disabled = true;
        badge.classList.remove('bg-success');
        badge.classList.add('bg-primary');
    }
}

// Toggle todos los checkboxes
function toggleTodos(checkbox) {
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    actualizarContador();
}

// Seleccionar todos
function seleccionarTodos() {
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = true;
    });
    document.getElementById('selectAll').checked = true;
    actualizarContador();
}

// Deseleccionar todos
function deseleccionarTodos() {
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    actualizarContador();
}

// Validar formulario antes de enviar
document.getElementById('formSeleccion')?.addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    if (checkboxes.length < 2) {
        e.preventDefault();
        alert('Por favor selecciona al menos 2 productos para agrupar');
    }
});
</script>
