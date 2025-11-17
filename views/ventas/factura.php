<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= htmlspecialchars($venta['numero_venta']) ?> - KyoShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            background: white;
        }
        .factura {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            border: 2px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="factura">
        <!-- Header con logo y datos de la empresa -->
        <div class="text-center mb-4">
            <h1 class="mb-0" style="color: #667eea;">KyoShop</h1>
            <p class="mb-0">Sistema de Inventario</p>
            <hr style="border-color: #667eea; border-width: 2px;">
        </div>

        <!-- Información de la factura -->
        <div class="row mb-4">
            <div class="col-6">
                <h5>FACTURA DE VENTA</h5>
                <p class="mb-1"><strong>Número:</strong> <?= htmlspecialchars($venta['numero_venta']) ?></p>
                <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></p>
                <p class="mb-1"><strong>Vendedor:</strong> <?= htmlspecialchars($venta['vendedor_nombre']) ?></p>
            </div>
            <div class="col-6 text-end">
                <h6>DATOS DEL CLIENTE</h6>
                <p class="mb-1"><strong><?= htmlspecialchars($venta['cliente_nombre']) ?></strong></p>
                <p class="mb-1">Tel: <?= htmlspecialchars($venta['cliente_telefono']) ?></p>
                <?php if (!empty($venta['cliente_email'])): ?>
                    <p class="mb-1"><?= htmlspecialchars($venta['cliente_email']) ?></p>
                <?php endif; ?>
                <?php if (!empty($venta['cliente_direccion'])): ?>
                    <p class="mb-1"><?= htmlspecialchars($venta['cliente_direccion']) ?></p>
                <?php endif; ?>
                <p class="mb-1"><?= htmlspecialchars($venta['cliente_ciudad']) ?></p>
            </div>
        </div>

        <!-- Tabla de productos -->
        <table class="table table-bordered">
            <thead style="background-color: #667eea; color: white;">
                <tr>
                    <th>Descripción</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venta['items'] as $item): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($item['producto_nombre']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($item['codigo_producto']) ?></small>
                        </td>
                        <td class="text-center"><?= $item['cantidad'] ?></td>
                        <td class="text-end">$<?= number_format($item['precio_unitario'], 0, ',', '.') ?></td>
                        <td class="text-end">$<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                    <td class="text-end"><strong>$<?= number_format($venta['subtotal'], 0, ',', '.') ?></strong></td>
                </tr>
                <?php if ($venta['impuestos'] > 0): ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Impuestos:</strong></td>
                        <td class="text-end"><strong>$<?= number_format($venta['impuestos'], 0, ',', '.') ?></strong></td>
                    </tr>
                <?php endif; ?>
                <tr style="background-color: #f8f9fa;">
                    <td colspan="3" class="text-end"><h5>TOTAL:</h5></td>
                    <td class="text-end"><h5 style="color: #667eea;">$<?= number_format($venta['total'], 0, ',', '.') ?></h5></td>
                </tr>
            </tfoot>
        </table>

        <!-- Información de pago -->
        <div class="row mt-4">
            <div class="col-6">
                <p class="mb-1"><strong>Método de Pago:</strong> <?= ucfirst(str_replace('_', ' ', $venta['metodo_pago'])) ?></p>
                <p class="mb-1"><strong>Estado:</strong>
                    <span class="badge bg-<?= $venta['estado_pago'] === 'pagado' ? 'success' : ($venta['estado_pago'] === 'cancelado' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($venta['estado_pago']) ?>
                    </span>
                </p>
            </div>
        </div>

        <?php if (!empty($venta['observaciones'])): ?>
            <div class="mt-4">
                <p><strong>Observaciones:</strong></p>
                <p><?= nl2br(htmlspecialchars($venta['observaciones'])) ?></p>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="text-center mt-5 pt-4" style="border-top: 1px solid #dee2e6;">
            <p class="text-muted mb-0">Gracias por su compra</p>
            <small class="text-muted">KyoShop - Sistema de Inventario</small>
        </div>

        <!-- Botones de acción -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <a href="<?= APP_URL ?>/ventas/ver/<?= $venta['id'] ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
