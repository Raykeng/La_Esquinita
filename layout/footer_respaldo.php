<div class="modal fade" id="modalCobrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Finalizar Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <select class="form-select" id="select-cliente-venta">
                        <!-- Llenado dinámicamente -->
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="metodoPago" id="pago-efectivo" value="efectivo"
                            checked onchange="togglePagoInput('efectivo')">
                        <label class="btn btn-outline-success" for="pago-efectivo"><i class="fas fa-money-bill"></i>
                            Efectivo</label>

                        <input type="radio" class="btn-check" name="metodoPago" id="pago-tarjeta" value="tarjeta"
                            onchange="togglePagoInput('tarjeta')">
                        <label class="btn btn-outline-primary" for="pago-tarjeta"><i class="fas fa-credit-card"></i>
                            Tarjeta</label>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <h2 class="display-4 fw-bold text-success" id="modal-total-pagar">Q 0.00</h2>
                    <p class="text-muted">Total a Pagar</p>
                </div>

                <div class="mb-3" id="seccion-efectivo">
                    <label>Efectivo Recibido</label>
                    <div class="input-group">
                        <span class="input-group-text">Q</span>
                        <input type="number" class="form-control input-lg fs-4" id="input-pago-recibido"
                            placeholder="0.00" oninput="calcularCambio()">
                    </div>
                </div>

                <div class="alert alert-secondary text-center fs-5">
                    Cambio: <strong id="lbl-cambio">Q 0.00</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="procesarVentaConfirmada()">COBRAR</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>