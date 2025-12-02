<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-dark">
                <i class="bi bi-eye"></i>
                Detalles del Producto Agrupado
            </h1>
            <p class="text-secondary"><?= htmlspecialchars($producto['nombre']) ?></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= APP_URL ?>/variantes" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Producto Principal -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-star"></i>
                        Producto Principal
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                             alt="<?= htmlspecialchars($producto['nombre']) ?>"
                             class="img-fluid rounded mb-3"
                             style="max-height: 300px;">
                    <?php else: ?>
                        <div class="bg-light text-center p-5 mb-3">
                            <i class="bi bi-image" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-2">Sin imagen</p>
                        </div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars($producto['nombre']) ?></h4>

                    <?php if (!empty($producto['descripcion'])): ?>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
                    <?php endif; ?>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="text-muted">Categoría</h6>
                            <p><?= htmlspecialchars($producto['categoria']) ?></p>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Tipo</h6>
                            <p><?= htmlspecialchars($producto['tipo']) ?></p>
                        </div>
                    </div>

                    <?php if (!empty($producto['codigo_producto'])): ?>
                        <div class="mt-3">
                            <h6 class="text-muted">Código</h6>
                            <code><?= htmlspecialchars($producto['codigo_producto']) ?></code>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="<?= APP_URL ?>/productos/editar/<?= $producto['id'] ?>"
                           class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                            Editar Producto
                        </a>
                        <form method="POST" action="<?= APP_URL ?>/variantes/desagrupar/<?= $producto['id'] ?>"
                              onsubmit="return confirm('¿Estás seguro de que deseas desagrupar este producto? Las variantes se convertirán en productos independientes.');">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle"></i>
                                Desagrupar Producto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variantes del Producto -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-collection"></i>
                        Variantes Disponibles (<?= count($producto['variantes'] ?? []) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($producto['variantes'])): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Este producto no tiene variantes asociadas.
                        </div>
                    <?php else: ?>
                        <!-- Resumen de Stock Total -->
                        <div class="alert alert-info mb-4">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h6 class="text-muted">Total Variantes</h6>
                                    <h3><?= count($producto['variantes']) ?></h3>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-muted">Stock Total</h6>
                                    <h3>
                                        <?php
                                        $stockTotal = 0;
                                        foreach ($producto['variantes'] as $variante) {
                                            $stockTotal += $variante['stock'];
                                        }
                                        echo $stockTotal;
                                        ?>
                                    </h3>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-muted">Valor Total</h6>
                                    <h3>
                                        <?php
                                        $valorTotal = 0;
                                        foreach ($producto['variantes'] as $variante) {
                                            $valorTotal += $variante['precio'] * $variante['stock'];
                                        }
                                        echo formatPrice($valorTotal);
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Variantes -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Talla</th>
                                        <th>Color</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                        <th>Ubicación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($producto['variantes'] as $variante): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($variante['imagen'])): ?>
                                                    <img src="<?= APP_URL ?>/uploads/<?= $variante['imagen'] ?>"
                                                         alt="Variante"
                                                         class="img-thumbnail"
                                                         style="max-width: 50px; max-height: 50px;">
                                                <?php else: ?>
                                                    <div class="bg-light text-center p-2" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary fs-6">
                                                    <?= htmlspecialchars($variante['talla'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($variante['color'] ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $variante['stock'] > 10 ? 'success' : ($variante['stock'] > 5 ? 'warning' : 'danger') ?>">
                                                    <?= $variante['stock'] ?> unidades
                                                </span>
                                            </td>
                                            <td><?= formatPrice($variante['precio']) ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($variante['ubicacion']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= APP_URL ?>/productos/editar/<?= $variante['id'] ?>"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($variante['producto_padre_id']): ?>
                                                        <form method="POST"
                                                              action="<?= APP_URL ?>/variantes/eliminarVariante/<?= $variante['id'] ?>"
                                                              onsubmit="return confirm('¿Convertir esta variante en producto independiente?');"
                                                              class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    title="Separar variante">
                                                                <i class="bi bi-box-arrow-right"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
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

            <!-- Historial / Información Adicional -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i>
                        Información Adicional
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha de creación:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($producto['fecha_creacion'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Última actualización:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
