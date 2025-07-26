# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**KyoShop Inventory System** - Sistema de inventario web para tienda de ropa desarrollado en PHP puro con arquitectura MVC simple. Diseñado para ser fácil de desplegar en hosting compartido cPanel.

### Tech Stack
- **Backend**: PHP 8.2+ (sin frameworks, MVC puro)
- **Frontend**: HTML5 + Bootstrap 5.3 + JavaScript vanilla
- **Database**: MySQL 8.0+
- **Hosting**: Optimizado para cPanel/hosting compartido

### Project Status
✅ **MVP COMPLETADO** (January 2025)
- Sistema completamente funcional en desarrollo local
- Todas las características core implementadas
- Testing manual completado exitosamente
- Listo para producción

🚀 **DEPLOYED TO PRODUCTION** (January 2025)
- **URL de producción**: https://inventory.kyoshop.co
- **Hosting**: Namecheap Shared Hosting
- **Deployment status**: En proceso...

## Development Environment Setup (macOS)

### Local Development Stack
- **PHP**: Installed via Homebrew (`/opt/homebrew/opt/php@8.2/bin/php`)
- **MySQL**: Local installation (password: `0309`)
- **Server**: PHP built-in server (`php -S localhost:8000`)
- **Database**: `kyoshop_inventory` with sample data

### Database Configuration
```php
// config/database.php (local)
private $host = 'localhost';
private $db_name = 'kyoshop_inventory';
private $username = 'root';
private $password = '0309';
```

### Application Configuration
```php
// config/config.php (local)
define('APP_URL', 'http://localhost:8000');
```

## Production Environment Setup (Namecheap)

### Production Hosting Details
- **Hosting Provider**: Namecheap Shared Hosting
- **Domain**: kyoshop.co
- **Subdomain**: inventory.kyoshop.co
- **Server**: server277.web-hosting.com
- **SSH User**: kyosankk
- **Document Root**: /home/kyosankk/public_html/inventory/

### Production Database Configuration
```php
// config/database.php (production)
private $host = 'localhost';
private $db_name = 'kyosankk_inventory';
private $username = 'kyosankk_inv';
private $password = '[production_password]';
```

### Production Application Configuration
```php
// config/config.php (production)
define('APP_URL', 'https://inventory.kyoshop.co');
```

### SSH Access
```bash
# Connect to production server
ssh kyosankk@server277.web-hosting.com

# Navigate to project directory
cd /home/kyosankk/public_html/inventory/
```

## Development Commands

### Starting Local Environment
```bash
# Start MySQL server
sudo /usr/local/mysql/support-files/mysql.server start

# Start PHP development server
cd /path/to/kyoshop-inventory
/opt/homebrew/opt/php@8.2/bin/php -S localhost:8000

# Access application
open http://localhost:8000
```

