<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/historial" class="row g-3">
            <div class="col-md-3">
                <label for="producto_id" class="form-label">Producto</label>
                <select class="form-select" id="producto_id" name="producto_id">
                    <option value="">Todos los productos</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>"
                                <?= $filtros['producto_id'] == $producto['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($producto['nombre']) ?> (<?= htmlspecialchars($producto['codigo_producto']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="tipo_movimiento" class="form-label">Tipo</label>
                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento">
                    <option value="">Todos los tipos</option>
                    <option value="entrada" <?= $filtros['tipo_movimiento'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                    <option value="salida" <?= $filtros['tipo_movimiento'] === 'salida' ? 'selected' : '' ?>>Salida</option>
                    <option value="venta" <?= $filtros['tipo_movimiento'] === 'venta' ? 'selected' : '' ?>>Venta</option>
                    <option value="ajuste" <?= $filtros['tipo_movimiento'] === 'ajuste' ? 'selected' : '' ?>>Ajuste</option>
                    <option value="creacion" <?= $filtros['tipo_movimiento'] === 'creacion' ? 'selected' : '' ?>>Creación</option>
                    <option value="eliminacion" <?= $filtros['tipo_movimiento'] === 'eliminacion' ? 'selected' : '' ?>>Eliminación</option>
                    <option value="cambio_precio" <?= $filtros['tipo_movimiento'] === 'cambio_precio' ? 'selected' : '' ?>>Cambio de precio</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="fecha_desde" class="form-label">Desde</label>
                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                       value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
            </div>

            <div class="col-md-2">
                <label for="fecha_hasta" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                       value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <?php if (!empty(array_filter($filtros))): ?>
                        <a href="<?= APP_URL ?>/historial" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Información de resultados -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Mostrando <?= count($movimientos) ?> de <?= $paginacion['total_movimientos'] ?> movimientos
        <?php if ($paginacion['total_paginas'] > 1): ?>
            (Página <?= $paginacion['pagina_actual'] ?> de <?= $paginacion['total_paginas'] ?>)
        <?php endif; ?>
    </div>
</div>

<!-- Tabla de movimientos -->
<?php if (empty($movimientos)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">No hay movimientos registrados</h4>
            <p class="text-muted">
                <?php if (!empty(array_filter($filtros))): ?>
                    Intenta ajustar los filtros de búsqueda.
                <?php else: ?>
                    Los movimientos se registrarán automáticamente.
                <?php endif; ?>
            </p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Stock Anterior</th>
                            <th>Stock Nuevo</th>
                            <th>Precio Anterior</th>
                            <th>Precio Nuevo</th>
                            <th>Usuario</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $mov): ?>
                            <tr>
                                <td>
                                    <small>
                                        <?= date('d/m/Y', strtotime($mov['fecha_movimiento'])) ?><br>
                                        <span class="text-muted"><?= date('H:i', strtotime($mov['fecha_movimiento'])) ?></span>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($mov['producto_nombre']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($mov['codigo_producto']) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = '';
                                    $icon = '';
                                    $tipoTexto = '';
                                    switch($mov['tipo_movimiento']) {
                                        case 'entrada':
                                            $badgeClass = 'bg-success';
                                            $icon = 'arrow-up-circle';
                                            $tipoTexto = 'Entrada';
                                            break;
                                        case 'salida':
                                            $badgeClass = 'bg-danger';
                                            $icon = 'arrow-down-circle';
                                            $tipoTexto = 'Salida';
                                            break;
                                        case 'ajuste':
                                            $badgeClass = 'bg-warning';
                                            $icon = 'gear';
                                            $tipoTexto = 'Ajuste';
                                            break;
                                        case 'creacion':
                                            $badgeClass = 'bg-info';
                                            $icon = 'plus-circle';
                                            $tipoTexto = 'Creación';
                                            break;
                                        case 'eliminacion':
                                            $badgeClass = 'bg-secondary';
                                            $icon = 'trash';
                                            $tipoTexto = 'Eliminación';
                                            break;
                                        case 'venta':
                                            $badgeClass = 'bg-primary';
                                            $icon = 'cart-check';
                                            $tipoTexto = 'Venta';
                                            break;
                                        case 'cambio_precio':
                                            // Detectar si fue aumento o disminución
                                            if ($mov['precio_nuevo'] > $mov['precio_anterior']) {
                                                $badgeClass = 'bg-success';
                                                $icon = 'arrow-up-circle-fill';
                                                $tipoTexto = 'Aumento precio';
                                            } else {
                                                $badgeClass = 'bg-danger';
                                                $icon = 'arrow-down-circle-fill';
                                                $tipoTexto = 'Bajó precio';
                                            }
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <i class="bi bi-<?= $icon ?>"></i> <?= $tipoTexto ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="<?= $mov['cantidad'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $mov['cantidad'] >= 0 ? '+' : '' ?><?= $mov['cantidad'] ?>
                                    </strong>
                                </td>
                                <td><?= $mov['stock_anterior'] ?></td>
                                <td><strong><?= $mov['stock_nuevo'] ?></strong></td>
                                <td>
                                    <?php if ($mov['precio_anterior']): ?>
                                        <span class="text-muted">$<?= number_format($mov['precio_anterior'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($mov['precio_nuevo']): ?>
                                        <strong class="<?= $mov['tipo_movimiento'] === 'cambio_precio' && $mov['precio_nuevo'] > $mov['precio_anterior'] ? 'text-success' : ($mov['tipo_movimiento'] === 'cambio_precio' ? 'text-danger' : '') ?>">
                                            $<?= number_format($mov['precio_nuevo'], 0, ',', '.') ?>
                                        </strong>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($mov['usuario_nombre']) ?><br>
                                    <small class="text-muted"><?= ucfirst($mov['usuario_rol']) ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($mov['motivo']) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <?php if ($paginacion['total_paginas'] > 1): ?>
        <nav aria-label="Paginación de historial" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($paginacion['pagina_actual'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= APP_URL ?>/historial?pagina=<?= $paginacion['pagina_actual'] - 1 ?><?= !empty($filtros['producto_id']) ? '&producto_id=' . urlencode($filtros['producto_id']) : '' ?><?= !empty($filtros['tipo_movimiento']) ? '&tipo_movimiento=' . urlencode($filtros['tipo_movimiento']) : '' ?><?= !empty($filtros['fecha_desde']) ? '&fecha_desde=' . urlencode($filtros['fecha_desde']) : '' ?><?= !empty($filtros['fecha_hasta']) ? '&fecha_hasta=' . urlencode($filtros['fecha_hasta']) : '' ?>">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                $inicio = max(1, $paginacion['pagina_actual'] - 2);
                $fin = min($paginacion['total_paginas'], $paginacion['pagina_actual'] + 2);
                ?>

                <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                    <li class="page-item <?= $i == $paginacion['pagina_actual'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/historial?pagina=<?= $i ?><?= !empty($filtros['producto_id']) ? '&producto_id=' . urlencode($filtros['producto_id']) : '' ?><?= !empty($filtros['tipo_movimiento']) ? '&tipo_movimiento=' . urlencode($filtros['tipo_movimiento']) : '' ?><?= !empty($filtros['fecha_desde']) ? '&fecha_desde=' . urlencode($filtros['fecha_desde']) : '' ?><?= !empty($filtros['fecha_hasta']) ? '&fecha_hasta=' . urlencode($filtros['fecha_hasta']) : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= APP_URL ?>/historial?pagina=<?= $paginacion['pagina_actual'] + 1 ?><?= !empty($filtros['producto_id']) ? '&producto_id=' . urlencode($filtros['producto_id']) : '' ?><?= !empty($filtros['tipo_movimiento']) ? '&tipo_movimiento=' . urlencode($filtros['tipo_movimiento']) : '' ?><?= !empty($filtros['fecha_desde']) ? '&fecha_desde=' . urlencode($filtros['fecha_desde']) : '' ?><?= !empty($filtros['fecha_hasta']) ? '&fecha_hasta=' . urlencode($filtros['fecha_hasta']) : '' ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
