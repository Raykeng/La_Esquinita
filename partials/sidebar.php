<?php
// Asegurar que $vista esté definida
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'dashboard';

// Obtener usuario actual si está autenticado
$usuarioActual = null;
if (isset($_SESSION['usuario_id'])) {
    $usuarioActual = [
        'nombre_completo' => $_SESSION['nombre_completo'] ?? 'Usuario',
        'rol' => $_SESSION['rol'] ?? 'cajero',
        'username' => $_SESSION['username'] ?? 'user'
    ];
}
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand-title">
            <i class="fas fa-store text-primary"></i>
            <span>La Esquinita</span>
        </div>
    </div>

    <div class="sidebar-menu">
        <a href="index.php?vista=dashboard" class="menu-item <?php echo ($vista == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Inicio
        </a>
        
        <a href="index.php?vista=pos" class="menu-item <?php echo ($vista == 'pos') ? 'active' : ''; ?>">
            <i class="fas fa-cash-register"></i> Punto de Venta
        </a>
        
        <a href="index.php?vista=inventario" class="menu-item <?php echo ($vista == 'inventario') ? 'active' : ''; ?>">
            <i class="fas fa-boxes"></i> Inventario
        </a>

        <a href="index.php?vista=clientes" class="menu-item <?php echo ($vista == 'clientes') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Clientes
        </a>

        <?php if ($usuarioActual && $usuarioActual['rol'] === 'administrador'): ?>
        <a href="index.php?vista=proveedores" class="menu-item <?php echo ($vista == 'proveedores') ? 'active' : ''; ?>">
            <i class="fas fa-truck"></i> Proveedores
        </a>
        
        <a href="index.php?vista=caja" class="menu-item <?php echo ($vista == 'caja') ? 'active' : ''; ?>">
            <i class="fas fa-wallet"></i> Cierre de Caja
        </a>
        
        <a href="index.php?vista=reportes" class="menu-item <?php echo ($vista == 'reportes') ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> Reportes
        </a>
        
        <a href="index.php?vista=usuarios" class="menu-item <?php echo ($vista == 'usuarios') ? 'active' : ''; ?>">
            <i class="fas fa-user-cog"></i> Usuarios
        </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <div class="user-info w-100 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">
                    <?php echo $usuarioActual ? strtoupper(substr($usuarioActual['nombre_completo'], 0, 1)) : 'U'; ?>
                </div>
                <div>
                    <div class="fw-bold">
                        <?php echo $usuarioActual ? htmlspecialchars($usuarioActual['nombre_completo']) : 'Usuario'; ?>
                    </div>
                    <div class="text-xs text-muted">
                        <?php 
                        if ($usuarioActual) {
                            echo $usuarioActual['rol'] === 'administrador' ? '👑 Administrador' : '💰 Cajero';
                        } else {
                            echo 'Sin rol';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-white-50 p-0" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="cambiarPassword()">
                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="cerrarSesion()">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
// Función para cerrar sesión
async function cerrarSesion() {
    const result = await Swal.fire({
        title: '¿Cerrar sesión?',
        text: 'Se cerrará tu sesión actual',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch('api/auth.php?action=logout', {
                method: 'POST'
            });
            
            const data = await response.json();
            
            if (data.success) {
                localStorage.removeItem('usuario');
                window.location.href = 'login.php';
            } else {
                Swal.fire('Error', 'No se pudo cerrar la sesión', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            // Redirigir de todas formas
            window.location.href = 'login.php';
        }
    }
}

// Función para cambiar contraseña
function cambiarPassword() {
    Swal.fire({
        title: 'Cambiar Contraseña',
        html: `
            <div class="mb-3">
                <label class="form-label">Contraseña Actual</label>
                <input type="password" id="password-actual" class="form-control" placeholder="Contraseña actual">
            </div>
            <div class="mb-3">
                <label class="form-label">Nueva Contraseña</label>
                <input type="password" id="password-nuevo" class="form-control" placeholder="Nueva contraseña">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" id="password-confirmar" class="form-control" placeholder="Confirmar contraseña">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Cambiar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const actual = document.getElementById('password-actual').value;
            const nuevo = document.getElementById('password-nuevo').value;
            const confirmar = document.getElementById('password-confirmar').value;
            
            if (!actual || !nuevo || !confirmar) {
                Swal.showValidationMessage('Todos los campos son requeridos');
                return false;
            }
            
            if (nuevo !== confirmar) {
                Swal.showValidationMessage('Las contraseñas no coinciden');
                return false;
            }
            
            if (nuevo.length < 6) {
                Swal.showValidationMessage('La nueva contraseña debe tener al menos 6 caracteres');
                return false;
            }
            
            return { password_actual: actual, password_nuevo: nuevo };
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch('api/auth.php?action=cambiar_password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(result.value)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire('Éxito', 'Contraseña cambiada correctamente', 'success');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo cambiar la contraseña', 'error');
            }
        }
    });
}
</script>