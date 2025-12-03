<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-cart-plus"></i> Nueva Venta
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/ventas/guardar" id="formVenta">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <!-- Sección Cliente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-person-circle"></i> Información del Cliente
                            </h6>
                        </div>

                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Buscar Cliente *</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="busquedaCliente"
                                           placeholder="Buscar por nombre o teléfono..."
                                           autocomplete="off">
                                </div>
                                <input type="hidden" name="cliente_id" id="cliente_id" required>

                                <!-- Lista de clientes filtrable -->
                                <div class="border rounded" style="max-height: 250px; overflow-y: auto; background: white;">
                                    <div id="listaClientes" class="list-group list-group-flush">
                                        <div class="list-group-item text-center text-muted">
                                            <i class="bi bi-hourglass-split"></i> Cargando clientes...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="button" class="btn btn-outline-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                <i class="bi bi-person-plus"></i> Nuevo Cliente
                            </button>

                            <!-- Info del cliente seleccionado -->
                            <div id="infoClienteSeleccionado" class="card border-success" style="display: none;">
                                <div class="card-body p-3" style="background: #ffffff;">
                                    <h6 class="card-title mb-2" style="color: #198754;">
                                        <i class="bi bi-person-check-fill"></i> Cliente Seleccionado
                                    </h6>
                                    <p class="mb-1 text-dark"><strong>Nombre:</strong> <span id="nombreClienteSeleccionado"></span></p>
                                    <p class="mb-1 text-dark"><strong>Teléfono:</strong> <span id="telefonoClienteSeleccionado"></span></p>
                                    <p class="mb-0 text-dark"><strong>Ciudad:</strong> <span id="ciudadClienteSeleccionado"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Productos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-box-seam"></i> Productos
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Buscar y Filtrar Productos</label>

                                <!-- Barra de búsqueda -->
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="busquedaProducto"
                                           placeholder="Buscar por nombre o código..."
                                           autocomplete="off">
                                </div>

                                <!-- Filtros adicionales -->
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <select class="form-select form-select-sm" id="filtroCategoria">
                                            <option value="">Categoría</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select form-select-sm" id="filtroTalla">
                                            <option value="">Talla</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select form-select-sm" id="filtroColor">
                                            <option value="">Color</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Controles de vista y ordenamiento -->
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="vistaProductos" id="vistaLista" value="lista" checked>
                                        <label class="btn btn-outline-primary" for="vistaLista" title="Vista lista">
                                            <i class="bi bi-list-ul"></i>
                                        </label>
                                        <input type="radio" class="btn-check" name="vistaProductos" id="vistaTarjetas" value="tarjetas">
                                        <label class="btn btn-outline-primary" for="vistaTarjetas" title="Vista tarjetas">
                                            <i class="bi bi-grid-3x2"></i>
                                        </label>
                                    </div>
                                    <select class="form-select form-select-sm fw-bold" id="ordenarProductos" style="width: auto; color: #212529;">
                                        <option value="nombre_asc">A-Z</option>
                                        <option value="nombre_desc">Z-A</option>
                                        <option value="precio_asc">Precio menor</option>
                                        <option value="precio_desc">Precio mayor</option>
                                        <option value="stock_asc">Stock menor</option>
                                        <option value="stock_desc">Stock mayor</option>
                                    </select>
                                </div>

                                <!-- Lista de productos filtrable -->
                                <div class="border rounded" style="max-height: 400px; overflow-y: auto; background: white;" id="contenedorProductos">
                                    <div id="listaProductos" class="list-group list-group-flush">
                                        <div class="list-group-item text-center text-muted">
                                            <i class="bi bi-hourglass-split"></i> Cargando productos...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Tabla de productos agregados -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Productos Agregados</label>
                                <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto; background: white;">
                                    <div id="listaProductosAgregados">
                                        <p class="text-center text-muted my-3">
                                            <i class="bi bi-inbox"></i><br>
                                            No hay productos agregados
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 p-2 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>TOTAL:</strong>
                                        <h5 id="totalVenta" class="mb-0" style="color: #667eea;">$0</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs para productos (se generan dinámicamente) -->
                    <div id="productosInputs"></div>

                    <!-- Sección Pago -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-credit-card"></i> Información de Pago
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label fw-bold">Método de Pago *</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                    <option value="">Seleccione un método</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="contra_entrega">Contra Entrega</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_pago" class="form-label fw-bold">Estado de Pago *</label>
                                <select class="form-select" id="estado_pago" name="estado_pago" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pagado">Pagado</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Ej: Cliente solicitó entrega a domicilio, dirección especial, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= APP_URL ?>/ventas" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
                                    <i class="bi bi-save"></i> Registrar Venta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-person-plus"></i> Nuevo Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoCliente">
                    <div class="mb-3">
                        <label for="nuevo_nombre" class="form-label fw-bold" style="color: #212529 !important;">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nuevo_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_telefono" class="form-label fw-bold" style="color: #212529 !important;">Teléfono *</label>
                        <input type="tel" class="form-control" id="nuevo_telefono" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_email" class="form-label" style="color: #212529 !important;">Email</label>
                        <input type="email" class="form-control" id="nuevo_email">
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_direccion" class="form-label" style="color: #212529 !important;">Dirección</label>
                        <textarea class="form-control" id="nuevo_direccion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_ciudad" class="form-label fw-bold" style="color: #212529 !important;">Ciudad</label>
                        <select class="form-select" id="nuevo_ciudad">
                            <option value="Medellín" selected>Medellín</option>
                            <option value="Bogotá">Bogotá</option>
                            <option value="Cali">Cali</option>
                            <option value="Barranquilla">Barranquilla</option>
                            <option value="Cartagena">Cartagena</option>
                            <option value="Otra">Otra</option>
                        </select>
                    </div>
                    <div id="erroresCliente" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCliente">
                    <i class="bi bi-save"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let todosLosClientes = [];
