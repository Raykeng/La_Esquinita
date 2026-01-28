<?php
// Verificar que solo administradores puedan acceder
if (!puedeGestionarUsuarios()) {
    header('Location: index.php?error=acceso_denegado');
    exit;
}
?>

<div class="dashboard-main-body">
    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">👥 Gestión de Usuarios</h4>
        <button class="btn btn-primary" onclick="abrirModalUsuario()">
            <i class="fas fa-plus me-2"></i>Nuevo Usuario
        </button>
    </div>

    <!-- Estadísticas de usuarios -->
    <div class="row gy-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h4 id="total-usuarios">0</h4>
                    <small>Total Usuarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-crown fa-2x mb-2"></i>
                    <h4 id="total-admins">0</h4>
                    <small>Administradores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-cash-register fa-2x mb-2"></i>
                    <h4 id="total-cajeros">0</h4>
                    <small>Cajeros</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-user-check fa-2x mb-2"></i>
                    <h4 id="usuarios-activos">0</h4>
                    <small>Activos</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="buscar-usuario" placeholder="Buscar usuario...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="filtro-rol" id="todos" value="" checked>
                        <label class="btn btn-outline-primary" for="todos">Todos</label>
                        
                        <input type="radio" class="btn-check" name="filtro-rol" id="admins" value="administrador">
                        <label class="btn btn-outline-success" for="admins">Admins</label>
                        
                        <input type="radio" class="btn-check" name="filtro-rol" id="cajeros" value="cajero">
                        <label class="btn btn-outline-info" for="cajeros">Cajeros</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando usuarios...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    <span id="modal-titulo">Nuevo Usuario</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-usuario">
                    <input type="hidden" id="usuario-id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Usuario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usuario-username" required>
                                <div class="form-text">Solo letras, números y guiones bajos</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-select" id="usuario-rol" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="administrador">👑 Administrador</option>
                                    <option value="cajero">💰 Cajero</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="usuario-nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="usuario-email">
                    </div>
                    
                    <div class="row" id="password-section">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="usuario-password">
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="usuario-password-confirm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="usuario-activo" checked>
                            <label class="form-check-label" for="usuario-activo">
                                Usuario activo
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let usuarios = [];

// Inicializar módulo
document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
    configurarFiltros();
});

