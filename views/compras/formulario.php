<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-<?= isset($compra) ? 'pencil' : 'plus-lg' ?>"></i>
                    <?= isset($compra) ? 'Editar Compra' : 'Nueva Compra de Insumos' ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($_SESSION['errores'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['errores'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['errores']); ?>
                <?php endif; ?>

                <form method="POST"
                      action="<?= APP_URL ?>/compras/<?= isset($compra) ? 'actualizar/' . $compra['id'] : 'guardar' ?>"
                      enctype="multipart/form-data"
                      id="formCompra">

                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Descripción *</label>
                            <textarea class="form-control"
                                      name="descripcion"
                                      rows="2"
                                      required><?= htmlspecialchars($compra['descripcion'] ?? '') ?></textarea>
                            <small class="text-muted">Ej: Bolsas de papel kraft tamaño mediano</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoría *</label>
                            <select class="form-select" name="categoria_insumo" required>
                                <option value="bolsas" <?= ($compra['categoria_insumo'] ?? '') === 'bolsas' ? 'selected' : '' ?>>Bolsas</option>
                                <option value="etiquetas" <?= ($compra['categoria_insumo'] ?? '') === 'etiquetas' ? 'selected' : '' ?>>Etiquetas</option>
                                <option value="cajas" <?= ($compra['categoria_insumo'] ?? '') === 'cajas' ? 'selected' : '' ?>>Cajas</option>
                                <option value="embalaje" <?= ($compra['categoria_insumo'] ?? '') === 'embalaje' ? 'selected' : '' ?>>Embalaje</option>
                                <option value="publicidad" <?= ($compra['categoria_insumo'] ?? '') === 'publicidad' ? 'selected' : '' ?>>Publicidad</option>
                                <option value="otros" <?= ($compra['categoria_insumo'] ?? 'otros') === 'otros' ? 'selected' : '' ?>>Otros</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Proveedor</label>
                            <input type="text"
                                   class="form-control"
                                   name="proveedor"
                                   value="<?= htmlspecialchars($compra['proveedor'] ?? '') ?>"
                                   placeholder="Nombre del proveedor">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Método de Pago *</label>
                            <select class="form-select" name="metodo_pago" required>
                                <option value="transferencia" <?= ($compra['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                                <option value="efectivo" <?= ($compra['metodo_pago'] ?? '') === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                <option value="tarjeta" <?= ($compra['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                                <option value="credito" <?= ($compra['metodo_pago'] ?? '') === 'credito' ? 'selected' : '' ?>>Crédito</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cantidad</label>
                            <input type="number"
                                   class="form-control"
                                   name="cantidad"
                                   id="cantidad"
                                   value="<?= $compra['cantidad'] ?? '' ?>"
                                   placeholder="Ej: 100">
                            <small class="text-muted">Opcional</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Precio Unitario</label>
                            <input type="number"
                                   class="form-control"
                                   name="precio_unitario"
                                   id="precio_unitario"
                                   step="0.01"
                                   value="<?= $compra['precio_unitario'] ?? '' ?>"
                                   placeholder="Ej: 500">
                            <small class="text-muted">Opcional</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total *</label>
                            <input type="number"
                                   class="form-control"
                                   name="total"
                                   id="total"
                                   step="0.01"
                                   value="<?= $compra['total'] ?? '' ?>"
                                   required
                                   placeholder="Ej: 50000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Comprobante (PDF o Imagen)</label>
                        <input type="file"
                               class="form-control"
                               name="comprobante"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Adjunta la factura o recibo (opcional, máx 5MB)</small>
                        <?php if (!empty($compra['comprobante'])): ?>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="bi bi-file-earmark-check"></i> Archivo actual: <?= htmlspecialchars($compra['comprobante']) ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea class="form-control"
                                  name="observaciones"
                                  rows="3"><?= htmlspecialchars($compra['observaciones'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= APP_URL ?>/compras" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?= isset($compra) ? 'Actualizar' : 'Guardar' ?> Compra
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calcular total cuando se ingresa cantidad y precio unitario
document.addEventListener('DOMContentLoaded', function() {
    const cantidad = document.getElementById('cantidad');
    const precioUnitario = document.getElementById('precio_unitario');
    const total = document.getElementById('total');

    function calcularTotal() {
        const cant = parseFloat(cantidad.value) || 0;
        const precio = parseFloat(precioUnitario.value) || 0;
        if (cant > 0 && precio > 0) {
            total.value = (cant * precio).toFixed(2);
        }
    }

    cantidad.addEventListener('input', calcularTotal);
    precioUnitario.addEventListener('input', calcularTotal);
});
</script>
