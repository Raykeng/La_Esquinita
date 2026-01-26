<div class="modal fade" id="modalCobrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Finalizar Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h2 class="display-4 fw-bold text-success" id="modal-total-pagar">Q 0.00</h2>
                    <p class="text-muted">Total a Pagar</p>
                </div>
                
                <div class="mb-3">
                    <label>Efectivo Recibido</label>
                    <input type="number" class="form-control input-lg" id="input-pago-recibido" oninput="calcularCambio()">
                </div>
                <div class="alert alert-secondary text-center">
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