<?php
// modulos/caja.php
// Módulo de cierre de caja
?>

<div class="dashboard-main-body">
    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Cierre de Caja</h4>
        <button class="btn btn-success" onclick="procesarCierreCaja()">
            <i class="fas fa-calculator me-2"></i>Procesar Cierre
        </button>
    </div>

    <div class="row gy-4">
        <!-- Resumen del Turno -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Resumen del Turno Actual</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 bg-primary-subtle rounded">
                                <h4 class="text-primary mb-1" id="total-ventas">Q 0.00</h4>
                                <small class="text-muted">Total Ventas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-success-subtle rounded">
                                <h4 class="text-success mb-1" id="total-efectivo">Q 0.00</h4>
                                <small class="text-muted">Efectivo</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-info-subtle rounded">
                                <h4 class="text-info mb-1" id="total-tarjeta">Q 0.00</h4>
                                <small class="text-muted">Tarjeta</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-warning-subtle rounded">
                                <h4 class="text-warning mb-1" id="total-vales">Q 0.00</h4>
                                <small class="text-muted">Vales</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos y Egresos -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Ingresos Adicionales</h6>
                    <button class="btn btn-sm btn-success" onclick="agregarIngreso()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="lista-ingresos">
                        <div class="text-center text-muted py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 mb-0">Cargando ingresos...</p>
                        </div>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <strong>Total Ingresos: <span id="total-ingresos">Q 0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Egresos</h6>
                    <button class="btn btn-sm btn-danger" onclick="agregarEgreso()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="lista-egresos">
                        <div class="text-center text-muted py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 mb-0">Cargando egresos...</p>
                        </div>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <strong>Total Egresos: <span id="total-egresos">Q 0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Cierres -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Historial de Cierres</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Turno</th>
                                    <th>Ventas</th>
                                    <th>Efectivo</th>
                                    <th>Tarjeta</th>
                                    <th>Vales</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-cierres">
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2 mb-0">Cargando historial...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para cierre de caja - se implementarán después
function procesarCierreCaja() {
    console.log('Procesar cierre de caja');
}

function agregarIngreso() {
    console.log('Agregar ingreso');
}

function agregarEgreso() {
    console.log('Agregar egreso');
}
</script>