<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/combos/guardar" id="formCombo">

            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="row">
                <!-- Información Básica del Combo -->
                <div class="col-lg-8">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-box-seam"></i> Información del Combo
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Combo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                   value="<?= htmlspecialchars($_SESSION['datos_antiguos']['nombre'] ?? '') ?>"
                                   required maxlength="255"
                                   placeholder="Ej: Combo Primavera 2025">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Combo *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="small">Small (10 prendas)</option>
                                <option value="medium">Medium (25 prendas)</option>
                                <option value="big">Big (50 prendas)</option>
                                <option value="extra_big">Extra Big (100 prendas)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">Precio del Combo *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio"
                                       value="<?= $_SESSION['datos_antiguos']['precio'] ?? '' ?>"
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ubicacion" class="form-label">Ubicación de Productos *</label>
                            <select class="form-select" id="ubicacion" name="ubicacion" required>
                                <option value="Mixto">Mixto (Medellín + Bogotá)</option>
                                <option value="Medellín">Solo Medellín</option>
                                <option value="Bogotá">Solo Bogotá</option>
                            </select>
                            <small class="text-muted">De dónde se tomarán los productos</small>
                        </div>
                    </div>

                    <input type="hidden" id="cantidad_total" name="cantidad_total" value="0">

                    <h5 class="text-primary mb-3 mt-4">
                        <i class="bi bi-tags"></i> Distribución por Categorías
                    </h5>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Total de prendas:</strong> <span id="total_prendas">0</span> / <span id="max_prendas">0</span>
                    </div>

                    <div id="categorias-container">
                        <!-- Las categorías se agregarán dinámicamente aquí -->
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarCategoria">
                        <i class="bi bi-plus-circle"></i> Agregar Categoría
                    </button>
                </div>

                <!-- Información Lateral -->
                <div class="col-lg-4">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-info-circle"></i> Información
                    </h5>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="text-dark mb-2">Tipos de Combo</h6>
                            <ul class="list-unstyled small text-dark">
                                <li><strong>Small:</strong> 10 prendas</li>
                                <li><strong>Medium:</strong> 25 prendas</li>
                                <li><strong>Big:</strong> 50 prendas</li>
                                <li><strong>Extra Big:</strong> 100 prendas</li>
                            </ul>

                            <hr>

                            <h6 class="text-dark mb-2">Instrucciones</h6>
                            <ol class="small text-dark">
                                <li>Seleccione el tipo de combo</li>
                                <li>Agregue categorías y cantidades</li>
                                <li>La suma debe coincidir con el total</li>
                                <li>Los productos se seleccionan aleatoriamente</li>
                            </ol>

                            <hr>

                            <h6 class="text-dark mb-2">Categorías Disponibles</h6>
                            <div id="categorias-disponibles" class="small">
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No hay categorías disponibles</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="<?= APP_URL ?>/combos" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Crear Combo
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const tiposCombos = {
    'small': 10,
    'medium': 25,
    'big': 50,
    'extra_big': 100
};

const categoriasDisponibles = <?= json_encode($categorias) ?>;

let contadorCategorias = 0;

// Cambiar tipo de combo
document.getElementById('tipo').addEventListener('change', function() {
    const tipo = this.value;
    const maxPrendas = tiposCombos[tipo] || 0;
    document.getElementById('max_prendas').textContent = maxPrendas;
    document.getElementById('cantidad_total').value = maxPrendas;
    calcularTotal();
});

// Agregar categoría
document.getElementById('btnAgregarCategoria').addEventListener('click', function() {
    agregarCategoria();
});

function agregarCategoria(nombre = '', cantidad = '') {
    const container = document.getElementById('categorias-container');
    const id = contadorCategorias++;

    const div = document.createElement('div');
    div.className = 'row mb-2 categoria-row';
    div.id = `categoria-${id}`;

    div.innerHTML = `
        <div class="col-md-6">
            <select class="form-select" name="categorias[${id}][nombre]" required>
                <option value="">Seleccione categoría</option>
                ${categoriasDisponibles.map(cat =>
                    `<option value="${cat}" ${cat === nombre ? 'selected' : ''}>${cat}</option>`
                ).join('')}
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control cantidad-input"
                   name="categorias[${id}][cantidad]"
                   placeholder="Cantidad"
                   min="1"
                   value="${cantidad}"
                   required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="eliminarCategoria(${id})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(div);

    // Agregar evento para calcular total
    div.querySelector('.cantidad-input').addEventListener('input', calcularTotal);
}

function eliminarCategoria(id) {
    const elemento = document.getElementById(`categoria-${id}`);
    if (elemento) {
        elemento.remove();
        calcularTotal();
    }
}

function calcularTotal() {
    const inputs = document.querySelectorAll('.cantidad-input');
    let total = 0;

    inputs.forEach(input => {
        const valor = parseInt(input.value) || 0;
        total += valor;
    });

    document.getElementById('total_prendas').textContent = total;

    // Validar que no exceda el máximo
    const max = parseInt(document.getElementById('max_prendas').textContent) || 0;
    const totalElement = document.getElementById('total_prendas');

    if (total > max) {
        totalElement.className = 'text-danger fw-bold';
    } else if (total === max && total > 0) {
        totalElement.className = 'text-success fw-bold';
    } else {
        totalElement.className = '';
    }
}

// Validación del formulario
document.getElementById('formCombo').addEventListener('submit', function(e) {
    const tipo = document.getElementById('tipo').value;
    const precio = parseFloat(document.getElementById('precio').value);
    const total = parseInt(document.getElementById('total_prendas').textContent);
    const max = parseInt(document.getElementById('max_prendas').textContent);

    if (!tipo) {
        e.preventDefault();
        alert('Debe seleccionar un tipo de combo');
        return;
    }

    if (precio <= 0) {
        e.preventDefault();
        alert('El precio debe ser mayor a 0');
        return;
    }

    if (total === 0) {
        e.preventDefault();
        alert('Debe agregar al menos una categoría con cantidad');
        return;
    }

    if (total !== max) {
        e.preventDefault();
        alert(`La suma de categorías (${total}) debe ser igual al total del combo (${max})`);
        return;
    }

    if (!confirm('¿Está seguro de crear este combo? Los productos se seleccionarán aleatoriamente del inventario.')) {
        e.preventDefault();
        return;
    }
});
</script>

<?php
// Limpiar datos antiguos de sesión
unset($_SESSION['datos_antiguos']);
?>
