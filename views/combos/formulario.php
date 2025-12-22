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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente *</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>"
                                            <?= (isset($_SESSION['datos_antiguos']['cliente_id']) && $_SESSION['datos_antiguos']['cliente_id'] == $cliente['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cliente['nombre']) ?> - <?= htmlspecialchars($cliente['telefono']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Cliente al que se le venderá el combo</small>
                        </div>
                    </div>

                    <input type="hidden" id="cantidad_total" name="cantidad_total" value="0">

                    <h5 class="text-primary mb-3 mt-4">
                        <i class="bi bi-tags"></i> Distribución por Tipos
                    </h5>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Total de prendas:</strong> <span id="total_prendas">0</span> / <span id="max_prendas">0</span>
                    </div>

                    <div id="tipos-container">
                        <!-- Los tipos se agregarán dinámicamente aquí -->
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarTipo">
                        <i class="bi bi-plus-circle"></i> Agregar Tipo
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
                                <li>Agregue tipos (Niño, Mujer, Hombre) y cantidades</li>
                                <li>La suma debe coincidir con el total</li>
                                <li>Los productos se seleccionan aleatoriamente</li>
                            </ol>

                            <hr>

                            <h6 class="text-dark mb-2">Tipos Disponibles</h6>
                            <div id="tipos-disponibles" class="small">
                                <span class="badge bg-secondary me-1 mb-1">Niño</span>
                                <span class="badge bg-secondary me-1 mb-1">Mujer</span>
                                <span class="badge bg-secondary me-1 mb-1">Hombre</span>
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
// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DEBUG: DOM Cargado ===');

    const tiposCombos = {
        'small': 10,
        'medium': 25,
        'big': 50,
        'extra_big': 100
    };

    const tiposDisponibles = ['Niño', 'Mujer', 'Hombre'];

    let contadorTipos = 0;

    // Verificar que los elementos existen
    const elementoTipo = document.getElementById('tipo');
    const elementoMaxPrendas = document.getElementById('max_prendas');
    const elementoCantidadTotal = document.getElementById('cantidad_total');
    const elementoTotalPrendas = document.getElementById('total_prendas');
    const elementoTiposContainer = document.getElementById('tipos-container');
    const elementoBtnAgregar = document.getElementById('btnAgregarTipo');
    const elementoFormCombo = document.getElementById('formCombo');

    console.log('Elementos del DOM:', {
        tipo: elementoTipo,
        maxPrendas: elementoMaxPrendas,
        cantidadTotal: elementoCantidadTotal,
        totalPrendas: elementoTotalPrendas,
        tiposContainer: elementoTiposContainer,
        btnAgregar: elementoBtnAgregar,
        formCombo: elementoFormCombo
    });

    if (!elementoTipo || !elementoMaxPrendas || !elementoCantidadTotal || !elementoTotalPrendas) {
        console.error('ERROR: Faltan elementos del DOM necesarios para el formulario');
        return;
    }

// Cambiar tipo de combo
elementoTipo.addEventListener('change', function() {
    const tipo = this.value;
    const maxPrendas = tiposCombos[tipo] || 0;
    console.log('=== DEBUG: Tipo cambiado ===');
    console.log('Tipo seleccionado:', tipo);
    console.log('Max prendas:', maxPrendas);
    elementoMaxPrendas.textContent = maxPrendas;
    elementoCantidadTotal.value = maxPrendas;
    console.log('Campo cantidad_total actualizado a:', elementoCantidadTotal.value);
    calcularTotal();
});

// Agregar tipo
elementoBtnAgregar.addEventListener('click', function() {
    agregarTipo();
});

function agregarTipo(nombre = '', cantidad = '') {
    const container = elementoTiposContainer;
    const id = contadorTipos++;

    const div = document.createElement('div');
    div.className = 'row mb-2 tipo-row';
    div.id = `tipo-${id}`;

    div.innerHTML = `
        <div class="col-md-6">
            <select class="form-select" name="tipos[${id}][nombre]" required>
                <option value="">Seleccione tipo</option>
                ${tiposDisponibles.map(tipo =>
                    `<option value="${tipo}" ${tipo === nombre ? 'selected' : ''}>${tipo}</option>`
                ).join('')}
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control cantidad-input"
                   name="tipos[${id}][cantidad]"
                   placeholder="Cantidad"
                   min="1"
                   value="${cantidad}"
                   required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="eliminarTipo(${id})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(div);

    // Agregar evento para calcular total
    div.querySelector('.cantidad-input').addEventListener('input', calcularTotal);
}

function eliminarTipo(id) {
    const elemento = document.getElementById(`tipo-${id}`);
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

    console.log('=== DEBUG: Calculando total ===');
    console.log('Total de prendas calculadas:', total);
    elementoTotalPrendas.textContent = total;

    // Validar que no exceda el máximo
    const max = parseInt(elementoMaxPrendas.textContent) || 0;

    if (total > max) {
        elementoTotalPrendas.className = 'text-danger fw-bold';
    } else if (total === max && total > 0) {
        elementoTotalPrendas.className = 'text-success fw-bold';
    } else {
        elementoTotalPrendas.className = '';
    }
}

// Validación del formulario
elementoFormCombo.addEventListener('submit', function(e) {
    const tipo = elementoTipo.value;
    const precio = parseFloat(document.getElementById('precio').value);
    const total = parseInt(elementoTotalPrendas.textContent);
    const max = parseInt(elementoMaxPrendas.textContent);
    const cantidadTotal = elementoCantidadTotal.value;

    console.log('=== DEBUG: Validación del formulario ===');
    console.log('Tipo:', tipo);
    console.log('Precio:', precio);
    console.log('Total prendas (calculado):', total);
    console.log('Max prendas:', max);
    console.log('Campo cantidad_total (hidden):', cantidadTotal);

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
        alert('Debe agregar al menos un tipo con cantidad');
        return;
    }

    if (total !== max) {
        e.preventDefault();
        alert(`La suma de tipos (${total}) debe ser igual al total del combo (${max})`);
        return;
    }

    if (!confirm('¿Está seguro de crear este combo? Los productos se seleccionarán aleatoriamente del inventario.')) {
        e.preventDefault();
        return;
    }
});

}); // Fin de DOMContentLoaded
</script>

<?php
// Limpiar datos antiguos de sesión
unset($_SESSION['datos_antiguos']);
?>
