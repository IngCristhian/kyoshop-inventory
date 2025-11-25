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
                        <div class="card border-info mb-3">
                            <div class="card-body bg-info bg-opacity-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="crear_variantes" name="crear_variantes" value="1">
                                    <label class="form-check-label fw-bold text-dark" for="crear_variantes">
                                        ¿Crear múltiples variantes?
                                        <small class="d-block fw-normal text-dark">Activa esta opción si tienes el mismo producto en diferentes colores y/o tallas</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Campos simples (se ocultan si selecciona variantes) -->
                        <div id="campos-simples">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <select class="form-select" id="color_select" name="color" onchange="toggleColorOtro(this, 'color_otro_input')">
                                        <option value="">Seleccione un color</option>
                                        <optgroup label="Colores Básicos">
                                            <option value="Blanco" <?= ($datos_antiguos['color'] ?? '') === 'Blanco' ? 'selected' : '' ?>>Blanco</option>
                                            <option value="Negro" <?= ($datos_antiguos['color'] ?? '') === 'Negro' ? 'selected' : '' ?>>Negro</option>
                                            <option value="Gris" <?= ($datos_antiguos['color'] ?? '') === 'Gris' ? 'selected' : '' ?>>Gris</option>
                                            <option value="Beige" <?= ($datos_antiguos['color'] ?? '') === 'Beige' ? 'selected' : '' ?>>Beige</option>
                                            <option value="Café" <?= ($datos_antiguos['color'] ?? '') === 'Café' ? 'selected' : '' ?>>Café</option>
                                        </optgroup>
                                        <optgroup label="Colores Vivos">
                                            <option value="Rojo" <?= ($datos_antiguos['color'] ?? '') === 'Rojo' ? 'selected' : '' ?>>Rojo</option>
                                            <option value="Azul" <?= ($datos_antiguos['color'] ?? '') === 'Azul' ? 'selected' : '' ?>>Azul</option>
                                            <option value="Verde" <?= ($datos_antiguos['color'] ?? '') === 'Verde' ? 'selected' : '' ?>>Verde</option>
                                            <option value="Amarillo" <?= ($datos_antiguos['color'] ?? '') === 'Amarillo' ? 'selected' : '' ?>>Amarillo</option>
                                            <option value="Naranja" <?= ($datos_antiguos['color'] ?? '') === 'Naranja' ? 'selected' : '' ?>>Naranja</option>
                                            <option value="Rosa" <?= ($datos_antiguos['color'] ?? '') === 'Rosa' ? 'selected' : '' ?>>Rosa</option>
                                            <option value="Morado" <?= ($datos_antiguos['color'] ?? '') === 'Morado' ? 'selected' : '' ?>>Morado</option>
                                            <option value="Fucsia" <?= ($datos_antiguos['color'] ?? '') === 'Fucsia' ? 'selected' : '' ?>>Fucsia</option>
                                        </optgroup>
                                        <optgroup label="Tonos Especiales">
                                            <option value="Azul Marino" <?= ($datos_antiguos['color'] ?? '') === 'Azul Marino' ? 'selected' : '' ?>>Azul Marino</option>
                                            <option value="Vino Tinto" <?= ($datos_antiguos['color'] ?? '') === 'Vino Tinto' ? 'selected' : '' ?>>Vino Tinto</option>
                                            <option value="Verde Oliva" <?= ($datos_antiguos['color'] ?? '') === 'Verde Oliva' ? 'selected' : '' ?>>Verde Oliva</option>
                                            <option value="Turquesa" <?= ($datos_antiguos['color'] ?? '') === 'Turquesa' ? 'selected' : '' ?>>Turquesa</option>
                                            <option value="Dorado" <?= ($datos_antiguos['color'] ?? '') === 'Dorado' ? 'selected' : '' ?>>Dorado</option>
                                            <option value="Plateado" <?= ($datos_antiguos['color'] ?? '') === 'Plateado' ? 'selected' : '' ?>>Plateado</option>
                                        </optgroup>
                                        <option value="Multicolor" <?= ($datos_antiguos['color'] ?? '') === 'Multicolor' ? 'selected' : '' ?>>Multicolor</option>
                                        <option value="otro">Otro (escribir manualmente)</option>
                                    </select>
                                    <input type="text" class="form-control mt-2" id="color_otro_input" name="color"
                                           placeholder="Escriba el color personalizado"
                                           style="display: none;" maxlength="50">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="talla" class="form-label">Talla</label>
                                    <select class="form-select" id="talla_select" name="talla" onchange="toggleTallaOtro(this, 'talla_otro_input')">
                                        <option value="">Seleccione una talla</option>
                                        <optgroup label="Tallas Letras">
                                            <option value="XS" <?= ($datos_antiguos['talla'] ?? '') === 'XS' ? 'selected' : '' ?>>XS</option>
                                            <option value="S" <?= ($datos_antiguos['talla'] ?? '') === 'S' ? 'selected' : '' ?>>S</option>
                                            <option value="M" <?= ($datos_antiguos['talla'] ?? '') === 'M' ? 'selected' : '' ?>>M</option>
                                            <option value="L" <?= ($datos_antiguos['talla'] ?? '') === 'L' ? 'selected' : '' ?>>L</option>
                                            <option value="XL" <?= ($datos_antiguos['talla'] ?? '') === 'XL' ? 'selected' : '' ?>>XL</option>
                                            <option value="XXL" <?= ($datos_antiguos['talla'] ?? '') === 'XXL' ? 'selected' : '' ?>>XXL</option>
                                        </optgroup>
                                        <optgroup label="Tallas Números">
                                            <option value="28" <?= ($datos_antiguos['talla'] ?? '') === '28' ? 'selected' : '' ?>>28</option>
                                            <option value="30" <?= ($datos_antiguos['talla'] ?? '') === '30' ? 'selected' : '' ?>>30</option>
                                            <option value="32" <?= ($datos_antiguos['talla'] ?? '') === '32' ? 'selected' : '' ?>>32</option>
                                            <option value="34" <?= ($datos_antiguos['talla'] ?? '') === '34' ? 'selected' : '' ?>>34</option>
                                            <option value="36" <?= ($datos_antiguos['talla'] ?? '') === '36' ? 'selected' : '' ?>>36</option>
                                            <option value="38" <?= ($datos_antiguos['talla'] ?? '') === '38' ? 'selected' : '' ?>>38</option>
                                            <option value="40" <?= ($datos_antiguos['talla'] ?? '') === '40' ? 'selected' : '' ?>>40</option>
                                            <option value="42" <?= ($datos_antiguos['talla'] ?? '') === '42' ? 'selected' : '' ?>>42</option>
                                        </optgroup>
                                        <optgroup label="Tallas Infantiles">
                                            <option value="2" <?= ($datos_antiguos['talla'] ?? '') === '2' ? 'selected' : '' ?>>2</option>
                                            <option value="4" <?= ($datos_antiguos['talla'] ?? '') === '4' ? 'selected' : '' ?>>4</option>
                                            <option value="6" <?= ($datos_antiguos['talla'] ?? '') === '6' ? 'selected' : '' ?>>6</option>
                                            <option value="8" <?= ($datos_antiguos['talla'] ?? '') === '8' ? 'selected' : '' ?>>8</option>
                                            <option value="10" <?= ($datos_antiguos['talla'] ?? '') === '10' ? 'selected' : '' ?>>10</option>
                                            <option value="12" <?= ($datos_antiguos['talla'] ?? '') === '12' ? 'selected' : '' ?>>12</option>
                                            <option value="14" <?= ($datos_antiguos['talla'] ?? '') === '14' ? 'selected' : '' ?>>14</option>
                                            <option value="16" <?= ($datos_antiguos['talla'] ?? '') === '16' ? 'selected' : '' ?>>16</option>
                                        </optgroup>
                                        <option value="Única" <?= ($datos_antiguos['talla'] ?? '') === 'Única' ? 'selected' : '' ?>>Única</option>
                                        <option value="otro">Otro (escribir manualmente)</option>
                                    </select>
                                    <input type="text" class="form-control mt-2" id="talla_otro_input" name="talla"
                                           placeholder="Escriba la talla personalizada"
                                           style="display: none;" maxlength="50">
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
                                <?php
                                    $color_actual = $datos_antiguos['color'] ?? $producto['color'] ?? '';
                                    $colores_predefinidos = ['Blanco', 'Negro', 'Gris', 'Beige', 'Café', 'Rojo', 'Azul', 'Verde', 'Amarillo', 'Naranja', 'Rosa', 'Morado', 'Fucsia', 'Azul Marino', 'Vino Tinto', 'Verde Oliva', 'Turquesa', 'Dorado', 'Plateado', 'Multicolor'];
                                    $es_color_otro = !empty($color_actual) && !in_array($color_actual, $colores_predefinidos);
                                ?>
                                <select class="form-select" id="color_select_edit" name="color" onchange="toggleColorOtro(this, 'color_otro_input_edit')">
                                    <option value="">Seleccione un color</option>
                                    <optgroup label="Colores Básicos">
                                        <option value="Blanco" <?= $color_actual === 'Blanco' ? 'selected' : '' ?>>Blanco</option>
                                        <option value="Negro" <?= $color_actual === 'Negro' ? 'selected' : '' ?>>Negro</option>
                                        <option value="Gris" <?= $color_actual === 'Gris' ? 'selected' : '' ?>>Gris</option>
                                        <option value="Beige" <?= $color_actual === 'Beige' ? 'selected' : '' ?>>Beige</option>
                                        <option value="Café" <?= $color_actual === 'Café' ? 'selected' : '' ?>>Café</option>
                                    </optgroup>
                                    <optgroup label="Colores Vivos">
                                        <option value="Rojo" <?= $color_actual === 'Rojo' ? 'selected' : '' ?>>Rojo</option>
                                        <option value="Azul" <?= $color_actual === 'Azul' ? 'selected' : '' ?>>Azul</option>
                                        <option value="Verde" <?= $color_actual === 'Verde' ? 'selected' : '' ?>>Verde</option>
                                        <option value="Amarillo" <?= $color_actual === 'Amarillo' ? 'selected' : '' ?>>Amarillo</option>
                                        <option value="Naranja" <?= $color_actual === 'Naranja' ? 'selected' : '' ?>>Naranja</option>
                                        <option value="Rosa" <?= $color_actual === 'Rosa' ? 'selected' : '' ?>>Rosa</option>
                                        <option value="Morado" <?= $color_actual === 'Morado' ? 'selected' : '' ?>>Morado</option>
                                        <option value="Fucsia" <?= $color_actual === 'Fucsia' ? 'selected' : '' ?>>Fucsia</option>
                                    </optgroup>
                                    <optgroup label="Tonos Especiales">
                                        <option value="Azul Marino" <?= $color_actual === 'Azul Marino' ? 'selected' : '' ?>>Azul Marino</option>
                                        <option value="Vino Tinto" <?= $color_actual === 'Vino Tinto' ? 'selected' : '' ?>>Vino Tinto</option>
                                        <option value="Verde Oliva" <?= $color_actual === 'Verde Oliva' ? 'selected' : '' ?>>Verde Oliva</option>
                                        <option value="Turquesa" <?= $color_actual === 'Turquesa' ? 'selected' : '' ?>>Turquesa</option>
                                        <option value="Dorado" <?= $color_actual === 'Dorado' ? 'selected' : '' ?>>Dorado</option>
                                        <option value="Plateado" <?= $color_actual === 'Plateado' ? 'selected' : '' ?>>Plateado</option>
                                    </optgroup>
                                    <option value="Multicolor" <?= $color_actual === 'Multicolor' ? 'selected' : '' ?>>Multicolor</option>
                                    <option value="otro" <?= $es_color_otro ? 'selected' : '' ?>>Otro (escribir manualmente)</option>
                                </select>
                                <input type="text" class="form-control mt-2" id="color_otro_input_edit" name="color"
                                       placeholder="Escriba el color personalizado"
                                       value="<?= $es_color_otro ? htmlspecialchars($color_actual) : '' ?>"
                                       style="display: <?= $es_color_otro ? 'block' : 'none' ?>;" maxlength="50">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="talla" class="form-label">Talla</label>
                                <?php
                                    $talla_actual = $datos_antiguos['talla'] ?? $producto['talla'] ?? '';
                                    $tallas_predefinidas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36', '38', '40', '42', '2', '4', '6', '8', '10', '12', '14', '16', 'Única'];
                                    $es_talla_otra = !empty($talla_actual) && !in_array($talla_actual, $tallas_predefinidas);
                                ?>
                                <select class="form-select" id="talla_select_edit" name="talla" onchange="toggleTallaOtro(this, 'talla_otro_input_edit')">
                                    <option value="">Seleccione una talla</option>
                                    <optgroup label="Tallas Letras">
                                        <option value="XS" <?= $talla_actual === 'XS' ? 'selected' : '' ?>>XS</option>
                                        <option value="S" <?= $talla_actual === 'S' ? 'selected' : '' ?>>S</option>
                                        <option value="M" <?= $talla_actual === 'M' ? 'selected' : '' ?>>M</option>
                                        <option value="L" <?= $talla_actual === 'L' ? 'selected' : '' ?>>L</option>
                                        <option value="XL" <?= $talla_actual === 'XL' ? 'selected' : '' ?>>XL</option>
                                        <option value="XXL" <?= $talla_actual === 'XXL' ? 'selected' : '' ?>>XXL</option>
                                    </optgroup>
                                    <optgroup label="Tallas Números">
                                        <option value="28" <?= $talla_actual === '28' ? 'selected' : '' ?>>28</option>
                                        <option value="30" <?= $talla_actual === '30' ? 'selected' : '' ?>>30</option>
                                        <option value="32" <?= $talla_actual === '32' ? 'selected' : '' ?>>32</option>
                                        <option value="34" <?= $talla_actual === '34' ? 'selected' : '' ?>>34</option>
                                        <option value="36" <?= $talla_actual === '36' ? 'selected' : '' ?>>36</option>
                                        <option value="38" <?= $talla_actual === '38' ? 'selected' : '' ?>>38</option>
                                        <option value="40" <?= $talla_actual === '40' ? 'selected' : '' ?>>40</option>
                                        <option value="42" <?= $talla_actual === '42' ? 'selected' : '' ?>>42</option>
                                    </optgroup>
                                    <optgroup label="Tallas Infantiles">
                                        <option value="2" <?= $talla_actual === '2' ? 'selected' : '' ?>>2</option>
                                        <option value="4" <?= $talla_actual === '4' ? 'selected' : '' ?>>4</option>
                                        <option value="6" <?= $talla_actual === '6' ? 'selected' : '' ?>>6</option>
                                        <option value="8" <?= $talla_actual === '8' ? 'selected' : '' ?>>8</option>
                                        <option value="10" <?= $talla_actual === '10' ? 'selected' : '' ?>>10</option>
                                        <option value="12" <?= $talla_actual === '12' ? 'selected' : '' ?>>12</option>
                                        <option value="14" <?= $talla_actual === '14' ? 'selected' : '' ?>>14</option>
                                        <option value="16" <?= $talla_actual === '16' ? 'selected' : '' ?>>16</option>
                                    </optgroup>
                                    <option value="Única" <?= $talla_actual === 'Única' ? 'selected' : '' ?>>Única</option>
                                    <option value="otro" <?= $es_talla_otra ? 'selected' : '' ?>>Otro (escribir manualmente)</option>
                                </select>
                                <input type="text" class="form-control mt-2" id="talla_otro_input_edit" name="talla"
                                       placeholder="Escriba la talla personalizada"
                                       value="<?= $es_talla_otra ? htmlspecialchars($talla_actual) : '' ?>"
                                       style="display: <?= $es_talla_otra ? 'block' : 'none' ?>;" maxlength="50">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Imágenes del Producto -->
                <div class="col-lg-4">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-images"></i> Imágenes del Producto
                    </h5>

                    <!-- Imagen Principal (Producto Solo) -->
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <small class="fw-bold text-dark"><i class="bi bi-1-circle"></i> Producto Solo</small>
                            <?php if ($accion === 'editar' && !empty($producto['imagen'])): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarImagenExistente('imagen', 'image-preview')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body text-center">
                            <!-- Preview de imagen principal -->
                            <div id="image-preview" class="mb-3 position-relative">
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen'] ?>"
                                         class="img-fluid rounded" style="max-height: 150px;" id="imagen-actual">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="height: 150px;">
                                        <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <input type="file" class="form-control form-control-sm" id="imagen" name="imagen"
                                   accept="image/*" onchange="previewImage(this, 'image-preview')">
                            <input type="hidden" id="eliminar_imagen" name="eliminar_imagen" value="0">
                            <small class="text-muted d-block mt-1">Foto del producto sin modelo</small>
                        </div>
                    </div>

                    <!-- Imagen con Modelo -->
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <small class="fw-bold text-dark"><i class="bi bi-2-circle"></i> Con Modelo</small>
                            <?php if ($accion === 'editar' && !empty($producto['imagen_modelo'])): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarImagenExistente('imagen_modelo', 'image-modelo-preview')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body text-center">
                            <!-- Preview de imagen con modelo -->
                            <div id="image-modelo-preview" class="mb-3 position-relative">
                                <?php if (!empty($producto['imagen_modelo'])): ?>
                                    <img src="<?= APP_URL ?>/uploads/<?= $producto['imagen_modelo'] ?>"
                                         class="img-fluid rounded" style="max-height: 150px;" id="imagen-modelo-actual">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="height: 150px;">
                                        <i class="bi bi-person text-muted" style="font-size: 2.5rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <input type="file" class="form-control form-control-sm" id="imagen_modelo" name="imagen_modelo"
                                   accept="image/*" onchange="previewImage(this, 'image-modelo-preview')">
                            <input type="hidden" id="eliminar_imagen_modelo" name="eliminar_imagen_modelo" value="0">
                            <small class="text-muted d-block mt-1">Foto del producto puesto en modelo</small>
                        </div>
                    </div>

                    <small class="text-muted d-block text-center">
                        <i class="bi bi-info-circle"></i>
                        Formatos: JPG, PNG, GIF · Máx: 5MB
                    </small>
                    
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
                        <select class="form-select variante-color-select" name="variantes[${id}][color]"
                                id="variante_color_select_${id}"
                                onchange="toggleColorOtroVariante(${id})" required>
                            <option value="">Seleccione</option>
                            <optgroup label="Básicos">
                                <option value="Blanco" ${color === 'Blanco' ? 'selected' : ''}>Blanco</option>
                                <option value="Negro" ${color === 'Negro' ? 'selected' : ''}>Negro</option>
                                <option value="Gris" ${color === 'Gris' ? 'selected' : ''}>Gris</option>
                                <option value="Beige" ${color === 'Beige' ? 'selected' : ''}>Beige</option>
                                <option value="Café" ${color === 'Café' ? 'selected' : ''}>Café</option>
                            </optgroup>
                            <optgroup label="Vivos">
                                <option value="Rojo" ${color === 'Rojo' ? 'selected' : ''}>Rojo</option>
                                <option value="Azul" ${color === 'Azul' ? 'selected' : ''}>Azul</option>
                                <option value="Verde" ${color === 'Verde' ? 'selected' : ''}>Verde</option>
                                <option value="Amarillo" ${color === 'Amarillo' ? 'selected' : ''}>Amarillo</option>
                                <option value="Naranja" ${color === 'Naranja' ? 'selected' : ''}>Naranja</option>
                                <option value="Rosa" ${color === 'Rosa' ? 'selected' : ''}>Rosa</option>
                                <option value="Morado" ${color === 'Morado' ? 'selected' : ''}>Morado</option>
                                <option value="Fucsia" ${color === 'Fucsia' ? 'selected' : ''}>Fucsia</option>
                            </optgroup>
                            <optgroup label="Especiales">
                                <option value="Azul Marino" ${color === 'Azul Marino' ? 'selected' : ''}>Azul Marino</option>
                                <option value="Vino Tinto" ${color === 'Vino Tinto' ? 'selected' : ''}>Vino Tinto</option>
                                <option value="Verde Oliva" ${color === 'Verde Oliva' ? 'selected' : ''}>Verde Oliva</option>
                                <option value="Turquesa" ${color === 'Turquesa' ? 'selected' : ''}>Turquesa</option>
                                <option value="Dorado" ${color === 'Dorado' ? 'selected' : ''}>Dorado</option>
                                <option value="Plateado" ${color === 'Plateado' ? 'selected' : ''}>Plateado</option>
                            </optgroup>
                            <option value="Multicolor" ${color === 'Multicolor' ? 'selected' : ''}>Multicolor</option>
                            <option value="otro">Otro (escribir manualmente)</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="variante_color_otro_${id}"
                               name="variantes[${id}][color_otro]"
                               placeholder="Escriba el color personalizado"
                               style="display: none;" maxlength="50">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Talla *</label>
                        <select class="form-select variante-talla-select" name="variantes[${id}][talla]"
                                id="variante_talla_select_${id}"
                                onchange="toggleTallaOtroVariante(${id})" required>
                            <option value="">Seleccione</option>
                            <optgroup label="Letras">
                                <option value="XS" ${talla === 'XS' ? 'selected' : ''}>XS</option>
                                <option value="S" ${talla === 'S' ? 'selected' : ''}>S</option>
                                <option value="M" ${talla === 'M' ? 'selected' : ''}>M</option>
                                <option value="L" ${talla === 'L' ? 'selected' : ''}>L</option>
                                <option value="XL" ${talla === 'XL' ? 'selected' : ''}>XL</option>
                                <option value="XXL" ${talla === 'XXL' ? 'selected' : ''}>XXL</option>
                            </optgroup>
                            <optgroup label="Números">
                                <option value="28" ${talla === '28' ? 'selected' : ''}>28</option>
                                <option value="30" ${talla === '30' ? 'selected' : ''}>30</option>
                                <option value="32" ${talla === '32' ? 'selected' : ''}>32</option>
                                <option value="34" ${talla === '34' ? 'selected' : ''}>34</option>
                                <option value="36" ${talla === '36' ? 'selected' : ''}>36</option>
                                <option value="38" ${talla === '38' ? 'selected' : ''}>38</option>
                                <option value="40" ${talla === '40' ? 'selected' : ''}>40</option>
                                <option value="42" ${talla === '42' ? 'selected' : ''}>42</option>
                            </optgroup>
                            <optgroup label="Infantiles">
                                <option value="2" ${talla === '2' ? 'selected' : ''}>2</option>
                                <option value="4" ${talla === '4' ? 'selected' : ''}>4</option>
                                <option value="6" ${talla === '6' ? 'selected' : ''}>6</option>
                                <option value="8" ${talla === '8' ? 'selected' : ''}>8</option>
                                <option value="10" ${talla === '10' ? 'selected' : ''}>10</option>
                                <option value="12" ${talla === '12' ? 'selected' : ''}>12</option>
                                <option value="14" ${talla === '14' ? 'selected' : ''}>14</option>
                                <option value="16" ${talla === '16' ? 'selected' : ''}>16</option>
                            </optgroup>
                            <option value="Única" ${talla === 'Única' ? 'selected' : ''}>Única</option>
                            <option value="otro">Otro (escribir manualmente)</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="variante_talla_otro_${id}"
                               name="variantes[${id}][talla_otro]"
                               placeholder="Escriba la talla personalizada"
                               style="display: none;" maxlength="50">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock *</label>
                        <input type="number" class="form-control" name="variantes[${id}][stock]"
                               value="${stock}" required min="0">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-1-circle"></i> Imagen Producto Solo</label>
                        <input type="file" class="form-control form-control-sm imagen-variante" name="variantes[${id}][imagen]"
                               accept="image/*" onchange="previewVarianteImage(this, ${id}, 'solo')">
                        <small class="text-muted d-block">Opcional. Si no subes, usará la imagen principal</small>
                        <div id="preview-variante-${id}-solo" class="mt-2"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-2-circle"></i> Imagen Con Modelo</label>
                        <input type="file" class="form-control form-control-sm imagen-variante" name="variantes[${id}][imagen_modelo]"
                               accept="image/*" onchange="previewVarianteImage(this, ${id}, 'modelo')">
                        <small class="text-muted d-block">Opcional</small>
                        <div id="preview-variante-${id}-modelo" class="mt-2"></div>
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

    function previewVarianteImage(input, varianteId, tipo = 'solo') {
        const preview = document.getElementById(`preview-variante-${varianteId}-${tipo}`);

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
    function previewImage(input, previewId = 'image-preview') {
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const maxHeight = previewId === 'image-preview' ? '150px' : '150px';
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: ${maxHeight};">`;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Eliminar imagen existente
    function eliminarImagenExistente(campoNombre, previewId) {
        if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
            // Marcar para eliminación
            document.getElementById('eliminar_' + campoNombre).value = '1';

            // Actualizar preview
            const preview = document.getElementById(previewId);
            const icon = campoNombre === 'imagen_modelo' ? 'person' : 'image';
            preview.innerHTML = `
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                    <i class="bi bi-${icon} text-muted" style="font-size: 2.5rem;"></i>
                </div>
            `;

            // Limpiar el input de archivo
            document.getElementById(campoNombre).value = '';

            // Ocultar botón de eliminar
            event.target.closest('.btn').style.display = 'none';
        }
    }

    // Función para mostrar/ocultar campo de talla personalizada
    function toggleTallaOtro(selectElement, inputId) {
        const inputOtro = document.getElementById(inputId);

        if (selectElement.value === 'otro') {
            inputOtro.style.display = 'block';
            inputOtro.required = true;
            selectElement.removeAttribute('name'); // Remover name del select
            inputOtro.setAttribute('name', 'talla'); // Agregar name al input
        } else {
            inputOtro.style.display = 'none';
            inputOtro.required = false;
            inputOtro.value = '';
            selectElement.setAttribute('name', 'talla'); // Restaurar name al select
            inputOtro.removeAttribute('name'); // Remover name del input
        }
    }

    // Función específica para variantes de talla
    function toggleTallaOtroVariante(varianteId) {
        const selectElement = document.getElementById(`variante_talla_select_${varianteId}`);
        const inputOtro = document.getElementById(`variante_talla_otro_${varianteId}`);

        if (selectElement.value === 'otro') {
            inputOtro.style.display = 'block';
            inputOtro.required = true;
            selectElement.removeAttribute('name'); // Remover name del select
            inputOtro.setAttribute('name', `variantes[${varianteId}][talla]`); // Agregar name al input
        } else {
            inputOtro.style.display = 'none';
            inputOtro.required = false;
            inputOtro.value = '';
            selectElement.setAttribute('name', `variantes[${varianteId}][talla]`); // Restaurar name al select
            inputOtro.removeAttribute('name'); // Remover name del input
        }
    }

    // Función para mostrar/ocultar campo de color personalizado
    function toggleColorOtro(selectElement, inputId) {
        const inputOtro = document.getElementById(inputId);

        if (selectElement.value === 'otro') {
            inputOtro.style.display = 'block';
            inputOtro.required = true;
            selectElement.removeAttribute('name'); // Remover name del select
            inputOtro.setAttribute('name', 'color'); // Agregar name al input
        } else {
            inputOtro.style.display = 'none';
            inputOtro.required = false;
            inputOtro.value = '';
            selectElement.setAttribute('name', 'color'); // Restaurar name al select
            inputOtro.removeAttribute('name'); // Remover name del input
        }
    }

    // Función específica para variantes de color
    function toggleColorOtroVariante(varianteId) {
        const selectElement = document.getElementById(`variante_color_select_${varianteId}`);
        const inputOtro = document.getElementById(`variante_color_otro_${varianteId}`);

        if (selectElement.value === 'otro') {
            inputOtro.style.display = 'block';
            inputOtro.required = true;
            selectElement.removeAttribute('name'); // Remover name del select
            inputOtro.setAttribute('name', `variantes[${varianteId}][color]`); // Agregar name al input
        } else {
            inputOtro.style.display = 'none';
            inputOtro.required = false;
            inputOtro.value = '';
            selectElement.setAttribute('name', `variantes[${varianteId}][color]`); // Restaurar name al select
            inputOtro.removeAttribute('name'); // Remover name del input
        }
    }

    // Auto-generar código de producto (solo en modo simple)
    <?php if ($accion === 'crear'): ?>
    const categoriaInput = document.getElementById('categoria');
    const colorSelectSimple = document.getElementById('color_select');
    const colorOtroSimple = document.getElementById('color_otro_input');

    if (colorSelectSimple) {
        categoriaInput.addEventListener('blur', generarCodigo);
        colorSelectSimple.addEventListener('change', generarCodigo);
        colorOtroSimple.addEventListener('blur', generarCodigo);
    }
    <?php else: ?>
    document.getElementById('categoria').addEventListener('blur', generarCodigo);
    const colorSelectEdit = document.getElementById('color_select_edit');
    const colorOtroEdit = document.getElementById('color_otro_input_edit');
    if (colorSelectEdit) {
        colorSelectEdit.addEventListener('change', generarCodigo);
    }
    if (colorOtroEdit) {
        colorOtroEdit.addEventListener('blur', generarCodigo);
    }
    <?php endif; ?>

    function generarCodigo() {
        const codigoInput = document.getElementById('codigo_producto');

        <?php if ($accion === 'crear'): ?>
        const colorSelect = document.getElementById('color_select');
        const colorOtro = document.getElementById('color_otro_input');
        <?php else: ?>
        const colorSelect = document.getElementById('color_select_edit');
        const colorOtro = document.getElementById('color_otro_input_edit');
        <?php endif; ?>

        // Solo generar si el campo está vacío y no está en modo variantes
        if (codigoInput && codigoInput.value.trim() === '' && colorSelect) {
            const categoria = document.getElementById('categoria').value.trim();
            let color = '';

            // Obtener color del select o del input "otro"
            if (colorSelect.value === 'otro' && colorOtro && colorOtro.value.trim() !== '') {
                color = colorOtro.value.trim();
            } else if (colorSelect.value !== '' && colorSelect.value !== 'otro') {
                color = colorSelect.value.trim();
            }

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