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
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="busquedaCliente"
                                           placeholder="Buscar por nombre o teléfono..."
                                           autocomplete="off">
                                </div>
                                <div id="resultadosCliente" class="list-group mt-1" style="position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                                <input type="hidden" name="cliente_id" id="cliente_id" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                <i class="bi bi-person-plus"></i> Nuevo Cliente
                            </button>
                        </div>

                        <div class="col-12">
                            <div id="infoClienteSeleccionado" class="alert alert-info" style="display: none;">
                                <strong>Cliente seleccionado:</strong> <span id="nombreClienteSeleccionado"></span><br>
                                <strong>Teléfono:</strong> <span id="telefonoClienteSeleccionado"></span>
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

                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Buscar Producto</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="busquedaProducto"
                                           placeholder="Buscar por nombre o código..."
                                           autocomplete="off">
                                </div>
                                <div id="resultadosProducto" class="list-group mt-1" style="position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaProductos">
                                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <tr>
                                            <th style="width: 35%">Producto</th>
                                            <th style="width: 15%">Precio</th>
                                            <th style="width: 15%">Cantidad</th>
                                            <th style="width: 15%">Stock</th>
                                            <th style="width: 15%">Subtotal</th>
                                            <th style="width: 5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productosVenta">
                                        <tr id="filaVacia">
                                            <td colspan="6" class="text-center text-muted">
                                                No hay productos agregados. Use el buscador arriba para agregar productos.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-bold">
                                            <td colspan="4" class="text-end">TOTAL:</td>
                                            <td colspan="2" id="totalVenta" style="font-size: 1.2rem; color: #667eea;">$0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

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
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales sobre la venta..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= APP_URL ?>/ventas" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
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
                        <label for="nuevo_nombre" class="form-label fw-bold text-dark">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nuevo_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_telefono" class="form-label fw-bold text-dark">Teléfono *</label>
                        <input type="tel" class="form-control" id="nuevo_telefono" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_email" class="form-label text-dark">Email</label>
                        <input type="email" class="form-control" id="nuevo_email">
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_direccion" class="form-label text-dark">Dirección</label>
                        <textarea class="form-control" id="nuevo_direccion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_ciudad" class="form-label fw-bold text-dark">Ciudad</label>
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
let productosAgregados = [];
let clienteSeleccionado = null;
let timeoutBusqueda = null;

// Búsqueda de clientes
document.getElementById('busquedaCliente').addEventListener('input', function() {
    const termino = this.value.trim();

    clearTimeout(timeoutBusqueda);

    if (termino.length < 2) {
        document.getElementById('resultadosCliente').style.display = 'none';
        return;
    }

    timeoutBusqueda = setTimeout(() => {
        buscarClientes(termino);
    }, 300);
});

function buscarClientes(termino) {
    const formData = new FormData();
    formData.append('termino', termino);

    fetch('<?= APP_URL ?>/ventas/buscarCliente', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarResultadosClientes(data.clientes);
    })
    .catch(error => console.error('Error:', error));
}

function mostrarResultadosClientes(clientes) {
    const contenedor = document.getElementById('resultadosCliente');

    if (clientes.length === 0) {
        contenedor.style.display = 'none';
        return;
    }

    let html = '';
    clientes.forEach(cliente => {
        html += `
            <a href="#" class="list-group-item list-group-item-action" onclick="seleccionarCliente(${cliente.id}, '${cliente.nombre}', '${cliente.telefono}'); return false;">
                <strong>${cliente.nombre}</strong><br>
                <small class="text-muted">Tel: ${cliente.telefono}</small>
            </a>
        `;
    });

    contenedor.innerHTML = html;
    contenedor.style.display = 'block';
}

function seleccionarCliente(id, nombre, telefono) {
    document.getElementById('cliente_id').value = id;
    document.getElementById('busquedaCliente').value = nombre;
    document.getElementById('resultadosCliente').style.display = 'none';

    document.getElementById('nombreClienteSeleccionado').textContent = nombre;
    document.getElementById('telefonoClienteSeleccionado').textContent = telefono;
    document.getElementById('infoClienteSeleccionado').style.display = 'block';

    clienteSeleccionado = {id, nombre, telefono};
}

