# Módulo de Ventas - KyoShop Inventory System

## Descripción

Módulo completo de gestión de ventas para el sistema de inventario KyoShop. Incluye:

- ✅ Registro de ventas con descuento automático de stock
- ✅ Gestión de clientes
- ✅ Búsqueda dinámica de productos y clientes
- ✅ Múltiples métodos de pago
- ✅ Control de estados de pago
- ✅ Cancelación de ventas con devolución de stock
- ✅ Generación de facturas imprimibles
- ✅ Estadísticas y reportes
- ✅ Registro automático en historial de movimientos

## Instalación

### 1. Ejecutar Script SQL

Debes ejecutar el script SQL para crear las tablas necesarias en la base de datos:

```bash
# En desarrollo local
mysql -u root -p kyoshop_inventory < sql/modulo_ventas.sql

# En producción (vía SSH)
mysql -u kyosankk_inv -p kyosankk_inventory < sql/modulo_ventas.sql
```

Esto creará las siguientes tablas:
- `clientes` - Información de clientes
- `ventas` - Registro de ventas (header)
- `ventas_detalle` - Detalle de productos vendidos
- Vistas:
  - `estadisticas_ventas` - Estadísticas de los últimos 30 días
  - `top_productos_vendidos` - Top 10 productos más vendidos

### 2. Verificar Instalación

Verifica que las tablas se crearon correctamente:

```sql
SHOW TABLES LIKE '%ventas%';
SHOW TABLES LIKE 'clientes';

-- Ver estructura
DESC ventas;
DESC ventas_detalle;
DESC clientes;

-- Verificar clientes de ejemplo
SELECT * FROM clientes;
```

## Estructura de Archivos Creados

```
kyoshop-inventory/
├── sql/
│   └── modulo_ventas.sql            # Script de creación de BD
├── models/
│   ├── Cliente.php                  # Modelo de clientes
│   └── Venta.php                    # Modelo de ventas con transacciones
├── controllers/
│   └── VentaController.php          # Controlador de ventas
├── views/
│   └── ventas/
│       ├── index.php                # Listado de ventas
│       ├── formulario.php           # Formulario de nueva venta
│       ├── detalle.php              # Detalle de una venta
│       └── factura.php              # Factura imprimible
├── config/
│   └── database.php                 # Métodos de transacciones agregados
├── views/layouts/
│   └── master.php                   # Menú actualizado con Ventas
└── index.php                        # Rutas agregadas
```

## Funcionalidades Principales

### 1. Crear Nueva Venta

**Ruta:** `/ventas/crear`

**Características:**
- Búsqueda dinámica de clientes (autocompletar)
- Opción para crear cliente nuevo desde modal
- Búsqueda dinámica de productos con stock
- Validación de stock disponible en tiempo real
- Cálculo automático de totales
- Múltiples métodos de pago: Transferencia, Contra Entrega, Efectivo, Tarjeta
- Estados de pago: Pendiente, Pagado

**Proceso:**
1. Seleccionar o crear cliente
2. Buscar y agregar productos (con validación de stock)
3. Seleccionar método de pago
4. Registrar venta

**Al guardar la venta:**
- Se descuenta automáticamente el stock de productos
- Se genera número de venta único (formato: VEN-YYYYMMDD-XXXX)
- Se registran movimientos en historial
- Se usa transacción para garantizar integridad

### 2. Listado de Ventas

**Ruta:** `/ventas`

**Características:**
- Estadísticas de últimos 30 días
- Filtros por:
  - Búsqueda (número de venta, cliente)
  - Método de pago
  - Estado de pago
  - Rango de fechas
- Paginación
- Ver detalles de cada venta
- Ver/imprimir factura

### 3. Detalle de Venta

**Ruta:** `/ventas/ver/{id}`

**Características:**
- Información completa del cliente
- Lista de productos vendidos
- Totales y subtotales
- Cambiar estado de pago (Pendiente ↔ Pagado)
- Cancelar venta (devuelve stock automáticamente)
- Imprimir factura

### 4. Cancelación de Ventas

**Funcionalidad:**
- Solo se pueden cancelar ventas que NO estén ya canceladas
- Al cancelar:
  - Se devuelve el stock de todos los productos
  - Se marca la venta como "cancelada"
  - Se registra motivo de cancelación
  - Se registran movimientos de devolución en historial
- La cancelación NO se puede revertir

### 5. Factura Imprimible

**Ruta:** `/ventas/factura/{id}`

**Características:**
- Formato profesional listo para imprimir
- Incluye toda la información de la venta
- Botón de impresión integrado
- Responsive (se adapta a tamaño de página)

## Base de Datos - Detalles Técnicos

### Tabla: `clientes`
```sql
- id (PK, AUTO_INCREMENT)
- nombre (VARCHAR 255, NOT NULL)
- telefono (VARCHAR 20, NOT NULL, INDEX)
- email (VARCHAR 255, NULL)
- direccion (TEXT, NULL)
- ciudad (VARCHAR 100, DEFAULT 'Medellín')
- activo (TINYINT, DEFAULT 1)
- fecha_creacion, fecha_actualizacion
```

### Tabla: `ventas`
```sql
- id (PK, AUTO_INCREMENT)
- cliente_id (FK clientes, NOT NULL)
- usuario_id (FK usuarios, NOT NULL)
- numero_venta (VARCHAR 50, UNIQUE, NOT NULL, INDEX)
- fecha_venta (DATETIME, DEFAULT CURRENT_TIMESTAMP)
- subtotal, impuestos, total (DECIMAL 12,2)
- metodo_pago (ENUM: transferencia, contra_entrega, efectivo, tarjeta)
- estado_pago (ENUM: pendiente, pagado, cancelado)
- observaciones (TEXT, NULL)
- fecha_creacion, fecha_actualizacion
```

