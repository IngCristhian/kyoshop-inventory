<!-- Filtros y Búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/productos" class="row g-3">
            <div class="col-md-4">
                <label for="busqueda" class="form-label">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="busqueda" name="busqueda" 
                           placeholder="Nombre, descripción o código..." 
                           value="<?= htmlspecialchars($filtros['busqueda']) ?>">
                </div>
            </div>
            
            <div class="col-md-2">
                <label for="categoria" class="form-label">Categoría</label>
                <select class="form-select" id="categoria" name="categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria) ?>"
                                <?= $filtros['categoria'] === $categoria ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Todos los tipos</option>
                    <option value="Niño" <?= ($filtros['tipo'] ?? '') === 'Niño' ? 'selected' : '' ?>>Niño</option>
                    <option value="Mujer" <?= ($filtros['tipo'] ?? '') === 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                    <option value="Hombre" <?= ($filtros['tipo'] ?? '') === 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <select class="form-select" id="ubicacion" name="ubicacion">
                    <option value="">Todas</option>
                    <option value="Medellín" <?= ($filtros['ubicacion'] ?? '') === 'Medellín' ? 'selected' : '' ?>>Medellín</option>
                    <option value="Bogotá" <?= ($filtros['ubicacion'] ?? '') === 'Bogotá' ? 'selected' : '' ?>>Bogotá</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="stock_bajo" class="form-label">Filtros</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="stock_bajo" name="stock_bajo" value="1"
                           <?= $filtros['stock_bajo'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="stock_bajo">
                        Solo stock bajo
                    </label>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Información de resultados -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Mostrando <?= count($productos) ?> de <?= $paginacion['total_productos'] ?> productos
        <?php if ($paginacion['total_paginas'] > 1): ?>
            (Página <?= $paginacion['pagina_actual'] ?> de <?= $paginacion['total_paginas'] ?>)
        <?php endif; ?>
    </div>
    
    <?php if (!empty($filtros['busqueda']) || !empty($filtros['categoria']) || !empty($filtros['tipo']) || !empty($filtros['ubicacion']) || $filtros['stock_bajo']): ?>
        <a href="<?= APP_URL ?>/productos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-circle"></i> Limpiar filtros
        </a>
    <?php endif; ?>
</div>

<!-- Lista de Productos -->
<?php if (empty($productos)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">No se encontraron productos</h4>
            <p class="text-muted">
                <?php if (!empty($filtros['busqueda']) || !empty($filtros['categoria']) || !empty($filtros['tipo']) || !empty($filtros['ubicacion']) || $filtros['stock_bajo']): ?>
                    Intenta ajustar los filtros de búsqueda.
                <?php else: ?>
                    ¡Comienza agregando tu primer producto!
                <?php endif; ?>
            </p>
            <a href="<?= APP_URL ?>/productos/crear" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Crear Producto
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($productos as $producto): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <!-- Imagen del producto -->
                    <div class="position-relative">
                        <?php if ($producto['imagen'] || $producto['imagen_modelo']): ?>
                            <?php
                            // Usar imagen principal si existe, sino la de modelo
                            $imagenPrincipalMostrar = $producto['imagen'] ?? $producto['imagen_modelo'];
                            ?>
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='200'%3E%3Crect fill='%23f0f0f0' width='400' height='200'/%3E%3C/svg%3E"
                                 data-src="<?= APP_URL ?>/uploads/<?= $imagenPrincipalMostrar ?>"
                                 class="card-img-top lazy-load"
                                 style="height: 200px; object-fit: cover; cursor: pointer;"
                                 onclick="abrirVistaPrevia(
                                     <?= $producto['id'] ?>,
                                     <?= $producto['imagen'] ? '\'' . APP_URL . '/uploads/' . $producto['imagen'] . '\'' : 'null' ?>,
                                     <?= $producto['imagen_modelo'] ? '\'' . APP_URL . '/uploads/' . $producto['imagen_modelo'] . '\'' : 'null' ?>,
                                     '<?= htmlspecialchars($producto['nombre']) ?>',
                                     '<?= htmlspecialchars($producto['codigo_producto']) ?>',
                                     '<?= formatPrice($producto['precio']) ?>',
                                     '<?= $producto['stock'] ?>',
                                     '<?= htmlspecialchars($producto['categoria']) ?>',
                                     '<?= htmlspecialchars($producto['talla'] ?? '') ?>',
                                     '<?= htmlspecialchars($producto['color'] ?? '') ?>'
                                 )">
                            <!-- Badge si tiene ambas imágenes -->
                            <?php if ($producto['imagen'] && $producto['imagen_modelo']): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-info"><i class="bi bi-images"></i> 2 fotos</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                 style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de stock -->
                        <div class="position-absolute top-0 end-0 m-2">
                            <?php if ($producto['stock'] == 0): ?>
                                <span class="badge bg-danger">Sin Stock</span>
                            <?php elseif ($producto['stock'] <= 5): ?>
                                <span class="badge bg-warning">Stock Bajo</span>
                            <?php else: ?>
                                <span class="badge bg-success">En Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($producto['nombre']) ?></h5>
                            <small class="text-muted"><?= htmlspecialchars($producto['codigo_producto']) ?></small>
                        </div>
                        
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars(substr($producto['descripcion'], 0, 100)) ?>
                            <?= strlen($producto['descripcion']) > 100 ? '...' : '' ?>
                        </p>
                        
                        <div class="row g-0 mb-3">
                            <div class="col-4">
                                <small class="text-muted">Categoría:</small><br>
                                <span class="badge bg-primary"><?= htmlspecialchars($producto['categoria']) ?></span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Tipo:</small><br>
                                <span class="badge bg-success"><?= htmlspecialchars($producto['tipo'] ?? 'Niño') ?></span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Stock:</small><br>
                                <strong class="text-<?= $producto['stock'] <= 5 ? ($producto['stock'] == 0 ? 'danger' : 'warning') : 'success' ?>">
                                    <?= $producto['stock'] ?> unidades
                                </strong>
                            </div>
                        </div>

                        <?php if ($producto['talla'] || $producto['color'] || (isset($producto['ubicacion']) && $producto['ubicacion'])): ?>
                            <div class="row g-0 mb-3">
                                <?php if ($producto['talla']): ?>
                                    <div class="col-4">
                                        <small class="text-muted">Talla:</small><br>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($producto['talla']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($producto['color']): ?>
                                    <div class="col-4">
                                        <small class="text-muted">Color:</small><br>
                                        <span class="badge bg-info"><?= htmlspecialchars($producto['color']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($producto['ubicacion']) && $producto['ubicacion']): ?>
                                    <div class="col-4">
                                        <small class="text-muted">Ubicación:</small><br>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($producto['ubicacion']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <h4 class="text-primary mb-0"><?= formatPrice($producto['precio']) ?></h4>
                            
                            <div class="btn-group">
                                <a href="<?= APP_URL ?>/productos/editar/<?= $producto['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="confirmarEliminacionProducto(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> 
                            Actualizado: <?= date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Paginación -->
    <?php if ($paginacion['total_paginas'] > 1): ?>
        <nav aria-label="Paginación de productos" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($paginacion['pagina_actual'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= APP_URL ?>/productos?pagina=<?= $paginacion['pagina_actual'] - 1 ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?><?= !empty($filtros['tipo']) ? '&tipo=' . urlencode($filtros['tipo']) : '' ?><?= !empty($filtros['ubicacion']) ? '&ubicacion=' . urlencode($filtros['ubicacion']) : '' ?><?= $filtros['stock_bajo'] ? '&stock_bajo=1' : '' ?>">
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
                        <a class="page-link" href="<?= APP_URL ?>/productos?pagina=<?= $i ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?><?= !empty($filtros['tipo']) ? '&tipo=' . urlencode($filtros['tipo']) : '' ?><?= !empty($filtros['ubicacion']) ? '&ubicacion=' . urlencode($filtros['ubicacion']) : '' ?><?= $filtros['stock_bajo'] ? '&stock_bajo=1' : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= APP_URL ?>/productos?pagina=<?= $paginacion['pagina_actual'] + 1 ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?><?= !empty($filtros['tipo']) ? '&tipo=' . urlencode($filtros['tipo']) : '' ?><?= !empty($filtros['ubicacion']) ? '&ubicacion=' . urlencode($filtros['ubicacion']) : '' ?><?= $filtros['stock_bajo'] ? '&stock_bajo=1' : '' ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<!-- Modal de Vista Previa de Imagen -->
<div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title" id="modalVistaPreviaLabel">
                    <i class="bi bi-eye"></i> Vista Previa del Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="position-relative border rounded p-2 bg-light">
                            <!-- Imagen actual -->
                            <img id="imagenPreview" src="" class="img-fluid rounded shadow-sm" alt="Imagen del producto" style="max-height: 400px; width: 100%; object-fit: contain;">

                            <!-- Controles de navegación -->
                            <div id="galeriaControles" style="display: none;">
                                <!-- Flecha izquierda -->
                                <button type="button" id="btnPrevImagen" class="btn btn-dark btn-sm position-absolute top-50 start-0 translate-middle-y ms-2" style="opacity: 0.7; z-index: 10;">
                                    <i class="bi bi-chevron-left"></i>
                                </button>

                                <!-- Flecha derecha -->
                                <button type="button" id="btnNextImagen" class="btn btn-dark btn-sm position-absolute top-50 end-0 translate-middle-y me-2" style="opacity: 0.7; z-index: 10;">
                                    <i class="bi bi-chevron-right"></i>
                                </button>

                                <!-- Indicador de posición -->
                                <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2" style="z-index: 10;">
                                    <span class="badge bg-dark" id="indicadorImagen" style="opacity: 0.8;">1 / 2</span>
                                </div>

                                <!-- Etiquetas de tipo de imagen -->
                                <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                    <span class="badge bg-primary" id="etiquetaImagen">Producto Solo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h4 id="nombrePreview" class="mb-3 text-dark fw-bold"></h4>

                        <div class="mb-3 p-2 bg-light rounded">
                            <small class="text-muted d-block mb-1"><i class="bi bi-upc-scan"></i> Código:</small>
                            <p id="codigoPreview" class="mb-0 text-dark fw-semibold"></p>
                        </div>

                        <div class="mb-3 p-2 rounded border" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-color: #667eea !important;">
                            <small class="text-muted d-block mb-1"><i class="bi bi-tag"></i> Precio:</small>
                            <h5 id="precioPreview" class="mb-0 fw-bold" style="color: #667eea;"></h5>
                        </div>

                        <div class="mb-3 p-2 bg-light rounded">
                            <small class="text-muted d-block mb-1"><i class="bi bi-box-seam"></i> Stock:</small>
                            <p id="stockPreview" class="mb-0 text-dark fw-semibold"></p>
                        </div>

                        <div class="mb-3 p-2 bg-light rounded">
                            <small class="text-muted d-block mb-1"><i class="bi bi-folder"></i> Categoría:</small>
                            <p id="categoriaPreview" class="mb-0 text-dark fw-semibold"></p>
                        </div>

                        <!-- Selector de Variantes (cuando tiene variantes consolidadas) -->
                        <div id="variantesContainer" style="display: none;">
                            <div class="mb-3 p-3 border rounded bg-light">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-collection"></i> Selecciona una variante:
                                </label>
                                <div id="variantesSelector" class="d-flex flex-wrap gap-2">
                                    <!-- Las variantes se cargarán dinámicamente aquí -->
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle"></i> El stock se actualiza según la variante seleccionada
                                </small>
                            </div>
                        </div>

                        <!-- Detalles originales (cuando NO tiene variantes) -->
                        <div class="row g-2" id="detallesExtras">
                            <div class="col-6" id="tallaContainer" style="display: none;">
                                <div class="p-2 bg-light rounded h-100">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-rulers"></i> Talla:</small>
                                    <p id="tallaPreview" class="mb-0 text-dark fw-semibold"></p>
                                </div>
                            </div>
                            <div class="col-6" id="colorContainer" style="display: none;">
                                <div class="p-2 bg-light rounded h-100">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-palette"></i> Color:</small>
                                    <p id="colorPreview" class="mb-0 text-dark fw-semibold"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales para la galería
let imagenesGaleria = [];
let imagenActualIndex = 0;

function abrirVistaPrevia(productoId, imagenUrl, imagenModeloUrl, nombre, codigo, precio, stock, categoria, talla, color) {
    // Resetear galería
    imagenesGaleria = [];
    imagenActualIndex = 0;

    // Agregar imágenes disponibles a la galería
    if (imagenUrl && imagenUrl.trim() !== '') {
        imagenesGaleria.push({
            url: imagenUrl,
            etiqueta: 'Producto Solo'
        });
    }

    if (imagenModeloUrl && imagenModeloUrl.trim() !== '') {
        imagenesGaleria.push({
            url: imagenModeloUrl,
            etiqueta: 'Con Modelo'
        });
    }

    // Actualizar información del producto
    document.getElementById('nombrePreview').textContent = nombre;
    document.getElementById('codigoPreview').textContent = codigo;
    document.getElementById('precioPreview').textContent = precio;
    document.getElementById('stockPreview').textContent = stock + ' unidades';
    document.getElementById('categoriaPreview').textContent = categoria;

    // Ocultar variantes por defecto
    document.getElementById('variantesContainer').style.display = 'none';
    document.getElementById('detallesExtras').style.display = 'flex';

    // Mostrar talla si existe
    if (talla && talla.trim() !== '') {
        document.getElementById('tallaPreview').textContent = talla;
        document.getElementById('tallaContainer').style.display = 'block';
    } else {
        document.getElementById('tallaContainer').style.display = 'none';
    }

    // Mostrar color si existe
    if (color && color.trim() !== '') {
        document.getElementById('colorPreview').textContent = color;
        document.getElementById('colorContainer').style.display = 'block';
    } else {
        document.getElementById('colorContainer').style.display = 'none';
    }

    // Mostrar u ocultar controles de galería
    if (imagenesGaleria.length > 1) {
        document.getElementById('galeriaControles').style.display = 'block';
        mostrarImagenGaleria(0);
    } else if (imagenesGaleria.length === 1) {
        document.getElementById('galeriaControles').style.display = 'none';
        document.getElementById('imagenPreview').src = imagenesGaleria[0].url;
    }

    // Cargar variantes si existen
    cargarVariantes(productoId, stock);

    // Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
    modal.show();
}

// Función para cargar variantes del producto
function cargarVariantes(productoId, stockOriginal) {
    fetch('<?= APP_URL ?>/productos/variantes/' + productoId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.tieneVariantes && data.variantes.length > 0) {
                // Mostrar selector de variantes
                document.getElementById('variantesContainer').style.display = 'block';
                document.getElementById('detallesExtras').style.display = 'none';

                // Limpiar selector
                const selector = document.getElementById('variantesSelector');
                selector.innerHTML = '';

                // Crear botones para cada variante
                data.variantes.forEach((variante, index) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary variante-btn';
                    if (index === 0) btn.classList.add('active');

                    const tallaText = variante.talla || 'S/T';
                    const colorText = variante.color ? ` - ${variante.color}` : '';

                    btn.innerHTML = `
                        <strong>${tallaText}</strong>${colorText}<br>
                        <small>${variante.stock} unidades</small>
                    `;

                    btn.onclick = () => seleccionarVariante(variante, data.variantes);

                    selector.appendChild(btn);
                });

                // Seleccionar primera variante por defecto
                if (data.variantes.length > 0) {
                    seleccionarVariante(data.variantes[0], data.variantes);
                }
            }
        })
        .catch(error => {
            console.error('Error cargando variantes:', error);
        });
}

// Función para seleccionar una variante
function seleccionarVariante(variante, todasVariantes) {
    // Actualizar stock
    document.getElementById('stockPreview').textContent = variante.stock + ' unidades';

    // Resaltar botón seleccionado
    document.querySelectorAll('.variante-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.variante-btn').classList.add('active');
}

function mostrarImagenGaleria(index) {
    if (imagenesGaleria.length === 0) return;

    // Asegurar que el índice esté en rango
    imagenActualIndex = (index + imagenesGaleria.length) % imagenesGaleria.length;

    // Actualizar imagen
    document.getElementById('imagenPreview').src = imagenesGaleria[imagenActualIndex].url;

    // Actualizar indicador
    document.getElementById('indicadorImagen').textContent = `${imagenActualIndex + 1} / ${imagenesGaleria.length}`;

    // Actualizar etiqueta
    document.getElementById('etiquetaImagen').textContent = imagenesGaleria[imagenActualIndex].etiqueta;
}

function siguienteImagen() {
    mostrarImagenGaleria(imagenActualIndex + 1);
}

function anteriorImagen() {
    mostrarImagenGaleria(imagenActualIndex - 1);
}

// Event listeners para los botones de navegación
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnNextImagen')?.addEventListener('click', siguienteImagen);
    document.getElementById('btnPrevImagen')?.addEventListener('click', anteriorImagen);

    // Soporte para teclas de flecha
    document.getElementById('modalVistaPrevia')?.addEventListener('keydown', function(e) {
        if (imagenesGaleria.length > 1) {
            if (e.key === 'ArrowRight') {
                siguienteImagen();
            } else if (e.key === 'ArrowLeft') {
                anteriorImagen();
            }
        }
    });
});

// Función ESPECÍFICA para eliminar PRODUCTOS
function confirmarEliminacionProducto(id, nombre) {
    console.log('=== CONFIRMACIÓN DE ELIMINACIÓN DE PRODUCTO ===');
    console.log('Producto ID:', id);
    console.log('Producto Nombre:', nombre);
    console.log('Módulo: PRODUCTOS');

    if (confirm(`¿Estás seguro de que deseas eliminar el producto "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        console.log('Usuario confirmó eliminación de PRODUCTO');

        const appUrl = '<?= APP_URL ?>';
        const url = `${appUrl}/productos/eliminar/${id}`;

        console.log('URL de eliminación:', url);
        console.log('Esperado: debe contener "/productos/eliminar/"');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= generateCSRFToken() ?>';
        form.appendChild(csrfToken);

        console.log('CSRF Token:', csrfToken.value);
        console.log('Formulario creado para PRODUCTO, enviando...');

        document.body.appendChild(form);
        form.submit();
    } else {
        console.log('Usuario canceló eliminación de PRODUCTO');
    }
}

// Lazy Loading de imágenes con Intersection Observer
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img.lazy-load');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const realSrc = img.getAttribute('data-src');

                    if (realSrc) {
                        img.src = realSrc;
                        img.classList.remove('lazy-load');
                        imageObserver.unobserve(img);
                    }
                }
            });
        }, {
            rootMargin: '50px' // Cargar imágenes 50px antes de que sean visibles
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback para navegadores sin soporte
        lazyImages.forEach(img => {
            img.src = img.getAttribute('data-src');
        });
    }
});
</script>

<style>
/* Estilos para selector de variantes */
.variante-btn {
    min-width: 80px;
    font-size: 0.9rem;
    padding: 10px 15px;
    border-width: 2px;
    transition: all 0.2s ease;
}

.variante-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.variante-btn.active {
    background-color: #667eea;
    border-color: #667eea;
    color: white;
    font-weight: bold;
}

.variante-btn small {
    display: block;
    font-size: 0.75rem;
    margin-top: 2px;
}

#variantesContainer .border {
    border-color: #667eea !important;
}
</style>