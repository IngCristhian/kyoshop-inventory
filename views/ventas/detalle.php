<div class="row">
    <!-- Información Principal -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-white mb-0">
                        <i class="bi bi-receipt"></i> Venta #<?= htmlspecialchars($venta['numero_venta']) ?>
                    </h5>
                    <span class="badge bg-<?= $venta['estado_pago'] === 'pagado' ? 'success' : ($venta['estado_pago'] === 'cancelado' ? 'danger' : 'warning') ?> fs-6">
                        <?= ucfirst($venta['estado_pago']) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Información del Cliente -->
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-person-circle"></i> Información del Cliente
                </h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Nombre:</strong> <?= htmlspecialchars($venta['cliente_nombre']) ?></p>
                        <p class="mb-1"><strong>Teléfono:</strong> <?= htmlspecialchars($venta['cliente_telefono']) ?></p>
                        <?php if (!empty($venta['cliente_email'])): ?>
                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($venta['cliente_email']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($venta['cliente_direccion'])): ?>
                            <p class="mb-1"><strong>Dirección:</strong> <?= htmlspecialchars($venta['cliente_direccion']) ?></p>
                        <?php endif; ?>
                        <p class="mb-1"><strong>Ciudad:</strong> <?= htmlspecialchars($venta['cliente_ciudad']) ?></p>
                    </div>
                </div>

                <!-- Productos -->
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-box-seam"></i> Productos
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($venta['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($item['producto_nombre']) ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($item['codigo_producto']) ?>
                                            <?php if (!empty($item['talla']) || !empty($item['color'])): ?>
                                                - <?= htmlspecialchars($item['talla'] ?? '') ?> <?= htmlspecialchars($item['color'] ?? '') ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="text-center"><?= $item['cantidad'] ?></td>
                                    <td class="text-end">$<?= number_format($item['precio_unitario'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold">$<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end fw-bold">$<?= number_format($venta['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php if ($venta['impuestos'] > 0): ?>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end fw-bold">Impuestos:</td>
                                    <td class="text-end fw-bold">$<?= number_format($venta['impuestos'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="table-success">
                                <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                                <td class="text-end fw-bold fs-5" style="color: #667eea;">
                                    $<?= number_format($venta['total'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Observaciones -->
                <?php if (!empty($venta['observaciones'])): ?>
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-sticky"></i> Observaciones
                    </h6>
                    <div class="alert alert-secondary">
                        <?= nl2br(htmlspecialchars($venta['observaciones'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panel Lateral - Detalles y Acciones -->
    <div class="col-md-4">
        <!-- Detalles de la Venta -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Detalles de la Venta</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong><i class="bi bi-calendar"></i> Fecha:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?>
                </p>
                <p class="mb-2">
                    <strong><i class="bi bi-person-badge"></i> Vendedor:</strong><br>
                    <?= htmlspecialchars($venta['vendedor_nombre']) ?>
                </p>
                <p class="mb-2">
                    <strong><i class="bi bi-credit-card"></i> Método de Pago:</strong><br>
                    <?= ucfirst(str_replace('_', ' ', $venta['metodo_pago'])) ?>
                </p>
                <p class="mb-0">
                    <strong><i class="bi bi-flag"></i> Estado de Pago:</strong><br>
                    <span class="badge bg-<?= $venta['estado_pago'] === 'pagado' ? 'success' : ($venta['estado_pago'] === 'cancelado' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($venta['estado_pago']) ?>
                    </span>
                </p>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Acciones</h6>
            </div>
            <div class="card-body">
                <!-- Cambiar Estado de Pago -->
                <?php if ($venta['estado_pago'] !== 'cancelado'): ?>
                    <form method="POST" action="<?= APP_URL ?>/ventas/actualizarEstadoPago/<?= $venta['id'] ?>" class="mb-3">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <label class="form-label fw-bold">Cambiar Estado de Pago:</label>
                        <div class="input-group">
                            <select class="form-select form-select-sm" name="estado_pago" required>
                                <option value="pendiente" <?= $venta['estado_pago'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="pagado" <?= $venta['estado_pago'] === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </form>

                    <hr>
                <?php endif; ?>

                <!-- Ver Factura -->
                <a href="<?= APP_URL ?>/ventas/factura/<?= $venta['id'] ?>"
                   class="btn btn-success btn-sm w-100 mb-2"
                   target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Ver Factura (PDF)
                </a>

                <!-- Cancelar Venta -->
                <?php if ($venta['estado_pago'] !== 'cancelado'): ?>
                    <button type="button"
                            class="btn btn-danger btn-sm w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCancelarVenta">
                        <i class="bi bi-x-circle"></i> Cancelar Venta
                    </button>
                <?php else: ?>
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Venta Cancelada</strong><br>
                        El stock ha sido devuelto.
                    </div>
                <?php endif; ?>

                <hr>

                <!-- Volver -->
                <a href="<?= APP_URL ?>/ventas" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancelar Venta -->
<div class="modal fade" id="modalCancelarVenta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Cancelar Venta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/ventas/cancelar/<?= $venta['id'] ?>">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Esta acción devolverá el stock de todos los productos y marcará la venta como cancelada. No se puede revertir.
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label fw-bold">Motivo de Cancelación:</label>
                        <textarea class="form-control"
                                  id="motivo"
                                  name="motivo"
                                  rows="3"
                                  placeholder="Describa el motivo de la cancelación..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Confirmar Cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
