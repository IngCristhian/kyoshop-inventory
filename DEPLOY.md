# 🚀 GUÍA DE DESPLIEGUE - KYOSHOP INVENTORY SYSTEM

Esta guía te ayudará a desplegar el sistema de inventario en tu hosting compartido cPanel de **Namecheap** paso a paso.

## 📋 REQUISITOS PREVIOS

✅ **Hosting con cPanel** (Namecheap Shared Hosting)  
✅ **PHP 8.0+** habilitado  
✅ **MySQL 8.0+** disponible  
✅ **Al menos 100MB** de espacio en disco  
✅ **Acceso a phpMyAdmin**  

## 🗂️ PASO 1: PREPARAR ARCHIVOS

### 1.1 Comprimir el proyecto
```bash
# En tu máquina local, comprimir todo el proyecto
zip -r kyoshop-inventory.zip kyoshop-inventory/
# Excluir archivos innecesarios
zip -r kyoshop-inventory.zip kyoshop-inventory/ -x "*.git*" "*.DS_Store*"
```

### 1.2 Verificar estructura antes de subir
```
kyoshop-inventory/
├── index.php
├── config/
├── models/
├── controllers/
├── views/
├── assets/
├── sql/
└── uploads/ (vacío)
```

## 🌐 PASO 2: CONFIGURAR CPANEL

### 2.1 Acceder a cPanel
1. Ve a `https://tu-dominio.com/cpanel`
2. Ingresa tus credenciales de hosting

### 2.2 Subir archivos
1. **File Manager** → **public_html**
2. **Upload** → Seleccionar `kyoshop-inventory.zip`
3. **Extract** el archivo ZIP
4. Mover contenido de la carpeta a `public_html/kyoshop-inventory/`

### 2.3 Verificar estructura subida
```
public_html/
└── kyoshop-inventory/
    ├── index.php
    ├── config/
    ├── models/
    └── ... (resto de archivos)
```

## 🗄️ PASO 3: CONFIGURAR BASE DE DATOS

### 3.1 Crear base de datos MySQL
1. **cPanel** → **MySQL Databases**
2. **Create New Database**: `tu_usuario_kyoshop`
3. **Create Database**

### 3.2 Crear usuario de base de datos
1. **MySQL Users** → **Add New User**
2. **Username**: `tu_usuario_kyoshop_user`
3. **Password**: Generar contraseña segura (guardarla)
4. **Create User**

### 3.3 Asignar permisos
1. **Add User to Database**
2. Seleccionar usuario y base de datos creados
3. **Grant ALL PRIVILEGES**
4. **Make Changes**

### 3.4 Importar esquema
1. **cPanel** → **phpMyAdmin**
2. Seleccionar tu base de datos
3. **Import** → **Choose File**
4. Seleccionar `sql/database.sql`
5. **Go**

✅ **Verificar**: Deberías ver la tabla `productos` con datos de ejemplo

## ⚙️ PASO 4: CONFIGURAR APLICACIÓN

### 4.1 Configurar base de datos

Editar `config/database.php`:
```php
// Cambiar estas líneas:
private $host = 'localhost';
private $db_name = 'tu_usuario_kyoshop'; // Tu nombre de BD
private $username = 'tu_usuario_kyoshop_user'; // Tu usuario
private $password = 'tu_contraseña_segura'; // Tu contraseña
```

### 4.2 Configurar URL de la aplicación

Editar `config/config.php`:
```php
// Cambiar esta línea:
define('APP_URL', 'https://tu-dominio.com/kyoshop-inventory');
```

### 4.3 Configurar permisos de directorios
En **File Manager** → **kyoshop-inventory**:
1. **uploads/** → **Permissions** → **755**
2. **config/** → **Permissions** → **644**
3. Todos los archivos `.php` → **644**

## 🔧 PASO 5: CONFIGURACIONES ADICIONALES

### 5.1 Configurar PHP (si es necesario)
1. **cPanel** → **Select PHP Version**
2. Seleccionar **PHP 8.0** o superior
3. **Extensions** habilitadas:
   - ✅ pdo
   - ✅ pdo_mysql
   - ✅ gd (para imágenes)
   - ✅ fileinfo

### 5.2 Configurar .htaccess (opcional)
Crear `.htaccess` en `/kyoshop-inventory/`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
</IfModule>
```

## ✅ PASO 6: VERIFICAR INSTALACIÓN

### 6.1 Probar acceso
1. Navegar a `https://tu-dominio.com/kyoshop-inventory`
2. Deberías ver el **Dashboard** con estadísticas
3. **Verificar**:
   - ✅ Dashboard carga correctamente
   - ✅ Menú lateral funciona
   - ✅ Lista de productos muestra datos
   - ✅ Formulario de crear producto funciona

### 6.2 Probar funcionalidades
1. **Crear producto** con imagen
2. **Editar producto** existente
3. **Buscar** por nombre o categoría
4. **Filtrar** por stock bajo
5. **Eliminar** producto (soft delete)

### 6.3 Verificar subida de imágenes
1. Crear producto con imagen
2. **Verificar** que aparece en `/uploads/`
3. **Verificar** que se muestra correctamente

## 🐛 SOLUCIÓN DE PROBLEMAS COMUNES

### Error: "Connection failed"
**Causa**: Credenciales de BD incorrectas  
**Solución**: Verificar `config/database.php`

### Error: "Permission denied" al subir imágenes
**Causa**: Permisos incorrectos en `/uploads/`  
**Solución**: Cambiar permisos a **755**

### Error: "Page not found"
**Causa**: URL incorrecta en configuración  
**Solución**: Verificar `APP_URL` en `config/config.php`

### Las imágenes no se muestran
**Causa**: Ruta incorrecta o permisos  
**Solución**: 
1. Verificar `/uploads/` existe y tiene permisos 755
2. Verificar `APP_URL` es correcto

### Error: "Class 'PDO' not found"
**Causa**: Extensión PDO no habilitada  
**Solución**: Activar en **Select PHP Version** → **Extensions**

### Estilos no cargan correctamente
**Causa**: URL de Bootstrap o CSS personalizados  
**Solución**: Verificar conexión a internet y `APP_URL`

## 🔐 CONFIGURACIONES DE SEGURIDAD

### Para Producción
En `index.php`, cambiar:
```php
// DESARROLLO
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PRODUCCIÓN
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log');
```

### Backup Automático
1. **cPanel** → **Backup Wizard**
2. **Full Backup** → Programar mensual
3. **Partial Backup** → BD semanal

## 📞 SOPORTE TÉCNICO

### Información del Sistema
- **Versión**: 1.0.0
- **PHP mínimo**: 8.0
- **MySQL mínimo**: 8.0
- **Espacio requerido**: ~50MB

### Logs de Errores
- **PHP**: `cpanel/logs/error_log`
- **MySQL**: phpMyAdmin → **Status**
- **Aplicación**: `logs/` (crear si no existe)

### Contacto Desarrollador
Para modificaciones o soporte técnico especializado, contactar al desarrollador con:
- URL del sitio
- Descripción del problema
- Screenshots si es necesario
- Acceso temporal a cPanel (si es crítico)

---

🎉 **¡Felicitaciones!** Tu sistema de inventario KyoShop está listo para usar.

**Próximos pasos recomendados**:
1. Cambiar datos de ejemplo por productos reales
2. Configurar backup automático
3. Agregar más categorías según tu negocio
4. Personalizar colores/logo si deseas

¿Necesitas ayuda? Revisa la sección de **Solución de Problemas** o contacta soporte técnico.