// Búsqueda de productos
document.getElementById('busquedaProducto').addEventListener('input', function() {
    const termino = this.value.trim();

    clearTimeout(timeoutBusqueda);

    if (termino.length < 2) {
        document.getElementById('resultadosProducto').style.display = 'none';
        return;
    }

    timeoutBusqueda = setTimeout(() => {
        buscarProductos(termino);
    }, 300);
});

function buscarProductos(termino) {
    const formData = new FormData();
    formData.append('termino', termino);

    fetch('<?= APP_URL ?>/ventas/buscarProducto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarResultadosProductos(data.productos);
    })
    .catch(error => console.error('Error:', error));
}

function mostrarResultadosProductos(productos) {
    const contenedor = document.getElementById('resultadosProducto');

    if (productos.length === 0) {
        contenedor.innerHTML = '<div class="list-group-item text-muted">No se encontraron productos con stock</div>';
        contenedor.style.display = 'block';
        return;
    }

    let html = '';
    productos.forEach(producto => {
        html += `
            <a href="#" class="list-group-item list-group-item-action" onclick='agregarProducto(${JSON.stringify(producto)}); return false;'>
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>${producto.nombre}</strong><br>
                        <small class="text-muted">${producto.codigo_producto} - ${producto.categoria}</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-primary">$${parseFloat(producto.precio).toLocaleString('es-CO')}</strong><br>
                        <small class="text-success">Stock: ${producto.stock}</small>
                    </div>
                </div>
            </a>
        `;
    });

    contenedor.innerHTML = html;
    contenedor.style.display = 'block';
}

function agregarProducto(producto) {
    // Verificar si ya está agregado
    const yaAgregado = productosAgregados.find(p => p.id === producto.id);
    if (yaAgregado) {
        alert('Este producto ya está agregado. Modifique la cantidad en la tabla.');
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

    document.getElementById('busquedaProducto').value = '';
    document.getElementById('resultadosProducto').style.display = 'none';

    actualizarTablaProductos();
}

function actualizarTablaProductos() {
    const tbody = document.getElementById('productosVenta');
    const filaVacia = document.getElementById('filaVacia');

    if (productosAgregados.length === 0) {
        filaVacia.style.display = '';
        calcularTotal();
        return;
    }

    filaVacia.style.display = 'none';

    let html = '';
    productosAgregados.forEach((producto, index) => {
        const subtotal = producto.precio * producto.cantidad;
        html += `
            <tr>
                <td>
                    <strong>${producto.nombre}</strong><br>
                    <small class="text-muted">${producto.codigo}</small>
                    <input type="hidden" name="items[${index}][producto_id]" value="${producto.id}">
                    <input type="hidden" name="items[${index}][precio_unitario]" value="${producto.precio}">
                </td>
                <td class="text-end">$${producto.precio.toLocaleString('es-CO')}</td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm"
                           name="items[${index}][cantidad]"
                           value="${producto.cantidad}"
                           min="1"
                           max="${producto.stock}"
                           onchange="actualizarCantidad(${index}, this.value)"
                           required>
                </td>
                <td class="text-center">
                    <span class="badge bg-${producto.stock > 5 ? 'success' : 'warning'}">${producto.stock} disponibles</span>
                </td>
                <td class="text-end fw-bold">$${subtotal.toLocaleString('es-CO')}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
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
    actualizarTablaProductos();
}

function eliminarProducto(index) {
    if (confirm('¿Está seguro de eliminar este producto de la venta?')) {
        productosAgregados.splice(index, 1);
        actualizarTablaProductos();
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
            seleccionarCliente(data.cliente.id, data.cliente.nombre, data.cliente.telefono);
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

// Cerrar resultados al hacer click fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('#busquedaCliente') && !e.target.closest('#resultadosCliente')) {
        document.getElementById('resultadosCliente').style.display = 'none';
    }
    if (!e.target.closest('#busquedaProducto') && !e.target.closest('#resultadosProducto')) {
        document.getElementById('resultadosProducto').style.display = 'none';
    }
});
</script>
