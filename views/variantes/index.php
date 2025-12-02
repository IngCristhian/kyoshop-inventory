<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">
                <i class="bi bi-collection"></i>
                Gestión de Variantes de Productos
            </h1>
            <p class="text-muted">Agrupa productos por tallas y colores para optimizar tu catálogo</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= APP_URL ?>/variantes/seleccionar" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Agrupar Productos
            </a>
        </div>
    </div>

    <!-- Mostrar error si existe -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Productos</h6>
                    <h3 class="card-title mb-0"><?= number_format($estadisticas['total_productos'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Con Variantes</h6>
                    <h3 class="card-title mb-0"><?= number_format($estadisticas['productos_con_variantes'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Variantes</h6>
                    <h3 class="card-title mb-0"><?= number_format($estadisticas['total_variantes'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Agrupados -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-collection-fill"></i>
                        Productos Agrupados (<?= count($productos_agrupados) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($productos_agrupados)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            No hay productos agrupados aún. Haz clic en "Agrupar Productos" para comenzar.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Producto</th>
                                        <th>Variantes</th>
                                        <th>Tallas Disponibles</th>
                                        <th>Stock Total</th>
                                        <th>Precio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productos_agrupados as $producto): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($producto['imagen'])): ?>
                                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                                         alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                         class="img-thumbnail"
                                                         style="max-width: 60px; max-height: 60px;">
                                                <?php else: ?>
                                                    <div class="bg-light text-center p-2" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $producto['total_variantes'] ?> variantes</span>
                                            </td>
                                            <td>
                                                <?php
                                                $tallas = explode(',', $producto['tallas_disponibles'] ?? '');
                                                foreach ($tallas as $talla):
                                                    if (!empty(trim($talla))):
                                                ?>
                                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars(trim($talla)) ?></span>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $producto['stock_total'] > 10 ? 'success' : ($producto['stock_total'] > 5 ? 'warning' : 'danger') ?>">
                                                    <?= $producto['stock_total'] ?> unidades
                                                </span>
                                            </td>
                                            <td><?= formatPrice($producto['precio']) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= APP_URL ?>/variantes/ver/<?= $producto['id'] ?>"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <form method="POST" action="<?= APP_URL ?>/variantes/desagrupar/<?= $producto['id'] ?>"
                                                          onsubmit="return confirm('¿Estás seguro de que deseas desagrupar este producto? Las variantes se convertirán en productos independientes.');"
                                                          class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Desagrupar">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
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
        </div>
    </div>

    <!-- Productos Sin Agrupar -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-box"></i>
                        Productos Sin Agrupar (<?= count($productos_sin_agrupar) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($productos_sin_agrupar)): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            Todos los productos están agrupados.
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Estos productos aún no están agrupados como variantes.</p>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Talla</th>
                                        <th>Color</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Mostrar solo los primeros 10
                                    $contador = 0;
                                    foreach ($productos_sin_agrupar as $producto):
                                        if ($contador >= 10) break;
                                        $contador++;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                            <td><?= htmlspecialchars($producto['talla'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($producto['color'] ?? '-') ?></td>
                                            <td><?= $producto['stock'] ?></td>
                                            <td><?= formatPrice($producto['precio']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (count($productos_sin_agrupar) > 10): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <small>... y <?= count($productos_sin_agrupar) - 10 ?> productos más</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?= APP_URL ?>/variantes/seleccionar" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i>
                                Agrupar Estos Productos
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
