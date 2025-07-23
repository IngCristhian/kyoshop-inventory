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
            
            <div class="col-md-3">
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
            
            <div class="col-md-3">
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
    
    <?php if (!empty($filtros['busqueda']) || !empty($filtros['categoria']) || $filtros['stock_bajo']): ?>
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
                <?php if (!empty($filtros['busqueda']) || !empty($filtros['categoria']) || $filtros['stock_bajo']): ?>
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
                        <?php if ($producto['imagen']): ?>
                            <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>" 
                                 class="card-img-top" style="height: 200px; object-fit: cover;">
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
                            <div class="col-6">
                                <small class="text-muted">Categoría:</small><br>
                                <span class="badge bg-primary"><?= htmlspecialchars($producto['categoria']) ?></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Stock:</small><br>
                                <strong class="text-<?= $producto['stock'] <= 5 ? ($producto['stock'] == 0 ? 'danger' : 'warning') : 'success' ?>">
                                    <?= $producto['stock'] ?> unidades
                                </strong>
                            </div>
                        </div>
                        
                        <?php if ($producto['talla'] || $producto['color']): ?>
                            <div class="row g-0 mb-3">
                                <?php if ($producto['talla']): ?>
                                    <div class="col-6">
                                        <small class="text-muted">Talla:</small><br>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($producto['talla']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($producto['color']): ?>
                                    <div class="col-6">
                                        <small class="text-muted">Color:</small><br>
                                        <span class="badge bg-info"><?= htmlspecialchars($producto['color']) ?></span>
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
                                        onclick="confirmarEliminacion(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')">
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
                        <a class="page-link" href="<?= APP_URL ?>/productos?pagina=<?= $paginacion['pagina_actual'] - 1 ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?><?= $filtros['stock_bajo'] ? '&stock_bajo=1' : '' ?>">
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
                        <a class="page-link" href="<?= APP_URL ?>/productos?pagina=<?= $i ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?><?= $filtros['stock_bajo'] ? '&stock_bajo=1' : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= APP_URL ?>/productos?pagina=<?= $paginacion['pagina_actual'] + 1 ?><?= !empty($filtros['busqueda']) ? '&busqueda=' . urlencode($filtros['busqueda']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?><?= $filtros['stock_bajo'] ? '&stock_bajo=1' : '' ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>