let todosLosProductos = [];
let productosAgregados = [];
let clienteSeleccionado = null;
let vistaActual = 'lista';
let ordenActual = 'nombre_asc';

// Cargar datos al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarClientes();
    cargarProductos();
    cargarFiltros();
    configurarEventos();
});

//=== CLIENTES ===//

// Cargar todos los clientes
function cargarClientes() {
    // Cargar clientes al inicio (sin término de búsqueda)
    const formData = new FormData();
    formData.append('termino', '');

    fetch('<?= APP_URL ?>/ventas/buscarCliente', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarTodosLosClientes(data.clientes || []);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('listaClientes').innerHTML = '<div class="list-group-item text-danger">Error al cargar clientes</div>';
    });
}

function mostrarTodosLosClientes(clientes) {
    const contenedor = document.getElementById('listaClientes');

    if (clientes.length === 0) {
        contenedor.innerHTML = '<div class="list-group-item text-muted text-center">Escriba para buscar clientes...</div>';
        return;
    }

    let html = '';
    clientes.forEach(cliente => {
        html += `
            <a href="#" class="list-group-item list-group-item-action" onclick="seleccionarCliente(${cliente.id}, '${escapeHtml(cliente.nombre)}', '${escapeHtml(cliente.telefono)}', '${escapeHtml(cliente.ciudad || 'N/A')}'); return false;">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>${escapeHtml(cliente.nombre)}</strong><br>
                        <small class="text-muted"><i class="bi bi-telephone"></i> ${escapeHtml(cliente.telefono)}</small>
                    </div>
                    <small class="text-muted">${escapeHtml(cliente.ciudad || 'N/A')}</small>
                </div>
            </a>
        `;
    });

    contenedor.innerHTML = html;
}

// Filtrar clientes mientras se escribe
document.getElementById('busquedaCliente').addEventListener('input', function() {
    const termino = this.value.trim().toLowerCase();

    if (termino.length === 0) {
        mostrarTodosLosClientes([]);
        return;
    }

    // Buscar en el servidor
    const formData = new FormData();
    formData.append('termino', termino);

    fetch('<?= APP_URL ?>/ventas/buscarCliente', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarTodosLosClientes(data.clientes || []);
    })
    .catch(error => console.error('Error:', error));
});

function seleccionarCliente(id, nombre, telefono, ciudad) {
    document.getElementById('cliente_id').value = id;
    document.getElementById('busquedaCliente').value = nombre;

    document.getElementById('nombreClienteSeleccionado').textContent = nombre;
    document.getElementById('telefonoClienteSeleccionado').textContent = telefono;
    document.getElementById('ciudadClienteSeleccionado').textContent = ciudad;
    document.getElementById('infoClienteSeleccionado').style.display = 'block';

    clienteSeleccionado = {id, nombre, telefono, ciudad};
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

//=== PRODUCTOS ===//

// Cargar todos los productos
function cargarProductos() {
    const formData = new FormData();
    formData.append('termino', '');

    fetch('<?= APP_URL ?>/ventas/buscarProducto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        todosLosProductos = data.productos || [];
        aplicarFiltrosYOrden();
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('listaProductos').innerHTML = '<div class="list-group-item text-danger">Error al cargar productos</div>';
    });
}