### Database Setup
```sql
-- Create database
CREATE DATABASE kyoshop_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema and sample data
mysql -u root -p kyoshop_inventory < sql/database.sql

-- Verify installation
mysql -u root -p -e "USE kyoshop_inventory; SELECT COUNT(*) as productos FROM productos;"
```

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
├── assets/                  # CSS/JS personalizados
├── uploads/                 # Imágenes de productos (gitignored)
└── sql/database.sql         # Script completo de BD
```

### Database Schema
- **Tabla principal**: `productos` con campos: id, nombre, descripcion, precio, stock, imagen, categoria, talla, color, codigo_producto, activo, timestamps
- **Índices**: categoria, codigo_producto, activo, stock para optimizar consultas
- **Vista**: `estadisticas_productos` para dashboard
- **Sample data**: 5 productos de ejemplo para testing

### Key Features Implemented ✅
1. **CRUD completo** de productos con validaciones
2. **Sistema de imágenes** con validación y preview (uploads/ directory)
3. **Dashboard** con estadísticas en tiempo real
4. **Búsqueda y filtros** por categoría, stock bajo, texto libre
5. **Paginación** eficiente con LIMIT/OFFSET
6. **Seguridad**: CSRF tokens, sanitización, prepared statements
7. **Responsive design** con Bootstrap 5.3
8. **File uploads** funcionando correctamente

### Security Measures
- PDO prepared statements contra SQL injection
- CSRF tokens en formularios
- Sanitización de inputs con `htmlspecialchars()`
- Validación de tipos de archivo para uploads
- Soft delete (no eliminación física)

## Git Strategy

### Branch Structure (GitHub Flow Adaptado)
```
main (producción estable)
├── develop (desarrollo continuo)
├── feature/nueva-funcionalidad
├── hotfix/bug-critico
└── release/v1.1.0
```

### Workflow
1. **Features**: `feature/nombre` → `develop` → `main`
2. **Hotfixes**: `hotfix/nombre` → `main` + `develop`
3. **Releases**: `develop` → `release/vX.X.X` → `main`

### File Management
- **Code**: All tracked in Git
- **Images**: `uploads/` ignored, only `.gitkeep` tracked
- **Config**: Local configs not tracked (database passwords)

## Testing Status

### Manual Testing Completed ✅
- ✅ Dashboard loading with statistics
- ✅ Product CRUD operations
- ✅ Image upload and display
- ✅ Search and filtering
- ✅ Pagination
- ✅ Responsive design
- ✅ Form validations
- ✅ Error handling

### Backend Functionality Verified ✅
- ✅ Database connections working
- ✅ File uploads to `uploads/` directory
- ✅ CSRF protection active
- ✅ Data sanitization working
- ✅ Soft delete implementation
- ✅ Statistics calculations accurate

## Deployment Plans

### ✅ Current: SSH + cPanel Hybrid Deployment (COMPLETED)
**Production Environment Setup:**
1. ✅ Created subdomain `inventory.kyoshop.co` via cPanel
2. ✅ Created MySQL database `kyosankk_inventory` 
3. ✅ Created MySQL user `kyosankk_inv` with ALL PRIVILEGES
4. ✅ SSH access configured to `/home/kyosankk/public_html/inventory/`

**Deployment Status:**
- ✅ Upload project files via SSH/SCP
- ✅ Created .htaccess with URL rewriting rules (fixed 404 errors)
- ✅ Configure production database credentials via environment variables
- ✅ Import `sql/database.sql` via SSH
- ✅ Set proper file permissions (uploads/ directory)
- ✅ Test functionality on https://inventory.kyoshop.co

**Production Deployment Issues Resolved:**
- **404 Errors Fixed**: Missing .htaccess with mod_rewrite rules
- **Solution**: Created production .htaccess with RewriteEngine rules to redirect all requests to index.php

### Production Deployment Commands
```bash
# Upload project files from local
scp -r * kyosankk@server277.web-hosting.com:/home/kyosankk/public_html/inventory/

# Import database via SSH
mysql -u kyosankk_inv -p kyosankk_inventory < sql/database.sql

