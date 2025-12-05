<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-white mb-0">
                        <i class="bi bi-receipt"></i> Compra #<?= htmlspecialchars($compra['numero_compra']) ?>
                    </h5>
                    <div>
                        <a href="<?= APP_URL ?>/compras/editar/<?= $compra['id'] ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <a href="<?= APP_URL ?>/compras" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted">Información General</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-bold">Número:</td>
                                <td><?= htmlspecialchars($compra['numero_compra']) ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Fecha:</td>
                                <td><?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Categoría:</td>
                                <td><span class="badge bg-secondary"><?= ucfirst(htmlspecialchars($compra['categoria_insumo'])) ?></span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Registrado por:</td>
                                <td><?= htmlspecialchars($compra['usuario_nombre']) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted">Detalles de Pago</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-bold">Método de Pago:</td>
                                <td><span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $compra['metodo_pago'])) ?></span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Proveedor:</td>
                                <td><?= htmlspecialchars($compra['proveedor'] ?? 'No especificado') ?></td>
                            </tr>
                            <?php if (!empty($compra['cantidad'])): ?>
                            <tr>
                                <td class="fw-bold">Cantidad:</td>
                                <td><?= number_format($compra['cantidad']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($compra['precio_unitario'])): ?>
                            <tr>
                                <td class="fw-bold">Precio Unitario:</td>
                                <td>$<?= number_format($compra['precio_unitario'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-2">Descripción</h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($compra['descripcion'])) ?></p>
                    </div>
                </div>

                <?php if (!empty($compra['observaciones'])): ?>
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-muted mb-2">Observaciones</h6>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($compra['observaciones'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($compra['comprobante'])): ?>
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-muted mb-2">Comprobante</h6>
                            <a href="<?= APP_URL ?>/uploads/comprobantes/<?= htmlspecialchars($compra['comprobante']) ?>"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf"></i> Ver Comprobante
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h3 class="text-danger mb-0">
                            Total: $<?= number_format($compra['total'], 0, ',', '.') ?>
                        </h3>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <form method="POST" action="<?= APP_URL ?>/compras/eliminar/<?= $compra['id'] ?>" onsubmit="return confirm('¿Está seguro de eliminar esta compra?');" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Eliminar Compra
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
