<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Nota:</strong> Solo se pueden editar el nombre y el precio del combo. Las cantidades y productos ya están asignados y no pueden modificarse.
        </div>

        <form method="POST" action="<?= APP_URL ?>/combos/actualizar/<?= $combo['id'] ?>">

            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre del Combo *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?= htmlspecialchars($combo['nombre']) ?>"
                           required maxlength="255">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="precio" class="form-label">Precio del Combo *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="precio" name="precio"
                               value="<?= $combo['precio'] ?>"
                               step="0.01" min="0" required>
                    </div>
                </div>
            </div>

            <hr>

            <h6 class="text-muted mb-3">Información del Combo (No Editable)</h6>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label text-muted">Tipo</label>
                    <input type="text" class="form-control" value="<?= strtoupper($combo['tipo']) ?>" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted">Total Prendas</label>
                    <input type="text" class="form-control" value="<?= $combo['cantidad_total'] ?>" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted">Ubicación</label>
                    <input type="text" class="form-control" value="<?= $combo['ubicacion'] ?>" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted">Productos</label>
                    <input type="text" class="form-control" value="<?= count($combo['productos']) ?>" disabled>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted">Categorías</label>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach ($combo['categorias'] as $cat): ?>
                        <span class="badge bg-primary">
                            <?= htmlspecialchars($cat['tipo']) ?>: <?= $cat['cantidad'] ?> prendas
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= APP_URL ?>/combos/ver/<?= $combo['id'] ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Actualizar Combo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const precio = parseFloat(document.getElementById('precio').value);

    if (precio <= 0) {
        e.preventDefault();
        alert('El precio debe ser mayor a 0');
        return;
    }

    if (!confirm('¿Está seguro de actualizar este combo?')) {
        e.preventDefault();
        return;
    }
});
</script>
