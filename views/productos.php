<?php
/**
 * modulos/productos.php
 * Vista de Gestión de Productos
 */
?>
<div class="container-fluid p-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Inventario de Productos</h2>
            <p class="text-muted">Administra tu catálogo, precios y existencias.</p>
        </div>
        <button class="btn btn-primary btn-lg shadow-sm" onclick="abrirModalProducto()">
            <i class="fas fa-plus me-2"></i> Nuevo Producto
        </button>
    </div>

    <!-- Tabla de Productos -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabla-productos">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Producto</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3 text-end">Costo</th>
                            <th class="py-3 text-end">Precio Venta</th>
                            <th class="py-3 text-center">Stock</th>
                            <th class="pe-4 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-productos-body">
                        <!-- Se llena dinámicamente con JS -->
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-sm">Cargando inventario...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white px-4 py-3">
                <h5 class="modal-title fw-bold" id="modalProductoLabel">Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formProducto">
                    <input type="hidden" id="prod-id" name="id">

                    <div class="row g-3">
                        <!-- Código y Nombre -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Código de Barras</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fas fa-barcode text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="prod-codigo"
                                    name="codigo_barras" placeholder="Ej. 7501..." required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nombre del
                                Producto</label>
                            <input type="text" class="form-control" id="prod-nombre" name="nombre"
                                placeholder="Ej. Coca Cola 3L" required>
                        </div>

                        <!-- Categoría y Proveedor -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Categoría</label>
                            <div class="input-group">
                                <select class="form-select" id="prod-categoria" name="categoria_id" required>
                                    <option value="">Seleccionar...</option>
                                    <!-- Dinámico -->
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="crearCategoriaRapida()"
                                    title="Nueva Categoría"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Proveedor</label>
                            <div class="input-group">
                                <select class="form-select" id="prod-proveedor" name="proveedor_id">
                                    <option value="">Seleccionar...</option>
                                    <!-- Dinámico -->
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="crearProveedorRapido()"
                                    title="Nuevo Proveedor"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>

                        <!-- Precios -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Precio Compra
                                (Costo)</label>
                            <div class="input-group">
                                <span class="input-group-text">Q</span>
                                <input type="number" step="0.01" class="form-control" id="prod-costo"
                                    name="precio_compra" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Precio Venta</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-success">Q</span>
                                <input type="number" step="0.01" class="form-control fw-bold text-success"
                                    id="prod-precio" name="precio_venta" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Ganancia Estimada</label>
                            <input type="text" class="form-control bg-light text-muted" id="prod-ganancia" readonly
                                tabindex="-1" placeholder="Q 0.00">
                        </div>

                        <div class="col-12">
                            <hr class="my-2 border-light">
                        </div>

                        <!-- Inventario -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Stock Inicial</label>
                            <input type="number" class="form-control" id="prod-stock" name="stock_actual" value="0"
                                required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Stock Mínimo</label>
                            <input type="number" class="form-control" id="prod-minimo" name="stock_minimo" value="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Unidad</label>
                            <select class="form-select" id="prod-unidad" name="unidad_medida">
                                <option value="pieza">Pieza</option>
                                <option value="kg">Kilogramo</option>
                                <option value="litro">Litro</option>
                                <option value="paquete">Paquete</option>
                                <option value="caja">Caja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Vencimiento</label>
                            <input type="date" class="form-control" id="prod-vencimiento" name="fecha_vencimiento">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="guardarProducto()">
                    <i class="fas fa-save me-2"></i> Guardar Producto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lógica del Módulo -->
<script src="js/productos.js?v=<?php echo time(); ?>"></script>