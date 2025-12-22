<!-- Información del Combo -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3><?= htmlspecialchars($combo['nombre']) ?></h3>
                        <?php
                        $tiposNombres = [
                            'small' => 'Small',
                            'medium' => 'Medium',
                            'big' => 'Big',
                            'extra_big' => 'Extra Big'
                        ];
                        $badgeColors = [
                            'small' => 'info',
                            'medium' => 'success',
                            'big' => 'warning',
                            'extra_big' => 'danger'
                        ];
                        ?>
                        <span class="badge bg-<?= $badgeColors[$combo['tipo']] ?? 'secondary' ?> me-2">
                            <?= $tiposNombres[$combo['tipo']] ?? $combo['tipo'] ?>
                        </span>
                        <span class="badge bg-secondary">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($combo['ubicacion']) ?>
                        </span>
                    </div>
                    <h2 class="text-success mb-0"><?= formatPrice($combo['precio']) ?></h2>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Total de Prendas</small>
                        <h4 class="text-primary"><?= $combo['cantidad_total'] ?></h4>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Precio por Prenda</small>
                        <h4 class="text-info"><?= formatPrice($combo['precio'] / $combo['cantidad_total']) ?></h4>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Tipos</small>
                        <h4 class="text-warning"><?= count($combo['categorias']) ?></h4>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted">Distribución por Tipos</h6>
                    <div class="row g-2">
                        <?php foreach ($combo['categorias'] as $cat): ?>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="badge bg-primary"><?= htmlspecialchars($cat['tipo']) ?></span>
                                    <strong><?= $cat['cantidad'] ?> prendas</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr>

                <small class="text-muted">
                    <i class="bi bi-clock"></i> Creado: <?= date('d/m/Y H:i', strtotime($combo['fecha_creacion'])) ?><br>
                    <i class="bi bi-clock-history"></i> Actualizado: <?= date('d/m/Y H:i', strtotime($combo['fecha_actualizacion'])) ?>
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Acciones</h6>
                <div class="d-grid gap-2">
                    <a href="<?= APP_URL ?>/combos/editar/<?= $combo['id'] ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Combo
                    </a>
                    <button type="button" class="btn btn-outline-danger"
                            onclick="confirmarEliminacion(<?= $combo['id'] ?>, '<?= htmlspecialchars($combo['nombre']) ?>')">
                        <i class="bi bi-trash"></i> Eliminar Combo
                    </button>
                    <a href="<?= APP_URL ?>/combos" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Combos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Productos del Combo -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-check"></i> Productos Incluidos en el Combo
            <span class="badge bg-primary ms-2"><?= count($combo['productos']) ?> productos</span>
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($combo['productos'])): ?>
            <p class="text-muted text-center py-4">No hay productos asignados a este combo</p>
        <?php else: ?>
            <?php
            // Agrupar productos por tipo
            $productosPorTipo = [];
            foreach ($combo['productos'] as $producto) {
                $tipo = $producto['tipo'];
                if (!isset($productosPorTipo[$tipo])) {
                    $productosPorTipo[$tipo] = [];
                }
                $productosPorTipo[$tipo][] = $producto;
            }
            ?>

            <?php foreach ($productosPorTipo as $tipo => $productos): ?>
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2">
                        <span class="badge bg-primary me-2"><?= htmlspecialchars($tipo) ?></span>
                        <?= count($productos) ?> productos
                    </h6>

                    <div class="row">
                        <?php foreach ($productos as $producto): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="d-flex">
                                            <?php if ($producto['imagen']): ?>
                                                <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                                     class="rounded me-2"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                     style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>

                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small"><?= htmlspecialchars($producto['nombre']) ?></h6>
                                                <small class="text-muted d-block"><?= htmlspecialchars($producto['codigo_producto']) ?></small>
                                                <div class="mt-1">
                                                    <?php if ($producto['talla']): ?>
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= htmlspecialchars($producto['talla']) ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($producto['color']): ?>
                                                        <span class="badge bg-info" style="font-size: 0.7rem;"><?= htmlspecialchars($producto['color']) ?></span>
                                                    <?php endif; ?>
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($producto['ubicacion']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarEliminacion(id, nombre) {
    console.log('=== DEBUG: confirmarEliminacion ===');
    console.log('ID:', id);
    console.log('Nombre:', nombre);

    if (confirm(`¿Está seguro de eliminar el combo "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        console.log('Usuario confirmó eliminación');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= APP_URL ?>/combos/eliminar/' + id;

        console.log('Action:', form.action);

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= generateCSRFToken() ?>';

        console.log('CSRF Token:', csrfInput.value);

        form.appendChild(csrfInput);
        document.body.appendChild(form);

        console.log('Enviando formulario...');
        form.submit();
    } else {
        console.log('Usuario canceló eliminación');
    }
}
</script>
