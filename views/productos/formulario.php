<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" 
              action="<?= APP_URL ?>/productos/<?= $accion === 'crear' ? 'guardar' : 'actualizar/' . $producto['id'] ?>">
            
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="row">
                <!-- Información Básica -->
                <div class="col-lg-8">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-info-circle"></i> Información Básica
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($datos_antiguos['nombre'] ?? $producto['nombre'] ?? '') ?>" 
                                   required maxlength="255">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="codigo_producto" class="form-label">Código de Producto</label>
                            <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" 
                                   value="<?= htmlspecialchars($datos_antiguos['codigo_producto'] ?? $producto['codigo_producto'] ?? '') ?>" 
                                   maxlength="100"
                                   placeholder="Se genera automáticamente si se deja vacío">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Descripción detallada del producto..."><?= htmlspecialchars($datos_antiguos['descripcion'] ?? $producto['descripcion'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" 
                                       value="<?= $datos_antiguos['precio'] ?? $producto['precio'] ?? '' ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?= $datos_antiguos['stock'] ?? $producto['stock'] ?? '' ?>" 
                                   min="0" required>
                        </div>
                    </div>
                    
                    <h5 class="text-primary mb-3 mt-4">
                        <i class="bi bi-tags"></i> Características
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <input type="text" class="form-control" id="categoria" name="categoria"
                                   value="<?= htmlspecialchars($datos_antiguos['categoria'] ?? $producto['categoria'] ?? '') ?>"
                                   list="categorias-list" required maxlength="100">
                            <datalist id="categorias-list">
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="talla" class="form-label">Talla</label>
                            <input type="text" class="form-control" id="talla" name="talla"
                                   value="<?= htmlspecialchars($datos_antiguos['talla'] ?? $producto['talla'] ?? '') ?>"
                                   maxlength="50" placeholder="XS, S, M, L, XL, 32, 34...">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" name="color"
                                   value="<?= htmlspecialchars($datos_antiguos['color'] ?? $producto['color'] ?? '') ?>"
                                   maxlength="50" placeholder="Rojo, Azul, Negro...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ubicacion" class="form-label">Ubicación *</label>
                            <select class="form-select" id="ubicacion" name="ubicacion" required>
                                <option value="">Seleccione una ciudad</option>
                                <option value="Medellín" <?= ($datos_antiguos['ubicacion'] ?? $producto['ubicacion'] ?? '') === 'Medellín' ? 'selected' : '' ?>>Medellín</option>
                                <option value="Bogotá" <?= ($datos_antiguos['ubicacion'] ?? $producto['ubicacion'] ?? '') === 'Bogotá' ? 'selected' : '' ?>>Bogotá</option>
                            </select>
                            <small class="text-muted">Ciudad donde está almacenada la mercancía</small>
                        </div>
                    </div>
                </div>
                
                <!-- Imagen del Producto -->
                <div class="col-lg-4">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-image"></i> Imagen del Producto
                    </h5>
                    
                    <div class="card">
                        <div class="card-body text-center">
                            <!-- Preview de imagen actual -->
                            <div id="image-preview" class="mb-3">
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <input type="file" class="form-control" id="imagen" name="imagen" 
                                       accept="image/*" onchange="previewImage(this)">
                            </div>
                            
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Formatos: JPG, PNG, GIF<br>
                                Tamaño máximo: 5MB
                            </small>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <?php if ($accion === 'editar' && !empty($producto)): ?>
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Información del Producto</h6>
                                <small class="text-muted">
                                    <strong>Creado:</strong><br>
                                    <?= date('d/m/Y H:i', strtotime($producto['fecha_creacion'])) ?><br><br>
                                    <strong>Última actualización:</strong><br>
                                    <?= date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="<?= APP_URL ?>/productos" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        
                        <div>
                            <?php if ($accion === 'editar'): ?>
                                <button type="button" class="btn btn-outline-danger me-2" 
                                        onclick="confirmarEliminacion(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-<?= $accion === 'crear' ? 'plus-lg' : 'check-lg' ?>"></i>
                                <?= $accion === 'crear' ? 'Crear Producto' : 'Actualizar Producto' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview de imagen antes de subir
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">`;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Auto-generar código de producto
    document.getElementById('categoria').addEventListener('blur', generarCodigo);
    document.getElementById('color').addEventListener('blur', generarCodigo);
    
    function generarCodigo() {
        const codigoInput = document.getElementById('codigo_producto');
        
        // Solo generar si el campo está vacío
        if (codigoInput.value.trim() === '') {
            const categoria = document.getElementById('categoria').value.trim();
            const color = document.getElementById('color').value.trim();
            
            if (categoria && color) {
                const categoriaCod = categoria.substring(0, 3).toUpperCase();
                const colorCod = color.substring(0, 3).toUpperCase();
                const numero = Math.floor(Math.random() * 900) + 100;
                
                codigoInput.value = `${categoriaCod}-${colorCod}-${numero}`;
            }
        }
    }
    
    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const precio = parseFloat(document.getElementById('precio').value);
        const stock = parseInt(document.getElementById('stock').value);

        if (precio <= 0) {
            e.preventDefault();
            alert('El precio debe ser mayor a 0');
            return;
        }

        if (stock < 0) {
            e.preventDefault();
            alert('El stock no puede ser negativo');
            return;
        }

        // Confirmación al editar producto
        <?php if ($accion !== 'crear'): ?>
        if (!confirm('¿Estás seguro de que deseas actualizar este producto?')) {
            e.preventDefault();
            return;
        }
        <?php endif; ?>
    });
</script>