// Cargar lista de usuarios
async function cargarUsuarios() {
    try {
        const response = await fetch('api/usuarios.php?action=listar');
        const data = await response.json();
        
        if (data.success) {
            usuarios = data.usuarios;
            mostrarUsuarios(usuarios);
            actualizarEstadisticas();
        } else {
            console.error('Error al cargar usuarios:', data.message);
            mostrarUsuariosEjemplo();
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarUsuariosEjemplo();
    }
}

// Mostrar usuarios en la tabla
function mostrarUsuarios(usuariosArray) {
    const tbody = document.getElementById('tabla-usuarios');
    
    if (usuariosArray.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                    <p>No hay usuarios registrados</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    usuariosArray.forEach(usuario => {
        const rolBadge = usuario.rol === 'administrador' ? 
            '<span class="badge bg-success">👑 Administrador</span>' : 
            '<span class="badge bg-info">💰 Cajero</span>';
        
        const estadoBadge = usuario.estado === 'activo' ? 
            '<span class="badge bg-success">Activo</span>' : 
            '<span class="badge bg-danger">Inactivo</span>';
        
        const ultimoAcceso = usuario.ultimo_acceso ? 
            new Date(usuario.ultimo_acceso).toLocaleString('es-GT') : 'Nunca';
        
        html += `
            <tr>
                <td>${usuario.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            ${usuario.nombre_completo.charAt(0).toUpperCase()}
                        </div>
                        <strong>${usuario.username}</strong>
                    </div>
                </td>
                <td>${usuario.nombre_completo}</td>
                <td>${rolBadge}</td>
                <td>${estadoBadge}</td>
                <td><small>${ultimoAcceso}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editarUsuario(${usuario.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="resetearPassword(${usuario.id})" title="Resetear Contraseña">
                            <i class="fas fa-key"></i>
                        </button>
                        ${usuario.id !== 1 ? `
                        <button class="btn btn-outline-${usuario.estado === 'activo' ? 'danger' : 'success'}" 
                                onclick="toggleEstadoUsuario(${usuario.id})" 
                                title="${usuario.estado === 'activo' ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${usuario.estado === 'activo' ? 'ban' : 'check'}"></i>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Actualizar estadísticas
function actualizarEstadisticas() {
    const total = usuarios.length;
    const admins = usuarios.filter(u => u.rol === 'administrador').length;
    const cajeros = usuarios.filter(u => u.rol === 'cajero').length;
    const activos = usuarios.filter(u => u.estado === 'activo').length;
    
    document.getElementById('total-usuarios').textContent = total;
    document.getElementById('total-admins').textContent = admins;
    document.getElementById('total-cajeros').textContent = cajeros;
    document.getElementById('usuarios-activos').textContent = activos;
}

// Configurar filtros
function configurarFiltros() {
    // Búsqueda
    document.getElementById('buscar-usuario').addEventListener('input', filtrarUsuarios);
    
    // Filtro por rol
    document.querySelectorAll('input[name="filtro-rol"]').forEach(radio => {
        radio.addEventListener('change', filtrarUsuarios);
    });
}

// Filtrar usuarios
function filtrarUsuarios() {
    const busqueda = document.getElementById('buscar-usuario').value.toLowerCase();
    const rolFiltro = document.querySelector('input[name="filtro-rol"]:checked').value;
    
    let usuariosFiltrados = usuarios.filter(usuario => {
        const coincideBusqueda = !busqueda || 
            usuario.username.toLowerCase().includes(busqueda) ||
            usuario.nombre_completo.toLowerCase().includes(busqueda);
        
        const coincideRol = !rolFiltro || usuario.rol === rolFiltro;
        
        return coincideBusqueda && coincideRol;
    });
    
    mostrarUsuarios(usuariosFiltrados);
}

// Abrir modal para nuevo usuario
function abrirModalUsuario(usuarioId = null) {
    const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    const form = document.getElementById('form-usuario');
    const titulo = document.getElementById('modal-titulo');
    const passwordSection = document.getElementById('password-section');
    
    // Limpiar formulario
    form.reset();
    document.getElementById('usuario-id').value = '';
    document.getElementById('usuario-activo').checked = true;
    
    if (usuarioId) {
        // Editar usuario existente
        const usuario = usuarios.find(u => u.id === usuarioId);
        if (usuario) {
            titulo.textContent = 'Editar Usuario';
            document.getElementById('usuario-id').value = usuario.id;
            document.getElementById('usuario-username').value = usuario.username;
            document.getElementById('usuario-nombre').value = usuario.nombre_completo;
            document.getElementById('usuario-email').value = usuario.email || '';
            document.getElementById('usuario-rol').value = usuario.rol;
            document.getElementById('usuario-activo').checked = usuario.estado === 'activo';
            
            // Ocultar campos de contraseña en edición
            passwordSection.style.display = 'none';
        }
    } else {
        // Nuevo usuario
        titulo.textContent = 'Nuevo Usuario';
        passwordSection.style.display = 'block';
    }
    
    modal.show();
}

// Guardar usuario
async function guardarUsuario() {
    const form = document.getElementById('form-usuario');
    const usuarioId = document.getElementById('usuario-id').value;
    const isEdit = !!usuarioId;
    
    const usuarioData = {
        id: usuarioId,
        username: document.getElementById('usuario-username').value.trim(),
        nombre_completo: document.getElementById('usuario-nombre').value.trim(),
        email: document.getElementById('usuario-email').value.trim(),
        rol: document.getElementById('usuario-rol').value,
        estado: document.getElementById('usuario-activo').checked ? 'activo' : 'inactivo'
    };
    
    // Validaciones
    if (!usuarioData.username || !usuarioData.nombre_completo || !usuarioData.rol) {
        Swal.fire('Error', 'Complete todos los campos requeridos', 'error');
        return;
    }
    
    if (!isEdit) {
        const password = document.getElementById('usuario-password').value;
        const passwordConfirm = document.getElementById('usuario-password-confirm').value;
        
        if (!password || !passwordConfirm) {
            Swal.fire('Error', 'La contraseña es requerida', 'error');
            return;
        }
        
        if (password !== passwordConfirm) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }
        
        if (password.length < 6) {
            Swal.fire('Error', 'La contraseña debe tener al menos 6 caracteres', 'error');
            return;
        }
        
        usuarioData.password = password;
    }
    
    try {
        const url = isEdit ? 'api/usuarios.php?action=actualizar' : 'api/usuarios.php?action=crear';
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(usuarioData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', result.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
            cargarUsuarios();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo guardar el usuario', 'error');
    }
}

// Editar usuario
function editarUsuario(usuarioId) {
    abrirModalUsuario(usuarioId);
}

// Resetear contraseña
function resetearPassword(usuarioId) {
    const usuario = usuarios.find(u => u.id === usuarioId);
    if (!usuario) return;
    
    Swal.fire({
        title: 'Resetear Contraseña',
        text: `¿Resetear la contraseña de ${usuario.username}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, resetear',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            // Implementar reseteo de contraseña
            Swal.fire('Info', 'Función de reseteo en desarrollo', 'info');
        }
    });
}

// Toggle estado usuario
function toggleEstadoUsuario(usuarioId) {
    const usuario = usuarios.find(u => u.id === usuarioId);
    if (!usuario) return;
    
    const nuevoEstado = usuario.estado === 'activo' ? 'inactivo' : 'activo';
    const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';
    
    Swal.fire({
        title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
        text: `Se ${accion}á a ${usuario.username}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            // Implementar cambio de estado
            Swal.fire('Info', 'Función de cambio de estado en desarrollo', 'info');
        }
    });
}

// Datos de ejemplo
function mostrarUsuariosEjemplo() {
    usuarios = [
        {
            id: 1,
            username: 'admin',
            nombre_completo: 'Administrador',
            email: 'admin@laesquinita.com',
            rol: 'administrador',
            estado: 'activo',
            ultimo_acceso: '2026-01-26 10:30:00'
        },
        {
            id: 2,
            username: 'marlon',
            nombre_completo: 'Marlon Cajero',
            email: 'marlon@laesquinita.com',
            rol: 'cajero',
            estado: 'activo',
            ultimo_acceso: '2026-01-26 09:15:00'
        }
    ];
    mostrarUsuarios(usuarios);
    actualizarEstadisticas();
}
</script>