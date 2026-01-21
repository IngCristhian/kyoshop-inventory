<!-- Dashboard Principal -->
<div class="row mb-4">
    <!-- Estadísticas Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Productos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($estadisticas['total_productos']) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box-seam fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Stock Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($estadisticas['total_stock'] ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-stack fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stock Bajo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($estadisticas['productos_bajo_stock']) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Valor Inventario
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= formatPrice($estadisticas['valor_total_inventario']) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas Financieras -->
<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="card text-white shadow h-100 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Ventas
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            <?= formatPrice($total_ventas ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up-arrow fa-2x" style="opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white bg-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Compras Insumos
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            <?= formatPrice($total_compras ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-bag-check fa-2x" style="opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white shadow h-100 py-2" style="background: linear-gradient(135deg, <?= ($ganancia_neta ?? 0) >= 0 ? '#56ab2f 0%, #a8e063 100%' : '#eb3349 0%, #f45c43 100%' ?>);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Ganancia Neta
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            <?= formatPrice($ganancia_neta ?? 0) ?>
                        </div>
                        <small style="opacity: 0.8;">Ventas - Compras Insumos</small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-<?= ($ganancia_neta ?? 0) >= 0 ? 'cash-stack' : 'exclamation-triangle' ?> fa-2x" style="opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas por Tipo de Prenda -->
<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="card text-white shadow h-100 py-2" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Prendas de Niño
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            <?= number_format($estadisticas['stock_nino'] ?? 0) ?>
                        </div>
                        <small style="opacity: 0.8;">Unidades en stock</small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white shadow h-100 py-2" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Prendas de Mujer
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            <?= number_format($estadisticas['stock_mujer'] ?? 0) ?>
                        </div>
                        <small style="opacity: 0.8;">Unidades en stock</small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-standing-dress" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white shadow h-100 py-2" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Prendas de Hombre
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            <?= number_format($estadisticas['stock_hombre'] ?? 0) ?>
                        </div>
                        <small style="opacity: 0.8;">Unidades en stock</small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-standing" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Productos con Stock Bajo -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-exclamation-triangle"></i> Productos con Stock Bajo
                </h6>
                <a href="<?= APP_URL ?>/productos?stock_bajo=1" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (!empty($productos_stock_bajo)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($productos_stock_bajo, 0, 5) as $producto): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($producto['imagen']): ?>
                                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>" 
                                                         class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($producto['nombre']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($producto['categoria']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $producto['stock'] == 0 ? 'danger' : 'warning' ?>">
                                                <?= $producto['stock'] ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= formatPrice($producto['precio']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">¡Excelente! No hay productos con stock bajo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Productos Recientes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i> Productos Recientes
                </h6>
                <a href="<?= APP_URL ?>/productos" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (!empty($productos_recientes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos_recientes as $producto): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($producto['imagen']): ?>
                                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>" 
                                                         class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($producto['nombre']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($producto['categoria']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $producto['stock'] <= 5 ? ($producto['stock'] == 0 ? 'danger' : 'warning') : 'success' ?>">
                                                <?= $producto['stock'] ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= formatPrice($producto['precio']) ?></td>
                                        <td>
                                            <a href="<?= APP_URL ?>/productos/editar/<?= $producto['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No hay productos registrados aún.</p>
                        <a href="<?= APP_URL ?>/productos/crear" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Crear Primer Producto
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>