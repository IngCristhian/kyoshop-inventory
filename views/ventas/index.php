<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Ventas (30d)</h6>
                        <h3 class="mb-0"><?= $estadisticas['total_ventas'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-cart-check" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Monto Total</h6>
                        <h3 class="mb-0">$<?= number_format($estadisticas['monto_total'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                    <i class="bi bi-cash-stack" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Ticket Promedio</h6>
                        <h3 class="mb-0">$<?= number_format($estadisticas['ticket_promedio'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                    <i class="bi bi-receipt" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Clientes Únicos</h6>
                        <h3 class="mb-0"><?= $estadisticas['clientes_unicos'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/ventas" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Búsqueda</label>
                <input type="text"
                       class="form-control"
                       name="busqueda"
                       value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>"
                       placeholder="Número de venta o cliente...">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Método de Pago</label>
                <select class="form-select" name="metodo_pago">
                    <option value="">Todos</option>
                    <option value="transferencia" <?= ($filtros['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                    <option value="contra_entrega" <?= ($filtros['metodo_pago'] ?? '') === 'contra_entrega' ? 'selected' : '' ?>>Contra Entrega</option>
                    <option value="efectivo" <?= ($filtros['metodo_pago'] ?? '') === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                    <option value="tarjeta" <?= ($filtros['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Estado de Pago</label>
                <select class="form-select" name="estado_pago">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= ($filtros['estado_pago'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="pagado" <?= ($filtros['estado_pago'] ?? '') === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                    <option value="cancelado" <?= ($filtros['estado_pago'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Desde</label>
                <input type="date" class="form-control" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Hasta</label>
                <input type="date" class="form-control" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>">
            </div>

            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Ventas -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h5 class="card-title text-white mb-0">
            <i class="bi bi-list-ul"></i> Listado de Ventas
        </h5>
        <a href="<?= APP_URL ?>/ventas/crear" class="btn btn-light btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Venta
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($ventas)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No se encontraron ventas con los filtros aplicados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th>N° Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Vendedor</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($venta['numero_venta']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($venta['cliente_nombre']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($venta['cliente_telefono']) ?></small>
                                </td>
                                <td class="fw-bold text-success">$<?= number_format($venta['total'], 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $iconoMetodo = [
                                        'transferencia' => 'bank',
                                        'contra_entrega' => 'truck',
                                        'efectivo' => 'cash',
                                        'tarjeta' => 'credit-card'
                                    ];
                                    $metodo = $venta['metodo_pago'];
                                    ?>
                                    <i class="bi bi-<?= $iconoMetodo[$metodo] ?? 'cash' ?>"></i>
                                    <?= ucfirst(str_replace('_', ' ', $metodo)) ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'pendiente' => 'warning',
                                        'pagado' => 'success',
                                        'cancelado' => 'danger'
                                    ];
                                    $estado = $venta['estado_pago'];
                                    ?>
                                    <span class="badge bg-<?= $badgeClass[$estado] ?? 'secondary' ?>">
                                        <?= ucfirst($estado) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($venta['vendedor_nombre']) ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= APP_URL ?>/ventas/ver/<?= $venta['id'] ?>"
                                           class="btn btn-outline-primary"
                                           title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= APP_URL ?>/ventas/factura/<?= $venta['id'] ?>"
                                           class="btn btn-outline-success"
                                           title="Ver Factura"
                                           target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($paginacion['total_paginas'] > 1): ?>
                <nav aria-label="Paginación">
                    <ul class="pagination justify-content-center mt-4">
                        <?php if ($paginacion['pagina_actual'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $paginacion['pagina_actual'] - 1 ?><?= http_build_query(array_filter($filtros), '', '&') ? '&' . http_build_query(array_filter($filtros)) : '' ?>">
                                    Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++): ?>
                            <li class="page-item <?= $i == $paginacion['pagina_actual'] ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?><?= http_build_query(array_filter($filtros), '', '&') ? '&' . http_build_query(array_filter($filtros)) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $paginacion['pagina_actual'] + 1 ?><?= http_build_query(array_filter($filtros), '', '&') ? '&' . http_build_query(array_filter($filtros)) : '' ?>">
                                    Siguiente
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
