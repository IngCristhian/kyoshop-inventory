<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people"></i> Lista de Usuarios
                    </h5>
                    <a href="<?= APP_URL ?>/usuarios/crear" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Nuevo Usuario
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-2">No hay usuarios registrados</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['id'] ?></td>
                                        <td>
                                            <i class="bi bi-person-circle"></i>
                                            <?= htmlspecialchars($usuario['nombre']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td>
                                            <?php
                                            $badgeColor = [
                                                'admin' => 'danger',
                                                'supervisor' => 'warning',
                                                'vendedor' => 'info'
                                            ];
                                            $color = $badgeColor[$usuario['rol']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>">
                                                <i class="bi bi-shield-check"></i>
                                                <?= ucfirst($usuario['rol']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($usuario['activo']): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Activo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle"></i> Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario['ultimo_acceso']): ?>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">Nunca</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= APP_URL ?>/usuarios/editar/<?= $usuario['id'] ?>"
                                                   class="btn btn-outline-primary"
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <?php if ($usuario['activo']): ?>
                                                    <button type="button"
                                                            class="btn btn-outline-warning"
                                                            onclick="desactivarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')"
                                                            title="Desactivar">
                                                        <i class="bi bi-ban"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button"
                                                            class="btn btn-outline-success"
                                                            onclick="activarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')"
                                                            title="Activar">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function desactivarUsuario(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas desactivar al usuario "${nombre}"?\n\nEl usuario no podrá iniciar sesión hasta que lo actives nuevamente.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= APP_URL ?>/usuarios/desactivar/${id}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= generateCSRFToken() ?>';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}

function activarUsuario(id, nombre) {
    if (confirm(`¿Deseas activar al usuario "${nombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= APP_URL ?>/usuarios/activar/${id}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= generateCSRFToken() ?>';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
