<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-person-<?= $accion === 'crear' ? 'plus' : 'pencil' ?>"></i>
                    <?= $accion === 'crear' ? 'Nuevo Cliente' : 'Editar Cliente' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/clientes/<?= $accion === 'crear' ? 'guardar' : 'actualizar/' . $cliente['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre Completo *</label>
                            <input type="text"
                                   class="form-control"
                                   id="nombre"
                                   name="nombre"
                                   value="<?= htmlspecialchars($datos_antiguos['nombre'] ?? $cliente['nombre'] ?? '') ?>"
                                   required
                                   maxlength="255">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-bold">Teléfono *</label>
                            <input type="tel"
                                   class="form-control"
                                   id="telefono"
                                   name="telefono"
                                   value="<?= htmlspecialchars($datos_antiguos['telefono'] ?? $cliente['telefono'] ?? '') ?>"
                                   required
                                   maxlength="20"
                                   pattern="[0-9]{7,20}"
                                   title="Debe contener entre 7 y 20 dígitos">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="<?= htmlspecialchars($datos_antiguos['email'] ?? $cliente['email'] ?? '') ?>"
                                   maxlength="255">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ciudad" class="form-label fw-bold">Ciudad *</label>
                            <select class="form-select" id="ciudad" name="ciudad" required>
                                <?php
                                $ciudadSeleccionada = $datos_antiguos['ciudad'] ?? $cliente['ciudad'] ?? 'Medellín';
                                $ciudadesComunes = ['Medellín', 'Bogotá', 'Cali', 'Barranquilla', 'Cartagena', 'Bucaramanga', 'Pereira', 'Manizales'];
                                ?>
                                <?php foreach ($ciudadesComunes as $ciudad): ?>
                                    <option value="<?= $ciudad ?>" <?= $ciudadSeleccionada === $ciudad ? 'selected' : '' ?>>
                                        <?= $ciudad ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control"
                                      id="direccion"
                                      name="direccion"
                                      rows="3"
                                      placeholder="Dirección completa del cliente..."><?= htmlspecialchars($datos_antiguos['direccion'] ?? $cliente['direccion'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= APP_URL ?>/clientes" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
