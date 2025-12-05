<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Compras (30d)</h6>
                        <h3 class="mb-0"><?= $estadisticas['total_compras'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-bag-check" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Monto Total</h6>
                        <h3 class="mb-0">$<?= number_format($estadisticas['monto_total_compras'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                    <i class="bi bi-cash-coin" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Ticket Promedio</h6>
                        <h3 class="mb-0">$<?= number_format($estadisticas['ticket_promedio'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                    <i class="bi bi-receipt-cutoff" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Bolsas</h6>
                        <h3 class="mb-0"><?= $estadisticas['compras_bolsas'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-basket" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/compras" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Búsqueda</label>
                <input type="text"
                       class="form-control"
                       name="busqueda"
                       value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>"
                       placeholder="Número, descripción, proveedor...">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Categoría</label>
                <select class="form-select" name="categoria_insumo">
                    <option value="">Todas</option>
                    <option value="bolsas" <?= ($filtros['categoria_insumo'] ?? '') === 'bolsas' ? 'selected' : '' ?>>Bolsas</option>
                    <option value="etiquetas" <?= ($filtros['categoria_insumo'] ?? '') === 'etiquetas' ? 'selected' : '' ?>>Etiquetas</option>
                    <option value="cajas" <?= ($filtros['categoria_insumo'] ?? '') === 'cajas' ? 'selected' : '' ?>>Cajas</option>
                    <option value="embalaje" <?= ($filtros['categoria_insumo'] ?? '') === 'embalaje' ? 'selected' : '' ?>>Embalaje</option>
                    <option value="publicidad" <?= ($filtros['categoria_insumo'] ?? '') === 'publicidad' ? 'selected' : '' ?>>Publicidad</option>
                    <option value="otros" <?= ($filtros['categoria_insumo'] ?? '') === 'otros' ? 'selected' : '' ?>>Otros</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Método de Pago</label>
                <select class="form-select" name="metodo_pago">
                    <option value="">Todos</option>
                    <option value="transferencia" <?= ($filtros['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                    <option value="efectivo" <?= ($filtros['metodo_pago'] ?? '') === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                    <option value="tarjeta" <?= ($filtros['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                    <option value="credito" <?= ($filtros['metodo_pago'] ?? '') === 'credito' ? 'selected' : '' ?>>Crédito</option>
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

<!-- Tabla de Compras -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <h5 class="card-title text-white mb-0">
            <i class="bi bi-list-ul"></i> Listado de Compras de Insumos
        </h5>
        <a href="<?= APP_URL ?>/compras/crear" class="btn btn-light btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Compra
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($compras)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No se encontraron compras con los filtros aplicados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                        <tr>
                            <th>N° Compra</th>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($compra['numero_compra']) ?></td>
                                <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($compra['descripcion']) ?></strong>
                                    <?php if (!empty($compra['cantidad'])): ?>
                                        <br><small class="text-muted">Cantidad: <?= $compra['cantidad'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= ucfirst(htmlspecialchars($compra['categoria_insumo'])) ?></span>
                                </td>
                                <td><?= htmlspecialchars($compra['proveedor'] ?? '-') ?></td>
                                <td class="fw-bold text-danger">$<?= number_format($compra['total'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $compra['metodo_pago'])) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/compras/ver/<?= $compra['id'] ?>"
                                       class="btn btn-sm btn-primary"
                                       title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= APP_URL ?>/compras/editar/<?= $compra['id'] ?>"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
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
                        <li class="page-item <?= $paginacion['pagina_actual'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $paginacion['pagina_actual'] - 1 ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?>">
                                Anterior
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++): ?>
                            <li class="page-item <?= $i == $paginacion['pagina_actual'] ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $paginacion['pagina_actual'] >= $paginacion['total_paginas'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $paginacion['pagina_actual'] + 1 ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?>">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
