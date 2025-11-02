<?php
$esEdicion = isset($usuario);
$datosFormulario = $_SESSION['datos_formulario'] ?? [];
unset($_SESSION['datos_formulario']);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-<?= $esEdicion ? 'pencil-square' : 'person-plus' ?>"></i>
                    <?= $esEdicion ? 'Editar Usuario' : 'Crear Nuevo Usuario' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/usuarios/<?= $esEdicion ? 'actualizar/' . $usuario['id'] : 'guardar' ?>">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="bi bi-person"></i> Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="nombre"
                               name="nombre"
                               value="<?= htmlspecialchars($usuario['nombre'] ?? $datosFormulario['nombre'] ?? '') ?>"
                               required
                               autofocus>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               value="<?= htmlspecialchars($usuario['email'] ?? $datosFormulario['email'] ?? '') ?>"
                               required>
                        <small class="text-muted">El usuario usará este email para iniciar sesión</small>
                    </div>

                    <!-- Rol -->
                    <div class="mb-3">
                        <label for="rol" class="form-label">
                            <i class="bi bi-shield-check"></i> Rol <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="rol" name="rol" required>
                            <?php
                            $rolActual = $usuario['rol'] ?? $datosFormulario['rol'] ?? 'vendedor';
                            $roles = [
                                'admin' => 'Administrador (acceso total)',
                                'supervisor' => 'Supervisor (visualización y reportes)',
                                'vendedor' => 'Vendedor (gestión de productos)'
                            ];
                            ?>
                            <?php foreach ($roles as $valor => $descripcion): ?>
                                <option value="<?= $valor ?>" <?= $rolActual === $valor ? 'selected' : '' ?>>
                                    <?= $descripcion ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Contraseña
                            <?php if (!$esEdicion): ?>
                                <span class="text-danger">*</span>
                            <?php else: ?>
                                <small class="text-muted">(dejar en blanco para mantener la actual)</small>
                            <?php endif; ?>
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               minlength="6"
                               <?= !$esEdicion ? 'required' : '' ?>>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-3">
                        <label for="password_confirmacion" class="form-label">
                            <i class="bi bi-lock-fill"></i> Confirmar Contraseña
                            <?php if (!$esEdicion): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmacion"
                               name="password_confirmacion"
                               minlength="6"
                               <?= !$esEdicion ? 'required' : '' ?>>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-<?= $esEdicion ? 'save' : 'plus-circle' ?>"></i>
                            <?= $esEdicion ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($esEdicion): ?>
            <!-- Card de información adicional -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle"></i> Información del Usuario
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li><strong>ID:</strong> <?= $usuario['id'] ?></li>
                        <li><strong>Registrado:</strong> <?= date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) ?></li>
                        <?php if ($usuario['ultimo_acceso']): ?>
                            <li><strong>Último acceso:</strong> <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?></li>
                        <?php endif; ?>
                        <li>
                            <strong>Estado:</strong>
                            <?php if ($usuario['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Validar que las contraseñas coincidan
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmacion = document.getElementById('password_confirmacion').value;

    // Solo validar si se ingresó un password
    if (password || confirmacion) {
        if (password !== confirmacion) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            document.getElementById('password_confirmacion').focus();
        }
    }
});
</script>
