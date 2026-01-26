<?php
?>
<div class="row h-100 g-0 overflow-hidden">
    <!-- PANEL IZQUIERDO: PRODUCTOS -->
    <div class="col-12 col-md-7 col-lg-8 d-flex flex-column h-100 bg-light border-end">

        <!-- BARRA SUPERIOR: Búsqueda y Filtros -->
        <div class="p-3 bg-white border-bottom shadow-sm">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-5 d-flex align-items-center gap-2">
                    <div class="input-group input-group-lg search-bar-premium flex-grow-1">
                        <span class="input-group-text bg-transparent border-end-0 text-primary">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 bg-transparent ps-0" id="pos-search"
                            placeholder="Buscar producto..." aria-label="Buscar producto">
                    </div>

                    <!-- Botón de Alertas de Vencimiento -->
                    <button class="btn btn-warning shadow-sm position-relative rounded-circle p-0 flex-shrink-0"
                        style="width: 48px; height: 48px;" onclick="openExpirationModal()"
                        title="Gestionar Vencimientos">
                        <i class="fas fa-exclamation-triangle text-dark"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            id="expiration-badge" style="display:none">
                            0
                        </span>
                    </button>
                </div>

                <!-- Categorías Pills -->
                <div class="col-12 col-lg-7">
                    <div class="d-flex gap-2 overflow-auto py-1" id="category-pills"
                        style="white-space: nowrap; -ms-overflow-style: none; scrollbar-width: none;">
                        <!-- JS inyectará botones aquí -->
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID DE PRODUCTOS -->
        <div class="flex-grow-1 p-3 overflow-auto" id="products-container" style="background-color: #f0f2f5;">
            <div class="row row-cols-2 row-cols-lg-3 row-cols-xl-4 g-3" id="pos-grid">
                <!-- Se llena dinámicamente con JS -->
            </div>
        </div>
    </div>

    <!-- PANEL DERECHO: CARRITO / TICKET -->
    <div class="col-12 col-md-5 col-lg-4 d-flex flex-column h-100 bg-white shadow">

        <!-- Header del Ticket -->
        <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt text-primary me-2"></i> Orden Current</h5>
                <small class="text-muted" id="ticket-number">#V-NEW</small>
            </div>
            <button class="btn btn-outline-danger btn-sm border-0 rounded-circle p-2" onclick="clearCart()"
                title="Limpiar Carrito">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>

        <!-- Lista de Items (Scrollable) -->
        <div class="flex-grow-1 overflow-auto p-0 scrollbar-premium" id="cart-items-wrapper">
            <div id="cart-items-container" class="p-3">
                <!-- Estado Vacío -->
            </div>
        </div>

        <!-- Footer: Totales y Botones -->
        <div class="p-4 bg-light border-top mt-auto">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold text-dark" id="cart-subtotal-display">Q 0.00</span>
            </div>
            <div class="d-flex justify-content-between mb-1 text-success">
                <span class="small"><i class="fas fa-tag"></i> Descuentos (Vencimiento)</span>
                <span class="fw-bold" id="cart-discount-display">- Q 0.00</span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm border">
                <span class="h5 mb-0 text-muted">Total</span>
                <span class="h2 mb-0 fw-bold text-primary" id="cart-total">Q 0.00</span>
            </div>

            <button
                class="btn btn-primary w-100 btn-lg rounded-3 py-3 shadow fw-bold text-uppercase d-flex justify-content-between align-items-center"
                id="btn-pay" onclick="openPaymentModal()" disabled>
                <span>Cobrar</span>
                <span><i class="fas fa-chevron-right"></i></span>
            </button>
        </div>
    </div>
</div>

<!-- MODAL GESTIÓN DE VENCIMIENTOS -->
<div class="modal fade" id="modalVencimientos" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Productos Por Vencer (<
                        60 días)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th>Vence</th>
                                <th>Días</th>
                                <th>Precio Actual</th>
                                <th style="width: 150px;">Aplicar Descuento</th>
                                <th class="pe-4 text-end">Nuevo Precio</th>
                            </tr>
                        </thead>
                        <tbody id="vencimientos-table-body">
                            <!-- JS Inyectará filas aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarDescuentos()">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE PAGO -->
<div class="modal fade" id="modalCobrar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-cash-register me-2"></i> Procesar Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h2 class="text-primary fw-bold" id="modal-total-pagar">Q 0.00</h2>
                    <small class="text-muted">Total a pagar</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Cliente</label>
                    <select class="form-select" id="select-cliente-venta">
                        <option value="1">Público General</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Método de Pago</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="metodoPago" id="pago-efectivo" value="efectivo"
                                checked onchange="togglePagoInput('efectivo')">
                            <label class="btn btn-outline-success w-100" for="pago-efectivo">
                                <i class="fas fa-money-bill-wave d-block mb-1"></i>
                                <small>Efectivo</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="metodoPago" id="pago-tarjeta" value="tarjeta"
                                onchange="togglePagoInput('tarjeta')">
                            <label class="btn btn-outline-primary w-100" for="pago-tarjeta">
                                <i class="fas fa-credit-card d-block mb-1"></i>
                                <small>Tarjeta</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="seccion-efectivo">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto Recibido</label>
                        <input type="number" class="form-control form-control-lg text-center fw-bold"
                            id="input-pago-recibido" placeholder="0.00" step="0.01" oninput="calcularCambio()"
                            onfocus="this.value=''">
                    </div>
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <span>Cambio:</span>
                        <span class="fw-bold" id="lbl-cambio">Q 0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-lg" onclick="procesarVentaConfirmada()">
                    <i class="fas fa-check me-2"></i> Confirmar Venta
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .search-bar-premium {
        border-radius: 50px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.2s;
    }

    .search-bar-premium:focus-within {
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        border-color: #0d6efd;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #e9ecef;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }

    .product-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.7rem;
        z-index: 1;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: #0d6efd;
    }

    .scrollbar-premium::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-premium::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .cart-item {
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .text-xs {
        font-size: 0.75rem;
    }
</style>