// Cargar filtros dinámicamente
function cargarFiltros() {
    const categorias = new Set();
    const tallas = new Set();
    const colores = new Set();

    // Obtener valores únicos del servidor
    fetch('<?= APP_URL ?>/ventas/obtenerFiltrosProductos', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        // Llenar select de categorías
        const selectCategoria = document.getElementById('filtroCategoria');
        (data.categorias || []).forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            selectCategoria.appendChild(option);
        });

        // Llenar select de tallas
        const selectTalla = document.getElementById('filtroTalla');
        (data.tallas || []).forEach(talla => {
            if (talla) {
                const option = document.createElement('option');
                option.value = talla;
                option.textContent = talla;
                selectTalla.appendChild(option);
            }
        });

        // Llenar select de colores
        const selectColor = document.getElementById('filtroColor');
        (data.colores || []).forEach(color => {
            if (color) {
                const option = document.createElement('option');
                option.value = color;
                option.textContent = color;
                selectColor.appendChild(option);
            }
        });
    })
    .catch(error => console.error('Error cargando filtros:', error));
}

// Configurar eventos de filtrado y ordenamiento
function configurarEventos() {
    // Búsqueda por texto - filtrado en tiempo real
    document.getElementById('busquedaProducto').addEventListener('input', aplicarFiltrosYOrden);

    // Filtros
    document.getElementById('filtroCategoria').addEventListener('change', aplicarFiltrosYOrden);
    document.getElementById('filtroTalla').addEventListener('change', aplicarFiltrosYOrden);
    document.getElementById('filtroColor').addEventListener('change', aplicarFiltrosYOrden);

    // Ordenamiento
    document.getElementById('ordenarProductos').addEventListener('change', function() {
        ordenActual = this.value;
        aplicarFiltrosYOrden();
    });

    // Vista
    document.querySelectorAll('input[name="vistaProductos"]').forEach(radio => {
        radio.addEventListener('change', function() {
            vistaActual = this.value;
            aplicarFiltrosYOrden();
        });
    });
}

// Aplicar filtros y ordenamiento
function aplicarFiltrosYOrden() {
    let productosFiltrados = [...todosLosProductos];

    // Filtrar por término de búsqueda
    const terminoBusqueda = document.getElementById('busquedaProducto').value.trim().toLowerCase();
    if (terminoBusqueda) {
        productosFiltrados = productosFiltrados.filter(p => {
            return p.nombre.toLowerCase().includes(terminoBusqueda) ||
                   p.codigo_producto.toLowerCase().includes(terminoBusqueda) ||
                   (p.descripcion && p.descripcion.toLowerCase().includes(terminoBusqueda)) ||
                   (p.categoria && p.categoria.toLowerCase().includes(terminoBusqueda)) ||
                   (p.color && p.color.toLowerCase().includes(terminoBusqueda));
        });
    }

    // Filtrar por categoría
    const categoria = document.getElementById('filtroCategoria').value;
    if (categoria) {
        productosFiltrados = productosFiltrados.filter(p => p.categoria === categoria);
    }

    // Filtrar por talla
    const talla = document.getElementById('filtroTalla').value;
    if (talla) {
        productosFiltrados = productosFiltrados.filter(p => p.talla === talla);
    }

    // Filtrar por color
    const color = document.getElementById('filtroColor').value;
    if (color) {
        productosFiltrados = productosFiltrados.filter(p => p.color === color);
    }

    // Ordenar
    productosFiltrados.sort((a, b) => {
        switch(ordenActual) {
            case 'nombre_asc':
                return a.nombre.localeCompare(b.nombre);
            case 'nombre_desc':
                return b.nombre.localeCompare(a.nombre);
            case 'precio_asc':
                return parseFloat(a.precio) - parseFloat(b.precio);
            case 'precio_desc':
                return parseFloat(b.precio) - parseFloat(a.precio);
            case 'stock_asc':
                return parseInt(a.stock) - parseInt(b.stock);
            case 'stock_desc':
                return parseInt(b.stock) - parseInt(a.stock);
            default:
                return 0;
        }
    });

    mostrarProductos(productosFiltrados);
}

// Mostrar productos según vista seleccionada
function mostrarProductos(productos) {
    const contenedor = document.getElementById('listaProductos');

    if (productos.length === 0) {
        contenedor.innerHTML = '<div class="list-group-item text-muted text-center">No se encontraron productos</div>';
        return;
    }

    if (vistaActual === 'lista') {
        mostrarProductosLista(productos, contenedor);
    } else {
        mostrarProductosTarjetas(productos, contenedor);
    }
}