# Set permissions
chmod 755 uploads/
chmod 644 config/*.php
chmod 644 .htaccess
```

### Common Production Issues & Solutions

#### 404 Errors on Routes
**Problem**: Apache returns 404 for URLs like `/productos`, `/dashboard`
**Cause**: Missing .htaccess with mod_rewrite rules for PHP routing
**Solution**: 
1. Create .htaccess with RewriteEngine rules
2. Ensure mod_rewrite is enabled on hosting
3. Set proper file permissions (644)

```bash
# If .htaccess conflicts during git pull:
mv .htaccess .htaccess.backup
git pull origin main
# Edit new .htaccess with production credentials
nano .htaccess
```

#### Database Connection Issues
**Problem**: Environment variables not loading
**Cause**: .htaccess not configured with production credentials
**Solution**: Edit .htaccess SetEnv variables:
```apache
SetEnv DB_HOST "localhost"
SetEnv DB_NAME "kyosankk_inventory" 
SetEnv DB_USER "kyosankk_inv"
SetEnv DB_PASSWORD "actual_production_password"
SetEnv APP_URL "https://inventory.kyoshop.co"
```

### Future: GitHub Actions Automation
- **Cost**: FREE (under 2000 minutes/month for private repos)
- **Trigger**: Push to `main` branch
- **Action**: SSH deploy to Namecheap automatically
- **Benefits**: Zero-downtime deployments, deployment history

### Deployment Files Ready
- `DEPLOY.md` - Complete cPanel deployment guide
- `README.md` - Project documentation
- `.gitignore` - Properly configured for uploads and system files

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

- **Directory**: `uploads/` (must have 755 permissions)
- **Max size**: 5MB (configurable in `config.php`)
- **Allowed**: JPG, PNG, GIF
- **Naming**: `uniqid('producto_') . '.' . extension`
- **Validation**: `validateImage()` helper
- **Status**: ✅ Fully functional and tested

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

## Performance Considerations

- Uses database indexes on frequently queried columns
- Pagination limits query results
- Image uploads optimized with file validation
- CSS/JS minification ready for production
- Database connection reuses single PDO instance

## Future Roadmap & Improvement Plan

### Phase 1 - Immediate Improvements (Next 1-2 months)
**Priority: HIGH** - Foundation & Production Ready
- [ ] Deploy to production cPanel hosting
- [ ] Setup GitHub Actions for automated deployment
- [ ] Add user authentication system (login/logout)
- [ ] Implement basic reporting features (inventory reports)
- [ ] Add data backup functionality
- [ ] Implement input validation improvements
- [ ] Add system logging for debugging

### Phase 2 - Feature Expansion (Next 3-6 months)
**Priority: MEDIUM** - Business Value Features
- [ ] WhatsApp API integration for low stock notifications
- [ ] Barcode scanning support (mobile-friendly)
- [ ] Multi-location inventory management
- [ ] Advanced reporting with PDF exports
- [ ] Product categories management system
- [ ] Bulk product import/export (CSV/Excel)
- [ ] Advanced search filters (price ranges, dates)
- [ ] Product image gallery (multiple images per product)

### Phase 3 - Enterprise Features (Next 6-12 months)
**Priority: LOW** - Scalability & Integration
- [ ] REST API for third-party integrations
- [ ] Advanced user roles and permissions system
- [ ] Comprehensive audit logging
- [ ] Automated backup scheduling
- [ ] Mobile app (React Native or PWA)
- [ ] Advanced analytics dashboard
- [ ] Integration with accounting software
- [ ] Multi-currency support
- [ ] Supplier management system
- [ ] Purchase order management

### Phase 4 - Advanced Optimization (Future)
**Priority: ENHANCEMENT** - Performance & Scale
- [ ] Database query optimization
- [ ] Caching layer implementation (Redis)
- [ ] CDN integration for image delivery
- [ ] Advanced security measures
- [ ] Load balancing support
- [ ] Microservices architecture migration
- [ ] Real-time notifications system
- [ ] Advanced inventory forecasting
- [ ] Machine learning for demand prediction

## Development Notes

### Tested Environment
- **macOS**: Monterey/Ventura compatible
- **PHP**: 8.2.29 via Homebrew
- **MySQL**: 5.7.24+ (local installation)
- **Browser**: Chrome/Safari compatible

### Known Working Features
- All CRUD operations
- Image upload and display
- Search functionality
- Statistical calculations
- Responsive layout
- Form validations
- Security measures

### Development Best Practices
- Always test on `develop` branch before merging to `main`
- Keep `uploads/` directory in `.gitignore`
- Use CSRF tokens on all forms
- Sanitize all user inputs
- Use prepared statements for all database queries

### Git Commit Guidelines
- **NO incluir co-autoría de Claude** en commits
- Usar mensajes descriptivos y concisos
- Commits deben ser del desarrollador únicamente
- Seguir conventional commits cuando sea posible

### Entorno Profesional - Principios Fundamentales
- **NUNCA editar archivos directamente en producción**
- **TODO cambio debe pasar por Git workflow**: develop → PR → main → deploy
- **Diferentes configuraciones por ambiente**:
  - `develop`: Configuraciones locales (kyoshop_inventory, localhost:8000)
  - `main`: Configuraciones de producción (kyosankk_inventory, inventory.kyoshop.co)
- **Separar SQL por ambiente**: database_local.sql vs database_production.sql
- **Mantener integridad del código**: Cambios controlados y versionados
- **Un deploy profesional SIEMPRE pasa por el repositorio**

This system prioritizes simplicity, security, and maintainability while being production-ready for small to medium-sized clothing store inventories.