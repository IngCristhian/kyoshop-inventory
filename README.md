# 🛍️ KyoShop Inventory System

Sistema de inventario web para tienda de ropa desarrollado en **PHP puro** con arquitectura MVC simple. Diseñado específicamente para ser fácil de desplegar en **hosting compartido cPanel**.

![Dashboard](https://img.shields.io/badge/Dashboard-Responsive-blue)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-violet)
![CI/CD](https://img.shields.io/badge/CI%2FCD-GitHub%20Actions-green)

## 🎯 Características Principales

### 📦 Gestión Completa de Productos
- ✅ **CRUD completo** (Crear, Leer, Actualizar, Eliminar)
- ✅ **Sistema de imágenes** con validación y preview
- ✅ **Código de producto único** autogenerado
- ✅ **Categorización** por tipo de prenda
- ✅ **Gestión de stock** con alertas de stock bajo
- ✅ **Precios** y **descripciones** detalladas

### 🔍 Búsqueda y Filtros Avanzados
- 🔎 **Búsqueda en tiempo real** por nombre, descripción o código
- 🏷️ **Filtros por categoría** dinámicos
- ⚠️ **Filtro de stock bajo** para control de inventario
- 📄 **Paginación** eficiente para grandes catálogos

### 📊 Dashboard con Estadísticas
- 📈 **Métricas en tiempo real** del inventario
- 💰 **Valor total del inventario**
- 📦 **Productos con stock bajo**
- 📅 **Productos agregados recientemente**

### 🎨 Interfaz Moderna y Responsive
- 📱 **Diseño responsive** con Bootstrap 5.3
- 🎯 **UX intuitiva** para uso interno
- 🌈 **Tema personalizado** con gradientes
- ⚡ **Carga rápida** optimizada

## 🛠️ Stack Tecnológico

| Componente | Tecnología | Versión |
|------------|------------|---------|
| **Backend** | PHP Puro | 8.0+ |
| **Frontend** | HTML5 + Bootstrap | 5.3 |
| **Database** | MySQL | 8.0+ |
| **CSS** | Custom + Bootstrap | - |
| **JavaScript** | Vanilla JS | ES6+ |
| **Hosting** | cPanel Compatible | - |

## 📁 Estructura del Proyecto

```
kyoshop-inventory/
├── 📄 index.php                    # Front Controller
├── ⚙️ config/
│   ├── database.php               # Configuración de BD
│   └── config.php                 # Configuraciones generales
├── 🏗️ models/
│   └── Producto.php               # Modelo de datos
├── 🎮 controllers/
│   └── ProductoController.php     # Lógica de negocio
├── 🎨 views/
│   ├── layouts/master.php         # Layout principal
│   ├── dashboard.php              # Dashboard
│   ├── productos/                 # Vistas CRUD
│   └── 404.php                    # Página de error
├── 🎭 assets/
│   ├── css/style.css              # Estilos personalizados
│   └── js/app.js                  # JavaScript principal
├── 📤 uploads/                     # Imágenes de productos
├── 🗄️ sql/
│   └── database.sql               # Script de BD
├── 📋 DEPLOY.md                   # Guía de despliegue
└── 📖 README.md                   # Este archivo
```

## 🚀 Instalación Rápida

### Desarrollo Local

1. **Clonar el repositorio**
```bash
git clone [repo-url]
cd kyoshop-inventory
```

2. **Configurar base de datos**
```bash
# Importar schema
mysql -u root -p < sql/database.sql
```

3. **Configurar aplicación**
```php
// Editar config/database.php
private $host = 'localhost';
private $db_name = 'kyoshop_inventory';
private $username = 'root';
private $password = 'tu_password';

// Editar config/config.php
define('APP_URL', 'http://localhost/kyoshop-inventory');
```

4. **Iniciar servidor**
```bash
php -S localhost:8000
```

5. **Acceder a la aplicación**
```
http://localhost:8000
```

### Despliegue en cPanel

👉 **[Ver guía completa de despliegue](DEPLOY.md)**

Resumen rápido:
1. Subir archivos via File Manager
2. Crear base de datos MySQL
3. Importar `sql/database.sql`
4. Configurar `config/database.php`
5. Establecer permisos en directorio `uploads/`

## 🎮 Uso del Sistema

### Dashboard Principal
- **Acceso**: `https://tu-dominio.com/kyoshop-inventory/`
- **Funciones**: Estadísticas generales, productos recientes, alertas de stock

### Gestión de Productos
- **Listar**: `/productos` - Ver todos los productos con filtros
- **Crear**: `/productos/crear` - Agregar nuevo producto
- **Editar**: `/productos/editar/{id}` - Modificar producto existente
- **Eliminar**: Botón de eliminar (soft delete)

### Características de Seguridad
- 🔒 **CSRF Protection** en todos los formularios
- 🛡️ **SQL Injection** protección con prepared statements
- 🧹 **Sanitización** de inputs automática
- 📁 **Validación de archivos** para uploads

## 🗄️ Base de Datos

### Tabla Principal: `productos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT AUTO_INCREMENT | ID único |
| `nombre` | VARCHAR(255) | Nombre del producto |
| `descripcion` | TEXT | Descripción detallada |
| `precio` | DECIMAL(10,2) | Precio en pesos |
| `stock` | INT | Cantidad disponible |
| `imagen` | VARCHAR(255) | Nombre del archivo de imagen |
| `categoria` | VARCHAR(100) | Categoría del producto |
| `talla` | VARCHAR(50) | Talla (S, M, L, etc.) |
| `color` | VARCHAR(50) | Color del producto |
| `codigo_producto` | VARCHAR(100) | Código único |
| `activo` | BOOLEAN | Estado (soft delete) |
| `fecha_creacion` | TIMESTAMP | Fecha de creación |
| `fecha_actualizacion` | TIMESTAMP | Última modificación |

### Datos de Ejemplo
El sistema incluye productos de ejemplo para testing:
- Camisetas básicas
- Jeans clásicos
- Blusas elegantes
- Chaquetas deportivas
- Faldas casuales

## 🔧 Personalización

### Cambiar Colores del Tema
Editar `assets/css/style.css`:
```css
:root {
    --primary-gradient: linear-gradient(135deg, #tu-color1 0%, #tu-color2 100%);
}
```

### Agregar Nuevas Categorías
Las categorías se generan automáticamente basadas en los productos existentes.

### Modificar Configuraciones
Editar `config/config.php`:
```php
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ITEMS_PER_PAGE', 20); // 20 productos por página
```

## 📈 Próximas Características

- [ ] 🔐 Sistema de usuarios y autenticación
- [ ] 📊 Reportes de inventario en PDF
- [ ] 🔌 API REST para integraciones
- [ ] 📱 Notificaciones push para stock bajo
- [ ] 📦 Integración con códigos de barras
- [ ] 💸 Registro de ventas básico

## 🤝 Contribución

Este es un proyecto de código abierto. Las contribuciones son bienvenidas:

1. Fork el proyecto
2. Crear branch para feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.

## 🆘 Soporte

### Problemas Comunes
- **Error de conexión BD**: Verificar credenciales en `config/database.php`
- **Imágenes no cargan**: Verificar permisos del directorio `uploads/` (755)
- **Páginas en blanco**: Activar `error_reporting` para debugging

### Documentación Adicional
- 📘 **[Guía de Despliegue](DEPLOY.md)** - Instrucciones detalladas para cPanel
- 🔧 **[CLAUDE.md](CLAUDE.md)** - Documentación técnica para desarrolladores

### Contacto
- 💼 **Desarrollador**: [Tu Nombre]
- 📧 **Email**: tu-email@ejemplo.com
- 🐛 **Issues**: [GitHub Issues]

---

**⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub!**

Desarrollado con ❤️ para tiendas de ropa que necesitan un sistema de inventario simple y efectivo.