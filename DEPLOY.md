# 🚀 GUÍA DE DESPLIEGUE - KYOSHOP INVENTORY SYSTEM

Este documento describe el proceso de despliegue automatizado para el sistema de inventario. El proyecto utiliza un flujo de **CI/CD (Integración y Despliegue Continuo)** con GitHub Actions para automatizar las actualizaciones en los ambientes de desarrollo y producción.

**El antiguo proceso de despliegue manual a través de cPanel ha quedado obsoleto.**

---

## 🌐 Ambientes

Existen dos ambientes configurados en el mismo servidor:

1.  **Desarrollo (Development)**:
    *   **URL**: `https://dev.inventory.kyoshop.co`
    *   **Branch**: `develop`
    *   **Propósito**: Es el ambiente para probar nuevas funcionalidades, corregir errores y realizar validaciones antes de pasar a producción.

2.  **Producción (Production)**:
    *   **URL**: `https://inventory.kyoshop.co`
    *   **Branch**: `main`
    *   **Propósito**: Es el ambiente en vivo que utilizan los usuarios finales.

---

## ⚙️ Proceso de Despliegue Automatizado

El despliegue es gestionado por **GitHub Actions**. Los pipelines se encuentran en el directorio `.github/workflows/`.

### Despliegue a Desarrollo

1.  **Disparador (Trigger)**: El pipeline de despliegue a desarrollo se ejecuta automáticamente cada vez que se hace un `push` a la rama `develop`.
2.  **Proceso**:
    *   El workflow `deploy-dev.yml` se activa.
    *   Se conecta al servidor vía SSH.
    *   Navega al directorio de despliegue de desarrollo (`secrets.DEPLOY_PATH_DEV`).
    *   Hace un `git pull` de la rama `develop` para obtener los últimos cambios.
    *   **Configura el archivo `.htaccess`** utilizando los secretos de GitHub (`secrets.DB_NAME_DEV`, `secrets.DB_USER_DEV`, etc.) para apuntar a la base de datos de desarrollo y establecer la `APP_URL` correcta.
    *   Ajusta los permisos de los archivos y directorios necesarios (ej. `uploads/`).

**Para probar una nueva funcionalidad, simplemente haz `push` de tus cambios a la rama `develop`.**

### Despliegue a Producción

El paso a producción es más controlado para garantizar la estabilidad.

1.  **Disparador (Trigger)**: El pipeline de despliegue a producción se ejecuta automáticamente solo cuando se hace un `push` a la rama `main`.
2.  **Flujo de Trabajo (Workflow)**:
    *   Para llevar cambios a `main`, se debe crear un **Pull Request (PR)** desde la rama `develop` (o una rama de feature) hacia `main`.
    *   Este PR debe ser revisado y aprobado por otro miembro del equipo.
    *   Una vez que el PR es aprobado y **fusionado (merged)** a `main`, el `push` resultante dispara el pipeline.
3.  **Proceso**:
    *   El workflow `deploy-prd.yml` se activa.
    *   Realiza un proceso similar al de desarrollo: se conecta al servidor, navega al directorio de producción (`secrets.DEPLOY_PATH_PRD`), y hace `git pull` de la rama `main`.
    *   **Configura el `.htaccess`** con los secretos de producción (`secrets.DB_NAME_PRD`, `secrets.DB_USER_PRD`, etc.).
    *   Ajusta los permisos de los archivos.

---

## 🛠️ Variables de Entorno y Configuración

-   **Toda la configuración sensible** (credenciales de base de datos, rutas, etc.) se gestiona a través de **GitHub Secrets**.
-   El pipeline de GitHub Actions es el encargado de leer estos secretos y construir el archivo `.htaccess` en el servidor con las variables de entorno (`SetEnv`) adecuadas para cada ambiente.
-   **No es necesario modificar manualmente el archivo `.htaccess` en el servidor.**

---

## 🐛 Solución de Problemas (Troubleshooting)

-   **Si un despliegue falla**: Revisa los logs de la ejecución de GitHub Actions en la pestaña "Actions" del repositorio de GitHub. Allí encontrarás los detalles del error.
-   **Si el sitio se cae después de un despliegue**:
    *   Verifica que los secretos de GitHub estén configurados correctamente para el ambiente correspondiente.
    *   Revisa los logs de error del servidor Apache.
    *   Considera revertir el commit o PR que causó el problema.
