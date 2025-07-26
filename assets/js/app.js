/**
 * KYOSHOP INVENTORY SYSTEM
 * JavaScript principal de la aplicación
 */

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Inicializar funciones principales
 */
function initializeApp() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar popovers de Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-hide alerts después de 5 segundos
    autoHideAlerts();
    
    // Inicializar búsqueda en tiempo real
    initLiveSearch();
    
    // Inicializar validaciones de formulario
    initFormValidation();
    
    // Agregar animaciones de entrada
    addEntryAnimations();
}

/**
 * Auto-ocultar alertas después de un tiempo
 */
function autoHideAlerts() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
}

/**
 * Búsqueda en tiempo real (opcional)
 */
function initLiveSearch() {
    const searchInput = document.getElementById('busqueda');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => performLiveSearch(query), 500);
            }
        });
    }
}

/**
 * Realizar búsqueda en tiempo real
 */
function performLiveSearch(query) {
    const searchResults = document.getElementById('search-results');
    if (!searchResults) return;
    
    // Mostrar loading
    searchResults.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Buscando...</div>';
    searchResults.style.display = 'block';
    
    fetch('/productos/buscar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'termino=' + encodeURIComponent(query)
    })
    .then(response => response.json())
    .then(data => {
        displaySearchResults(data.productos);
    })
    .catch(error => {
        console.error('Error en búsqueda:', error);
        searchResults.innerHTML = '<div class="text-danger p-3">Error al buscar productos</div>';
    });
}

/**
 * Mostrar resultados de búsqueda
 */
function displaySearchResults(productos) {
    const searchResults = document.getElementById('search-results');
    
    if (productos.length === 0) {
        searchResults.innerHTML = '<div class="text-muted p-3">No se encontraron productos</div>';
        return;
    }
    
    let html = '<div class="list-group">';
    productos.forEach(producto => {
        html += `
            <a href="/productos/editar/${producto.id}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    ${producto.imagen ? 
                        `<img src="/uploads/${producto.imagen}" class="rounded me-2" width="40" height="40" style="object-fit: cover;">` :
                        '<div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-image text-white"></i></div>'
                    }
                    <div>
                        <div class="fw-bold">${escapeHtml(producto.nombre)}</div>
                        <small class="text-muted">${escapeHtml(producto.categoria)} | Stock: ${producto.stock}</small>
                    </div>
                </div>
                <span class="badge bg-primary rounded-pill">${formatPrice(producto.precio)}</span>
            </a>
        `;
    });
    html += '</div>';
    
    searchResults.innerHTML = html;
}

/**
 * Validaciones de formulario
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
}

/**
 * Confirmar eliminación de producto
 */
function confirmarEliminacion(id, nombre) {
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal') || createConfirmModal());
    
    document.getElementById('confirmMessage').textContent = 
        `¿Estás seguro de que deseas eliminar el producto "${nombre}"? Esta acción no se puede deshacer.`;
    
    document.getElementById('confirmAction').onclick = function() {
        eliminarProducto(id);
        confirmModal.hide();
    };
    
    confirmModal.show();
}

/**
 * Crear modal de confirmación dinámicamente
 */
function createConfirmModal() {
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmAction">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    return document.getElementById('confirmModal');
}

/**
 * Eliminar producto
 */
function eliminarProducto(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/productos/eliminar/${id}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = 'csrf_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
}

/**
 * Preview de imagen antes de subir
 */
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const file = input.files[0];
    
    if (file) {
        // Validar tamaño
        if (file.size > 5 * 1024 * 1024) {
            alert('La imagen es demasiado grande. Máximo 5MB.');
            input.value = '';
            return;
        }
        
        // Validar tipo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de archivo no permitido. Solo JPG, PNG, GIF.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">`;
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Generar código de producto automáticamente
 */
function generarCodigoProducto() {
    const codigoInput = document.getElementById('codigo_producto');
    
    // Solo generar si está vacío
    if (codigoInput && codigoInput.value.trim() === '') {
        const categoria = document.getElementById('categoria')?.value.trim() || '';
        const color = document.getElementById('color')?.value.trim() || '';
        
        if (categoria && color) {
            const categoriaCod = categoria.substring(0, 3).toUpperCase();
            const colorCod = color.substring(0, 3).toUpperCase();
            const numero = Math.floor(Math.random() * 900) + 100;
            
            codigoInput.value = `${categoriaCod}-${colorCod}-${numero}`;
        }
    }
}

/**
 * Animaciones de entrada
 */
function addEntryAnimations() {
    const cards = document.querySelectorAll('.card');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Funciones de utilidad
 */

// Escapar HTML para prevenir XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Formatear precio
function formatPrice(price) {
    return '$' + new Intl.NumberFormat('es-CO').format(price);
}

// Mostrar loading en botón
function showButtonLoading(button, text = 'Cargando...') {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span>${text}`;
    
    return function() {
        button.disabled = false;
        button.innerHTML = originalText;
    };
}

// Copiar al portapapeles
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copiado al portapapeles', 'success');
        });
    } else {
        // Fallback para navegadores más antiguos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('Copiado al portapapeles', 'success');
        } catch (err) {
            console.error('Error al copiar:', err);
        }
        
        document.body.removeChild(textArea);
    }
}

// Mostrar toast notification
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'primary')} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-triangle' : 'info-circle')} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Limpiar después de que se cierre
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Debounce function para optimizar búsquedas
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}