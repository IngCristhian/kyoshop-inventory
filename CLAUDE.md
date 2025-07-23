# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**KyoShop Inventory System** - Sistema de inventario web para tienda de ropa desarrollado en PHP puro con arquitectura MVC simple. Diseñado para ser fácil de desplegar en hosting compartido cPanel.

### Tech Stack
- **Backend**: PHP 8.0+ (sin frameworks, MVC puro)
- **Frontend**: HTML5 + Bootstrap 5.3 + JavaScript vanilla
- **Database**: MySQL 8.0+
- **Hosting**: Optimizado para cPanel/hosting compartido

## Development Commands

### Database Setup
```sql
-- Ejecutar en phpMyAdmin o terminal MySQL
mysql -u username -p database_name < sql/database.sql
```

### Local Development
```bash
# Servidor local PHP
php -S localhost:8000

# O usar XAMPP/WAMP con documentRoot apuntando al proyecto
```

### Configuration
1. Editar `config/database.php` con credenciales de BD
2. Ajustar `APP_URL` en `config/config.php`
3. Crear directorio `uploads/` con permisos 755

## Architecture

### MVC Structure
```
/kyoshop-inventory/
├── index.php                 # Front Controller (enrutamiento)
├── config/
│   ├── database.php         # Singleton de conexión PDO
│   └── config.php           # Configuraciones y helpers
├── models/
│   └── Producto.php         # Modelo con validaciones y queries
├── controllers/
│   └── ProductoController.php # Lógica CRUD completa
├── views/
│   ├── layouts/master.php   # Layout principal con Bootstrap
│   ├── dashboard.php        # Dashboard con estadísticas
│   └── productos/           # Vistas CRUD
└── assets/                  # CSS/JS personalizados
```

### Database Schema
- **Tabla principal**: `productos` con campos: id, nombre, descripcion, precio, stock, imagen, categoria, talla, color, codigo_producto, activo, timestamps
- **Índices**: categoria, codigo_producto, activo, stock para optimizar consultas
- **Vista**: `estadisticas_productos` para dashboard

### Key Features Implemented
1. **CRUD completo** de productos con validaciones
2. **Sistema de imágenes** con validación y redimensionado
3. **Dashboard** con estadísticas en tiempo real
4. **Búsqueda y filtros** por categoría, stock bajo, texto libre
5. **Paginación** eficiente con LIMIT/OFFSET
6. **Seguridad**: CSRF tokens, sanitización, prepared statements
7. **Responsive design** con Bootstrap 5.3

### Security Measures
- PDO prepared statements contra SQL injection
- CSRF tokens en formularios
- Sanitización de inputs con `htmlspecialchars()`
- Validación de tipos de archivo para uploads
- Soft delete (no eliminación física)

## Common Development Patterns

### Adding New Models
1. Crear clase en `models/` extendiendo patrón Repository
2. Inyectar `Database::getInstance()` en constructor
3. Usar métodos `fetchAll()`, `fetch()`, `insert()`, `execute()`

### Adding Controllers
1. Crear en `controllers/` con métodos públicos para cada acción
2. Usar `cargarVista()` para renderizar con layout
3. Validar CSRF en formularios POST
4. Implementar `procesarDatos()` para sanitización

### Adding Views
1. Crear en `views/` usando template con `$contenido`
2. Usar helpers: `formatPrice()`, `sanitize()`, `generateCSRFToken()`
3. Mantener consistencia con Bootstrap classes

## Database Operations

### Connection Pattern
```php
$db = getDB(); // Singleton instance
$productos = $db->fetchAll("SELECT * FROM productos WHERE activo = 1");
```

### Common Queries
- List with pagination: `LIMIT :limite OFFSET :offset`
- Search: `WHERE nombre LIKE :busqueda OR descripcion LIKE :busqueda`
- Filters: Build dynamic WHERE clauses with arrays
- Stats: Use aggregate functions with conditional COUNT

## File Upload System

- Directory: `uploads/` (must have 755 permissions)
- Max size: 5MB (configurable in `config.php`)
- Allowed: JPG, PNG, GIF
- Naming: `uniqid('producto_') . '.' . extension`
- Validation in `validateImage()` helper

## Routing System

Simple switch-based routing in `index.php`:
- Static routes: `'productos'` → `ProductoController@index`
- Dynamic routes: `preg_match('/productos\/editar\/(\d+)/', $path, $matches)`
- Parameters passed to controller methods

## Error Handling

- Development: `error_reporting(E_ALL)` in index.php
- Production: Log errors, show generic messages
- 404: Custom view in `views/404.php`
- Database errors: Try-catch with logging

## Deployment Notes

### cPanel Setup
1. Upload all files to `public_html/kyoshop-inventory/`
2. Create MySQL database and user via cPanel
3. Import `sql/database.sql`
4. Update `config/database.php` with cPanel credentials
5. Set `uploads/` directory permissions to 755
6. Update `APP_URL` in `config/config.php`

### File Permissions
- PHP files: 644
- Directories: 755
- `uploads/`: 755 (writable)
- `config/`: 644 (readable)

## Performance Considerations

- Uses database indexes on frequently queried columns
- Pagination limits query results
- Image uploads optimized with file validation
- CSS/JS minification for production
- Database connection reuses single PDO instance

## Future Extensibility

The codebase is designed for easy extension:
- Add new models following Repository pattern
- Controllers use consistent method signatures
- Views share common layout and helpers
- Database supports additional tables/relationships
- Ready for API endpoints (JSON responses implemented)

This system prioritizes simplicity, security, and maintainability over complex abstractions.