// Vista de lista
function mostrarProductosLista(productos, contenedor) {
    let html = '';
    productos.forEach(producto => {
        const yaAgregado = productosAgregados.find(p => p.id === producto.id);
        const claseDeshabilitado = yaAgregado ? 'disabled' : '';
        const textoAgregado = yaAgregado ? ' <span class="badge bg-success">Agregado</span>' : '';

        const talla = producto.talla ? ` - ${escapeHtml(producto.talla)}` : '';
        const color = producto.color ? ` - ${escapeHtml(producto.color)}` : '';

        html += `
            <a href="#" class="list-group-item list-group-item-action ${claseDeshabilitado}" onclick='agregarProducto(${JSON.stringify(producto)}); return false;'>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        ${producto.imagen ? `<img src="<?= APP_URL ?>/uploads/${escapeHtml(producto.imagen)}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Producto">` : '<div class="bg-secondary rounded me-2" style="width: 40px; height: 40px;"></div>'}
                        <div>
                            <strong>${escapeHtml(producto.nombre)}</strong>${textoAgregado}<br>
                            <small class="text-muted">
                                ${escapeHtml(producto.codigo_producto)} - ${escapeHtml(producto.categoria)}${talla}${color}
                            </small>
                        </div>
                    </div>
                    <div class="text-end">
                        <strong class="text-primary">$${parseFloat(producto.precio).toLocaleString('es-CO')}</strong><br>
                        <small class="${producto.stock > 5 ? 'text-success' : 'text-warning'}">
                            <i class="bi bi-box"></i> ${producto.stock} disponibles
                        </small>
                    </div>
                </div>
            </a>
        `;
    });

    contenedor.innerHTML = html;
}

