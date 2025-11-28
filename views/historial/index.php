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
                <label for="usuario_id" class="form-label">Usuario</label>
                <select class="form-select" id="usuario_id" name="usuario_id">
                    <option value="">Todos los usuarios</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id'] ?>"
                                <?= $filtros['usuario_id'] == $usuario['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre']) ?> (<?= ucfirst($usuario['rol']) ?>)
                        </option>
                    <?php endforeach; ?>
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

            <div class="col-md-1">
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
                <table class="table table-hover historial-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Usuario</th>
                            <th style="width: 40%;">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $mov): ?>
                            <tr>
                                <td class="text-nowrap">
                                    <?= date('d/m/Y', strtotime($mov['fecha_movimiento'])) ?><br>
                                    <small class="text-muted"><?= date('H:i', strtotime($mov['fecha_movimiento'])) ?></small>
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
                                        case 'entrada': $badgeClass = 'bg-success-light text-success'; $icon = 'arrow-up-circle'; $tipoTexto = 'Entrada'; break;
                                        case 'salida': $badgeClass = 'bg-danger-light text-danger'; $icon = 'arrow-down-circle'; $tipoTexto = 'Salida'; break;
                                        case 'venta': $badgeClass = 'bg-primary-light text-primary'; $icon = 'cart-check'; $tipoTexto = 'Venta'; break;
                                        case 'ajuste': $badgeClass = 'bg-warning-light text-warning'; $icon = 'gear'; $tipoTexto = 'Ajuste'; break;
                                        case 'creacion': $badgeClass = 'bg-info-light text-info'; $icon = 'plus-circle'; $tipoTexto = 'Creación'; break;
                                        case 'eliminacion': $badgeClass = 'bg-secondary-light text-secondary'; $icon = 'trash'; $tipoTexto = 'Eliminación'; break;
                                        case 'devolucion': $badgeClass = 'bg-warning-light text-warning'; $icon = 'arrow-return-left'; $tipoTexto = 'Devolución'; break;
                                        case 'cambio_precio':
                                            if ($mov['precio_nuevo'] > $mov['precio_anterior']) {
                                                $badgeClass = 'bg-success-light text-success'; $icon = 'graph-up-arrow'; $tipoTexto = 'Aumento';
                                            } else {
                                                $badgeClass = 'bg-danger-light text-danger'; $icon = 'graph-down-arrow'; $tipoTexto = 'Reducción';
                                            }
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> fs-6">
                                        <i class="bi bi-<?= $icon ?> me-1"></i> <?= $tipoTexto ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($mov['usuario_nombre']) ?><br>
                                    <small class="text-muted"><?= ucfirst($mov['usuario_rol']) ?></small>
                                </td>
                                <td>
                                    <div class="detalle-movimiento">
                                        <?php
                                        // Mostrar cambio de stock
                                        if ($mov['stock_anterior'] != $mov['stock_nuevo'] && $mov['tipo_movimiento'] !== 'cambio_precio') {
                                            $diff = $mov['stock_nuevo'] - $mov['stock_anterior'];
                                            $diffClass = $diff >= 0 ? 'text-success' : 'text-danger';
                                            echo '<div class="fw-bold">Stock: ' . $mov['stock_anterior'] . ' &rarr; ' . $mov['stock_nuevo'] . 
                                                 ' <span class="' . $diffClass . '">(' . ($diff >= 0 ? '+' : '') . $diff . ')</span></div>';
                                        }

                                        // Mostrar cambio de precio
                                        if ($mov['precio_anterior'] != $mov['precio_nuevo']) {
                                            $diff_precio = $mov['precio_nuevo'] - $mov['precio_anterior'];
                                            $diff_precio_class = $diff_precio >= 0 ? 'text-success' : 'text-danger';
                                            echo '<div class="fw-bold">Precio: ' . formatPrice($mov['precio_anterior']) . ' &rarr; ' . formatPrice($mov['precio_nuevo']) .
                                                 ' <span class="' . $diff_precio_class . '">(' . ($diff_precio >= 0 ? '+' : '-') . formatPrice(abs($diff_precio)) . ')</span></div>';
                                        }

                                        // Mostrar motivo si existe
                                        if (!empty($mov['motivo'])) {
                                            echo '<small class="text-muted">' . htmlspecialchars($mov['motivo']) . '</small>';
                                        }
                                        ?>
                                    </div>
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
                        <a class="page-link" href="<?= APP_URL ?>/historial?pagina=<?= $paginacion['pagina_actual'] - 1 ?><?= !empty($filtros['producto_id']) ? '&producto_id=' . urlencode($filtros['producto_id']) : '' ?><?= !empty($filtros['tipo_movimiento']) ? '&tipo_movimiento=' . urlencode($filtros['tipo_movimiento']) : '' ?><?= !empty($filtros['usuario_id']) ? '&usuario_id=' . urlencode($filtros['usuario_id']) : '' ?><?= !empty($filtros['fecha_desde']) ? '&fecha_desde=' . urlencode($filtros['fecha_desde']) : '' ?><?= !empty($filtros['fecha_hasta']) ? '&fecha_hasta=' . urlencode($filtros['fecha_hasta']) : '' ?>">
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
                        <a class="page-link" href="<?= APP_URL ?>/historial?pagina=<?= $i ?><?= !empty($filtros['producto_id']) ? '&producto_id=' . urlencode($filtros['producto_id']) : '' ?><?= !empty($filtros['tipo_movimiento']) ? '&tipo_movimiento=' . urlencode($filtros['tipo_movimiento']) : '' ?><?= !empty($filtros['usuario_id']) ? '&usuario_id=' . urlencode($filtros['usuario_id']) : '' ?><?= !empty($filtros['fecha_desde']) ? '&fecha_desde=' . urlencode($filtros['fecha_desde']) : '' ?><?= !empty($filtros['fecha_hasta']) ? '&fecha_hasta=' . urlencode($filtros['fecha_hasta']) : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= APP_URL ?>/historial?pagina=<?= $paginacion['pagina_actual'] + 1 ?><?= !empty($filtros['producto_id']) ? '&producto_id=' . urlencode($filtros['producto_id']) : '' ?><?= !empty($filtros['tipo_movimiento']) ? '&tipo_movimiento=' . urlencode($filtros['tipo_movimiento']) : '' ?><?= !empty($filtros['usuario_id']) ? '&usuario_id=' . urlencode($filtros['usuario_id']) : '' ?><?= !empty($filtros['fecha_desde']) ? '&fecha_desde=' . urlencode($filtros['fecha_desde']) : '' ?><?= !empty($filtros['fecha_hasta']) ? '&fecha_hasta=' . urlencode($filtros['fecha_hasta']) : '' ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
