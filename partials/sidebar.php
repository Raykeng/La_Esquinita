<?php
// Asegurar que $vista esté definida
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'dashboard';
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

        <a href="index.php?vista=caja" class="menu-item <?php echo ($vista == 'caja') ? 'active' : ''; ?>">
            <i class="fas fa-wallet"></i> Cierre de Caja
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-info w-100 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">M</div>
                <div>
                    <div class="fw-bold">Marlon</div>
                    <div class="text-xs text-muted">Administrador</div>
                </div>
            </div>
            <a href="logout.php" class="text-white-50 hover-text-white" title="Cerrar Sesión">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>