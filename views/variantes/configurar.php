<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-dark">
                <i class="bi bi-gear"></i>
                Configurar Producto Principal
            </h1>
            <p class="text-secondary">Paso 2: Elige el producto principal y configura la agrupación</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= APP_URL ?>/variantes/seleccionar" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Atrás
            </a>
        </div>
    </div>

    <!-- Resultado Esperado - SIEMPRE VISIBLE -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info mb-0 resultado-fijo">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <p class="mb-0"><strong><i class="bi bi-collection"></i> Total de variantes:</strong> <?= count($productos) ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-0"><strong><i class="bi bi-box-seam"></i> Stock total:</strong>
                            <?php
                            $stockTotal = 0;
                            foreach ($productos as $p) {
                                $stockTotal += $p['stock'];
                            }
                            echo $stockTotal . ' unidades';
                            ?>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-0"><strong><i class="bi bi-tags"></i> Tallas:</strong>
                            <?php
                            $tallas = array_map(function($p) {
                                return $p['talla'] ?? '-';
                            }, $productos);
                            echo implode(', ', array_unique($tallas));
                            ?>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-0">
                            <i class="bi bi-info-circle-fill"></i>
                            <strong>Se consolidará en 1 producto</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= APP_URL ?>/variantes/agrupar" id="formConfiguracion">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <!-- Productos Seleccionados -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">
                            <i class="bi bi-list-check"></i>
                            Productos Seleccionados (<?= count($productos) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Talla</th>
                                        <th>Color</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productos as $producto): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($producto['imagen'])): ?>
                                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                                         alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                         class="img-thumbnail"
                                                         style="max-width: 40px; max-height: 40px;">
                                                <?php else: ?>
                                                    <div class="bg-light text-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($producto['talla'] ?? '-') ?></span></td>
                                            <td><?= htmlspecialchars($producto['color'] ?? '-') ?></td>
                                            <td><?= $producto['stock'] ?></td>
                                            <td><?= formatPrice($producto['precio']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selección de Producto Padre -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-star"></i>
                            Producto Principal
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            El producto principal será el que aparezca en el catálogo. Las demás variantes se agruparán bajo este producto.
                        </p>

                        <div class="row">
                            <?php foreach ($productos as $index => $producto): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card producto-padre-opcion">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input producto-padre-radio"
                                                       type="radio"
                                                       name="producto_padre_id"
                                                       id="padre_<?= $producto['id'] ?>"
                                                       value="<?= $producto['id'] ?>"
                                                       <?= $index === 0 ? 'checked' : '' ?>
                                                       required>
                                                <label class="form-check-label" for="padre_<?= $producto['id'] ?>">
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($producto['imagen'])): ?>
                                                            <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                                                 alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                                 class="img-thumbnail me-2"
                                                                 style="max-width: 60px; max-height: 60px;">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                Talla: <?= htmlspecialchars($producto['talla'] ?? '-') ?> |
                                                                Stock: <?= $producto['stock'] ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuración del Producto Padre -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">
                            <i class="bi bi-pencil"></i>
                            Editar Información del Producto Principal (Opcional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_padre" class="form-label">Nombre del Producto Agrupado</label>
                                <input type="text"
                                       class="form-control"
                                       id="nombre_padre"
                                       name="nombre_padre"
                                       placeholder="Ej: Camibuso Navidad - Blanco con Verde"
                                       value="">
                                <small class="form-text text-muted">
                                    Deja vacío para mantener el nombre del producto principal seleccionado
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="descripcion_padre" class="form-label">Descripción</label>
                                <textarea class="form-control"
                                          id="descripcion_padre"
                                          name="descripcion_padre"
                                          rows="3"
                                          placeholder="Descripción del producto agrupado..."></textarea>
                                <small class="form-text text-muted">
                                    Deja vacío para mantener la descripción del producto principal
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos Seleccionados y Confirmación -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check"></i>
                            Productos que se consolidarán
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($productos as $producto): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($producto['imagen'])): ?>
                                            <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                                 alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                 class="img-thumbnail me-2"
                                                 style="max-width: 50px; max-height: 50px;">
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($producto['nombre']) ?></span>
                                    </div>
                                    <div>
                                        <span class="badge bg-secondary me-2"><?= htmlspecialchars($producto['talla'] ?? '-') ?></span>
                                        <span class="badge bg-info"><?= $producto['stock'] ?> unidades</span>
                                    </div>
                                    <input type="hidden" name="variantes_ids[]" value="<?= $producto['id'] ?>">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="<?= APP_URL ?>/variantes/seleccionar" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                Cambiar Selección
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i>
                                Confirmar Agrupación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.producto-padre-opcion {
    cursor: pointer;
    transition: all 0.2s;
}

.producto-padre-opcion:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    transform: translateY(-2px);
}

.producto-padre-radio:checked + label {
    font-weight: bold;
}

.producto-padre-radio:checked ~ * {
    border-color: var(--bs-primary);
}

/* Resultado esperado siempre visible en la parte superior */
.resultado-fijo {
    border-left: 5px solid #0dcaf0;
    background-color: #d1ecf1;
    border-color: #0dcaf0;
    font-size: 0.95rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.resultado-fijo p {
    font-size: 0.9rem;
}

.resultado-fijo strong {
    color: #055160;
}
</style>

<script>
// Resaltar la tarjeta seleccionada
document.querySelectorAll('.producto-padre-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        // Remover resaltado de todas las tarjetas
        document.querySelectorAll('.producto-padre-opcion').forEach(card => {
            card.classList.remove('border-primary');
        });

        // Agregar resaltado a la tarjeta seleccionada
        if (this.checked) {
            this.closest('.producto-padre-opcion').classList.add('border-primary');
        }
    });
});

// Inicializar la tarjeta seleccionada por defecto
document.querySelector('.producto-padre-radio:checked')?.closest('.producto-padre-opcion')?.classList.add('border-primary');

// Validar antes de enviar
document.getElementById('formConfiguracion')?.addEventListener('submit', function(e) {
    const productoPadreSeleccionado = document.querySelector('.producto-padre-radio:checked');
    if (!productoPadreSeleccionado) {
        e.preventDefault();
        alert('Por favor selecciona un producto principal');
    }
});
</script>
