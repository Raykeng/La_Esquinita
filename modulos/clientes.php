<?php
// modulos/clientes.php
// Módulo de gestión de clientes
?>

<div class="dashboard-main-body">
    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Gestión de Clientes</h4>
        <button class="btn btn-primary" onclick="abrirModalCliente()">
            <i class="fas fa-plus me-2"></i>Nuevo Cliente
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="buscar-cliente" placeholder="Buscar cliente...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total clientes: <strong id="total-clientes">0</strong></span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Puntos</th>
                            <th>Última Compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-clientes">
                        <!-- Se llena dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-cliente">
                    <input type="hidden" id="cliente-id">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="cliente-nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="cliente-telefono">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Puntos Acumulados</label>
                        <input type="number" class="form-control" id="cliente-puntos" value="0" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para gestión de clientes - se implementarán después
function abrirModalCliente() {
    console.log('Abrir modal cliente');
}

function guardarCliente() {
    console.log('Guardar cliente');
}
</script>