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
                            <label for="tipo" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Niño" <?= ($datos_antiguos['tipo'] ?? $producto['tipo'] ?? '') === 'Niño' ? 'selected' : '' ?>>Niño</option>
                                <option value="Mujer" <?= ($datos_antiguos['tipo'] ?? $producto['tipo'] ?? '') === 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                                <option value="Hombre" <?= ($datos_antiguos['tipo'] ?? $producto['tipo'] ?? '') === 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                            </select>
                            <small class="text-muted">Clasificación para armado de combos</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="ubicacion" class="form-label">Ubicación *</label>
                            <select class="form-select" id="ubicacion" name="ubicacion" required>
                                <option value="">Seleccione una ciudad</option>
                                <option value="Medellín" <?= ($datos_antiguos['ubicacion'] ?? $producto['ubicacion'] ?? '') === 'Medellín' ? 'selected' : '' ?>>Medellín</option>
                                <option value="Bogotá" <?= ($datos_antiguos['ubicacion'] ?? $producto['ubicacion'] ?? '') === 'Bogotá' ? 'selected' : '' ?>>Bogotá</option>
                            </select>
                            <small class="text-muted">Ciudad donde está almacenada la mercancía</small>
                        </div>
                    </div>

                    <?php if ($accion === 'crear'): ?>
                        <!-- Opción de crear variantes (solo en creación) -->
                        <div class="alert alert-info mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="crear_variantes" name="crear_variantes" value="1">
                                <label class="form-check-label" for="crear_variantes">
                                    <strong>¿Crear múltiples variantes?</strong>
                                    <small class="d-block text-muted">Activa esta opción si tienes el mismo producto en diferentes colores y/o tallas</small>
                                </label>
                            </div>
                        </div>

                        <!-- Campos simples (se ocultan si selecciona variantes) -->
                        <div id="campos-simples">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="text" class="form-control" id="color" name="color"
                                           value="<?= htmlspecialchars($datos_antiguos['color'] ?? '') ?>"
                                           maxlength="50" placeholder="Rojo, Azul, Negro...">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="talla" class="form-label">Talla</label>
                                    <input type="text" class="form-control" id="talla" name="talla"
                                           value="<?= htmlspecialchars($datos_antiguos['talla'] ?? '') ?>"
                                           maxlength="50" placeholder="XS, S, M, L, XL, 32, 34...">
                                </div>
                            </div>
                        </div>

                        <!-- Área de variantes (se muestra si selecciona variantes) -->
                        <div id="area-variantes" style="display: none;">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-grid"></i> Variantes del Producto
                            </h6>

                            <div id="variantes-container">
                                <!-- Las variantes se agregarán dinámicamente aquí -->
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarVariante">
                                <i class="bi bi-plus-circle"></i> Agregar Variante
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- En modo edición, campos normales -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color"
                                       value="<?= htmlspecialchars($datos_antiguos['color'] ?? $producto['color'] ?? '') ?>"
                                       maxlength="50" placeholder="Rojo, Azul, Negro...">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="talla" class="form-label">Talla</label>
                                <input type="text" class="form-control" id="talla" name="talla"
                                       value="<?= htmlspecialchars($datos_antiguos['talla'] ?? $producto['talla'] ?? '') ?>"
                                       maxlength="50" placeholder="XS, S, M, L, XL, 32, 34...">
                            </div>
                        </div>
                    <?php endif; ?>
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
    <?php if ($accion === 'crear'): ?>
    // Gestión de variantes
    let contadorVariantes = 0;
    const checkboxVariantes = document.getElementById('crear_variantes');
    const camposSimples = document.getElementById('campos-simples');
    const areaVariantes = document.getElementById('area-variantes');
    const stockInput = document.getElementById('stock');
    const imagenInput = document.getElementById('imagen');

    // Alternar entre modo simple y modo variantes
    checkboxVariantes.addEventListener('change', function() {
        if (this.checked) {
            camposSimples.style.display = 'none';
            areaVariantes.style.display = 'block';
            stockInput.disabled = true;
            stockInput.value = 0;
            // NO deshabilitar imagen principal - se usará como default para variantes

            // Agregar primera variante automáticamente
            if (contadorVariantes === 0) {
                agregarVariante();
            }
        } else {
            camposSimples.style.display = 'block';
            areaVariantes.style.display = 'none';
            stockInput.disabled = false;

            // Limpiar variantes
            document.getElementById('variantes-container').innerHTML = '';
            contadorVariantes = 0;
        }
    });

    // Agregar variante
    document.getElementById('btnAgregarVariante').addEventListener('click', agregarVariante);

    function agregarVariante(color = '', talla = '', stock = '', imagen = null) {
        const container = document.getElementById('variantes-container');
        const id = contadorVariantes++;

        const div = document.createElement('div');
        div.className = 'card mb-3 variante-item';
        div.id = `variante-${id}`;

        div.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Variante ${id + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarVariante(${id})">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Color *</label>
                        <input type="text" class="form-control" name="variantes[${id}][color]"
                               value="${color}" required maxlength="50" placeholder="Rojo, Azul...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Talla *</label>
                        <input type="text" class="form-control" name="variantes[${id}][talla]"
                               value="${talla}" required maxlength="50" placeholder="S, M, L...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock *</label>
                        <input type="number" class="form-control" name="variantes[${id}][stock]"
                               value="${stock}" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Imagen</label>
                        <input type="file" class="form-control imagen-variante" name="variantes[${id}][imagen]"
                               accept="image/*" onchange="previewVarianteImage(this, ${id})">
                        <small class="text-muted d-block">Opcional. Si no subes imagen, usará la imagen principal</small>
                        <div id="preview-variante-${id}" class="mt-2"></div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(div);
    }

    function eliminarVariante(id) {
        const elemento = document.getElementById(`variante-${id}`);
        if (elemento) {
            if (confirm('¿Eliminar esta variante?')) {
                elemento.remove();
            }
        }
    }

    function previewVarianteImage(input, varianteId) {
        const preview = document.getElementById(`preview-variante-${varianteId}`);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 80px;">`;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    <?php endif; ?>

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

    // Auto-generar código de producto (solo en modo simple)
    <?php if ($accion === 'crear'): ?>
    const categoriaInput = document.getElementById('categoria');
    const colorInputSimple = document.getElementById('color');

    if (colorInputSimple) {
        categoriaInput.addEventListener('blur', generarCodigo);
        colorInputSimple.addEventListener('blur', generarCodigo);
    }
    <?php else: ?>
    document.getElementById('categoria').addEventListener('blur', generarCodigo);
    document.getElementById('color').addEventListener('blur', generarCodigo);
    <?php endif; ?>

    function generarCodigo() {
        const codigoInput = document.getElementById('codigo_producto');
        const colorInput = document.getElementById('color');

        // Solo generar si el campo está vacío y no está en modo variantes
        if (codigoInput.value.trim() === '' && colorInput && !colorInput.disabled) {
            const categoria = document.getElementById('categoria').value.trim();
            const color = colorInput.value.trim();

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
        <?php if ($accion === 'crear'): ?>
        const crearVariantes = document.getElementById('crear_variantes').checked;

        if (crearVariantes) {
            const variantes = document.querySelectorAll('.variante-item');
            if (variantes.length === 0) {
                e.preventDefault();
                alert('Debes agregar al menos una variante');
                return;
            }
        } else {
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
        }
        <?php else: ?>
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

        if (!confirm('¿Estás seguro de que deseas actualizar este producto?')) {
            e.preventDefault();
            return;
        }
        <?php endif; ?>
    });
</script>