### Tabla: `ventas_detalle`
```sql
- id (PK, AUTO_INCREMENT)
- venta_id (FK ventas, CASCADE ON DELETE)
- producto_id (FK productos, RESTRICT ON DELETE)
- cantidad (INT, NOT NULL)
- precio_unitario (DECIMAL 10,2, NOT NULL)
- subtotal (DECIMAL 12,2, NOT NULL)
- fecha_creacion
```

## Validaciones y Seguridad

### Validaciones del Sistema:
1. ✅ Cliente obligatorio
2. ✅ Mínimo 1 producto en la venta
3. ✅ Stock suficiente antes de crear venta
4. ✅ Cantidades mayores a 0
5. ✅ Precios mayores a 0
6. ✅ Método de pago válido
7. ✅ CSRF token en todos los formularios

### Transacciones:
- Crear venta usa transacciones (BEGIN, COMMIT, ROLLBACK)
- Si falla algún paso, se revierten todos los cambios
- Garantiza integridad de datos

### Restricciones:
- No se pueden eliminar ventas (solo cancelar)
- No se pueden eliminar clientes con ventas registradas
- No se pueden eliminar productos con ventas registradas

## Historial de Movimientos

Cada venta y cancelación se registra automáticamente en `historial_movimientos`:

**Al crear venta:**
- tipo_movimiento: 'venta'
- cantidad: negativa (descuento)
- motivo: "Venta #VEN-20250117-0001"

**Al cancelar venta:**
- tipo_movimiento: 'devolucion'
- cantidad: positiva (incremento)
- motivo: "Cancelación venta #VEN-20250117-0001 - {motivo}"

## Uso y Flujo de Trabajo

### Flujo Típico:

1. **Usuario ingresa a Ventas** (`/ventas`)
   - Ve estadísticas y listado de ventas
   - Puede filtrar ventas existentes

2. **Click en "Nueva Venta"** (`/ventas/crear`)
   - Busca cliente o crea uno nuevo
   - Busca productos y los agrega
   - Sistema valida stock en tiempo real
   - Selecciona método de pago
   - Registra la venta

3. **Sistema procesa:**
   - Descuenta stock automáticamente
   - Genera número de venta único
   - Registra en historial
   - Redirige a detalle de venta

4. **Desde detalle de venta:**
   - Puede imprimir factura
   - Puede cambiar estado de pago
   - Puede cancelar venta (si necesario)

### Ejemplo de Uso:

```
1. Cliente llama para hacer un pedido
2. Vendedor entra a /ventas/crear
3. Busca cliente por nombre/teléfono
4. Si no existe, lo crea con el modal
5. Busca productos y los va agregando
6. Revisa totales
7. Selecciona método de pago (ej: "Contra Entrega")
8. Marca como "Pendiente" (pagará al recibir)
9. Guarda venta
10. Imprime factura para despacho
11. Cuando cliente pague, cambia estado a "Pagado"
```

## Estadísticas Disponibles

En `/ventas` se muestran estadísticas de los últimos 30 días:
- Total de ventas realizadas
- Monto total vendido
- Ticket promedio por venta
- Clientes únicos que compraron
- Ventas por estado (pagadas, pendientes, canceladas)
- Ventas por método de pago

## Mejoras Futuras Sugeridas

1. **Generación PDF de facturas** con librería TCPDF
2. **WhatsApp API** para enviar factura al cliente
3. **Reportes avanzados** (ventas por período, por vendedor, etc.)
4. **Descuentos y promociones**
5. **Gestión de créditos** (ventas a crédito)
6. **Integración contable** (export a Excel/CSV)
7. **Dashboard de vendedor** (mis ventas del día)
8. **Notas de crédito** para devoluciones parciales

## Notas Importantes

⚠️ **Stock automático:** El stock se descuenta automáticamente al crear la venta. Si cancelas una venta, el stock se devuelve automáticamente.

⚠️ **Números de venta:** Se generan automáticamente con formato `VEN-YYYYMMDD-XXXX` (ejemplo: VEN-20250117-0001). Son únicos e incrementales por día.

⚠️ **Transacciones:** Las ventas usan transacciones de base de datos. Si algo falla (stock insuficiente, error de BD, etc.), toda la operación se revierte.

⚠️ **Clientes de ejemplo:** El script SQL inserta 3 clientes de ejemplo para testing. Puedes eliminarlos después.

## Troubleshooting

### Error: "Call to undefined method beginTransaction()"
**Solución:** Asegúrate de que el archivo `config/database.php` tenga los métodos de transacciones agregados.

### Error: "Table 'ventas' doesn't exist"
**Solución:** Ejecuta el script SQL `sql/modulo_ventas.sql`

### Error: "Stock insuficiente"
**Causa:** El producto no tiene stock disponible. Verifica el stock antes de agregar a la venta.

### Búsqueda de clientes/productos no funciona
**Causa:** Revisa la consola del navegador por errores JavaScript. Verifica que las rutas AJAX estén correctas en `index.php`.

## Soporte

Para reportar bugs o solicitar nuevas funcionalidades, contacta al desarrollador o crea un issue en el repositorio.

---

**Desarrollado para KyoShop Inventory System**
Versión: 1.0
Fecha: Enero 2025
