<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/combos" class="row g-3">
            <div class="col-md-4">
                <label for="tipo" class="form-label">Tipo de Combo</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Todos los tipos</option>
                    <option value="small" <?= ($filtros['tipo'] ?? '') === 'small' ? 'selected' : '' ?>>Small (10 prendas)</option>
                    <option value="medium" <?= ($filtros['tipo'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium (25 prendas)</option>
                    <option value="big" <?= ($filtros['tipo'] ?? '') === 'big' ? 'selected' : '' ?>>Big (50 prendas)</option>
                    <option value="extra_big" <?= ($filtros['tipo'] ?? '') === 'extra_big' ? 'selected' : '' ?>>Extra Big (100 prendas)</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <select class="form-select" id="ubicacion" name="ubicacion">
                    <option value="">Todas</option>
                    <option value="Medellín" <?= ($filtros['ubicacion'] ?? '') === 'Medellín' ? 'selected' : '' ?>>Medellín</option>
                    <option value="Bogotá" <?= ($filtros['ubicacion'] ?? '') === 'Bogotá' ? 'selected' : '' ?>>Bogotá</option>
                    <option value="Mixto" <?= ($filtros['ubicacion'] ?? '') === 'Mixto' ? 'selected' : '' ?>>Mixto</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Estadísticas -->
<?php if (!empty($estadisticas)): ?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Combos</h6>
                <h3 class="text-primary"><?= $estadisticas['total_combos'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Prendas</h6>
                <h3 class="text-success"><?= $estadisticas['total_prendas'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Precio Promedio</h6>
                <h3 class="text-info"><?= formatPrice($estadisticas['precio_promedio'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Valor Total</h6>
                <h3 class="text-warning"><?= formatPrice($estadisticas['valor_total_combos'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Información de resultados -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Mostrando <?= count($combos) ?> combos
    </div>

    <?php if (!empty($filtros['tipo']) || !empty($filtros['ubicacion'])): ?>
        <a href="<?= APP_URL ?>/combos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-circle"></i> Limpiar filtros
        </a>
    <?php endif; ?>
</div>

<!-- Lista de Combos -->
<?php if (empty($combos)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">No se encontraron combos</h4>
            <p class="text-muted">
                <?php if (!empty($filtros['tipo']) || !empty($filtros['ubicacion'])): ?>
                    Intenta ajustar los filtros de búsqueda.
                <?php else: ?>
                    ¡Comienza creando tu primer combo!
                <?php endif; ?>
            </p>
            <a href="<?= APP_URL ?>/combos/crear" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Crear Combo
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($combos as $combo): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($combo['nombre']) ?></h5>
                            <?php
                            $badgeColors = [
                                'small' => 'info',
                                'medium' => 'success',
                                'big' => 'warning',
                                'extra_big' => 'danger'
                            ];
                            $badgeColor = $badgeColors[$combo['tipo']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeColor ?>">
                                <?= strtoupper($combo['tipo']) ?>
                            </span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Prendas:</small><br>
                                <strong class="text-primary"><?= $combo['cantidad_total'] ?> unidades</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Ubicación:</small><br>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($combo['ubicacion']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h4 class="text-success mb-0"><?= formatPrice($combo['precio']) ?></h4>
                            <small class="text-muted">Precio por prenda: <?= formatPrice($combo['precio'] / $combo['cantidad_total']) ?></small>
                        </div>

                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="<?= APP_URL ?>/combos/ver/<?= $combo['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> Ver Detalle
                                </a>
                                <div class="btn-group">
                                    <a href="<?= APP_URL ?>/combos/editar/<?= $combo['id'] ?>"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirmarEliminacion(<?= $combo['id'] ?>, '<?= htmlspecialchars($combo['nombre']) ?>', event);">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i>
                            Creado: <?= date('d/m/Y H:i', strtotime($combo['fecha_creacion'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function confirmarEliminacion(id, nombre, event) {
    // Prevenir propagación y comportamiento por defecto
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    console.log('=== DEBUG: confirmarEliminacion ===');
    console.log('ID:', id);
    console.log('Nombre:', nombre);
    console.log('Event:', event);

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

    return false;
}
</script>