// Vista de tarjetas
function mostrarProductosTarjetas(productos, contenedor) {
    contenedor.className = 'row g-2 p-2';

    let html = '';
    productos.forEach(producto => {
        const yaAgregado = productosAgregados.find(p => p.id === producto.id);
        const claseDeshabilitado = yaAgregado ? 'opacity-50 pe-none' : '';
        const textoAgregado = yaAgregado ? '<span class="badge bg-success position-absolute top-0 end-0 m-1">Agregado</span>' : '';

        const talla = producto.talla ? `<span class="badge bg-secondary me-1">${escapeHtml(producto.talla)}</span>` : '';
        const color = producto.color ? `<span class="badge bg-secondary">${escapeHtml(producto.color)}</span>` : '';

        html += `
            <div class="col-6">
                <div class="card h-100 ${claseDeshabilitado}" style="cursor: pointer;" onclick='agregarProducto(${JSON.stringify(producto)}); return false;'>
                    <div class="position-relative">
                        ${producto.imagen ?
                            `<img src="<?= APP_URL ?>/uploads/${escapeHtml(producto.imagen)}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="Producto">` :
                            '<div class="bg-secondary" style="height: 120px;"></div>'}
                        ${textoAgregado}
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 small">${escapeHtml(producto.nombre)}</h6>
                        <p class="card-text mb-1">
                            <small class="text-muted">${escapeHtml(producto.codigo_producto)}</small><br>
                            <small class="text-muted">${escapeHtml(producto.categoria)}</small><br>
                            ${talla}${color}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-primary">$${parseFloat(producto.precio).toLocaleString('es-CO')}</strong>
                            <small class="${producto.stock > 5 ? 'text-success' : 'text-warning'}">
                                <i class="bi bi-box"></i> ${producto.stock}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = html;
}

function agregarProducto(producto) {
    // Verificar si ya está agregado
    const yaAgregado = productosAgregados.find(p => p.id === producto.id);
    if (yaAgregado) {
        alert('Este producto ya está agregado.');
        return;
    }

    productosAgregados.push({
        id: producto.id,
        nombre: producto.nombre,
        codigo: producto.codigo_producto,
        precio: parseFloat(producto.precio),
        cantidad: 1,
        stock: parseInt(producto.stock)
    });

    actualizarListaProductosAgregados();

    // Refrescar la lista de productos para mostrar el badge "Agregado"
    const termino = document.getElementById('busquedaProducto').value.trim();
    if (termino.length > 0) {
        document.getElementById('busquedaProducto').dispatchEvent(new Event('input'));
    }
}

function actualizarListaProductosAgregados() {
    const contenedor = document.getElementById('listaProductosAgregados');
    const inputsContainer = document.getElementById('productosInputs');

    if (productosAgregados.length === 0) {
        contenedor.innerHTML = '<p class="text-center text-muted my-3"><i class="bi bi-inbox"></i><br>No hay productos agregados</p>';
        inputsContainer.innerHTML = '';
        calcularTotal();
        return;
    }

    let html = '';
    let inputs = '';

    productosAgregados.forEach((producto, index) => {
        const subtotal = producto.precio * producto.cantidad;
        html += `
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <strong class="d-block">${escapeHtml(producto.nombre)}</strong>
                            <small class="text-muted">${escapeHtml(producto.codigo)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="eliminarProducto(${index})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-4">
                            <label class="form-label text-muted small mb-0">Cantidad</label>
                            <input type="number"
                                   class="form-control form-control-sm"
                                   value="${producto.cantidad}"
                                   min="1"
                                   max="${producto.stock}"
                                   onchange="actualizarCantidad(${index}, this.value)">
                        </div>
                        <div class="col-8 text-end">
                            <label class="form-label text-muted small mb-0">Precio</label>
                            <div class="fw-bold text-primary">$${producto.precio.toLocaleString('es-CO')}</div>
                            <small class="text-muted">Stock: ${producto.stock}</small>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Subtotal:</span>
                            <strong>$${subtotal.toLocaleString('es-CO')}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Hidden inputs para enviar al servidor
        inputs += `
            <input type="hidden" name="items[${index}][producto_id]" value="${producto.id}">
            <input type="hidden" name="items[${index}][cantidad]" value="${producto.cantidad}">
            <input type="hidden" name="items[${index}][precio_unitario]" value="${producto.precio}">
        `;
    });

    contenedor.innerHTML = html;
    inputsContainer.innerHTML = inputs;
    calcularTotal();
}

function actualizarCantidad(index, cantidad) {
    cantidad = parseInt(cantidad);

    if (cantidad < 1) {
        cantidad = 1;
    }

    if (cantidad > productosAgregados[index].stock) {
        alert(`Solo hay ${productosAgregados[index].stock} unidades disponibles`);
        cantidad = productosAgregados[index].stock;
    }

    productosAgregados[index].cantidad = cantidad;
    actualizarListaProductosAgregados();
}

function eliminarProducto(index) {
    if (confirm('¿Está seguro de eliminar este producto de la venta?')) {
        productosAgregados.splice(index, 1);
        actualizarListaProductosAgregados();

        // Refrescar la lista de productos para quitar el badge "Agregado"
        const termino = document.getElementById('busquedaProducto').value.trim();
        if (termino.length > 0) {
            document.getElementById('busquedaProducto').dispatchEvent(new Event('input'));
        }
    }
}

function calcularTotal() {
    let total = 0;
    productosAgregados.forEach(producto => {
        total += producto.precio * producto.cantidad;
    });

    document.getElementById('totalVenta').textContent = '$' + total.toLocaleString('es-CO');
}

// Crear nuevo cliente
document.getElementById('btnGuardarCliente').addEventListener('click', function() {
    const nombre = document.getElementById('nuevo_nombre').value.trim();
    const telefono = document.getElementById('nuevo_telefono').value.trim();
    const email = document.getElementById('nuevo_email').value.trim();
    const direccion = document.getElementById('nuevo_direccion').value.trim();
    const ciudad = document.getElementById('nuevo_ciudad').value;

    if (!nombre || !telefono) {
        document.getElementById('erroresCliente').innerHTML = 'Nombre y teléfono son obligatorios';
        document.getElementById('erroresCliente').style.display = 'block';
        return;
    }

    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('telefono', telefono);
    formData.append('email', email);
    formData.append('direccion', direccion);
    formData.append('ciudad', ciudad);

    fetch('<?= APP_URL ?>/ventas/crearCliente', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            seleccionarCliente(data.cliente.id, data.cliente.nombre, data.cliente.telefono, data.cliente.ciudad);
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente')).hide();
            document.getElementById('formNuevoCliente').reset();
            document.getElementById('erroresCliente').style.display = 'none';
        } else {
            document.getElementById('erroresCliente').innerHTML = data.errores.join('<br>');
            document.getElementById('erroresCliente').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear el cliente');
    });
});

// Validación del formulario
document.getElementById('formVenta').addEventListener('submit', function(e) {
    if (productosAgregados.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto a la venta');
        return false;
    }

    if (!document.getElementById('cliente_id').value) {
        e.preventDefault();
        alert('Debe seleccionar un cliente');
        return false;
    }
});
</script>
