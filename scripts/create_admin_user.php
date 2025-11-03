#!/usr/bin/env php
<?php
/**
 * Script para crear usuario administrador
 * Uso: php scripts/create_admin_user.php
 *
 * Este script crea el usuario admin por defecto con credenciales:
 * Email: admin@kyoshop.co
 * Password: admin123
 *
 * SEGURIDAD: Solo ejecutable desde línea de comandos (CLI)
 */

// PROTECCIÓN: Solo permitir ejecución desde CLI
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Acceso denegado. Este script solo puede ejecutarse desde línea de comandos.');
}

// Cargar configuración
require_once __DIR__ . '/../config/database.php';

echo "========================================\n";
echo "  CREAR USUARIO ADMINISTRADOR\n";
echo "========================================\n\n";

try {
    $db = getDB();

    // Datos del usuario admin
    $nombre = 'Administrador';
    $email = 'admin@kyoshop.co';
    $password = 'admin123';
    $rol = 'admin';

    // Verificar si el usuario ya existe
    $existente = $db->fetch("SELECT id FROM usuarios WHERE email = :email", ['email' => $email]);

    if ($existente) {
        echo "⚠️  El usuario ya existe (ID: {$existente['id']})\n";
        echo "¿Deseas actualizarlo? (s/n): ";
        $respuesta = trim(fgets(STDIN));

        if (strtolower($respuesta) !== 's') {
            echo "❌ Operación cancelada\n";
            exit(0);
        }

        // Actualizar usuario existente
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET
                nombre = :nombre,
                password = :password,
                rol = :rol,
                activo = 1,
                fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE email = :email";

        $result = $db->execute($sql, [
            'nombre' => $nombre,
            'email' => $email,
            'password' => $hash,
            'rol' => $rol
        ]);

        if ($result) {
            echo "✅ Usuario actualizado exitosamente\n\n";
        } else {
            echo "❌ Error al actualizar usuario\n";
            exit(1);
        }
    } else {
        // Crear nuevo usuario
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password, rol)
                VALUES (:nombre, :email, :password, :rol)";

        $userId = $db->insert($sql, [
            'nombre' => $nombre,
            'email' => $email,
            'password' => $hash,
            'rol' => $rol
        ]);

        if ($userId) {
            echo "✅ Usuario creado exitosamente (ID: $userId)\n\n";
        } else {
            echo "❌ Error al crear usuario\n";
            exit(1);
        }
    }

    // Mostrar credenciales
    echo "========================================\n";
    echo "  CREDENCIALES DE ACCESO\n";
    echo "========================================\n";
    echo "Email:    $email\n";
    echo "Password: $password\n";
    echo "Rol:      $rol\n";
    echo "========================================\n\n";

    // Verificar que se guardó correctamente
    $verificar = $db->fetch("SELECT id, nombre, email, rol FROM usuarios WHERE email = :email", ['email' => $email]);

    if ($verificar) {
        echo "✅ Verificación exitosa:\n";
        echo "   ID: {$verificar['id']}\n";
        echo "   Nombre: {$verificar['nombre']}\n";
        echo "   Email: {$verificar['email']}\n";
        echo "   Rol: {$verificar['rol']}\n\n";

        // Probar password
        $usuarioCompleto = $db->fetch("SELECT * FROM usuarios WHERE email = :email", ['email' => $email]);
        if (password_verify($password, $usuarioCompleto['password'])) {
            echo "✅ Password verificado correctamente\n";
        } else {
            echo "❌ Error: Password NO verifica correctamente\n";
            exit(1);
        }
    }

    echo "\n🎉 ¡Listo! Ahora puedes iniciar sesión.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
