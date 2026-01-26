<div class="row h-100">
    <div class="col-md-8">
        <div class="d-flex gap-3 mb-3">
            <input type="text" class="form-control" id="pos-search" placeholder="Buscar producto (Escanea código)...">
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-3" id="pos-grid" style="max-height: 80vh; overflow-y: auto;">
            </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 d-flex flex-column shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="m-0"><i class="fas fa-shopping-cart"></i> Carrito</h5>
            </div>
            <div class="card-body p-0 flex-grow-1" style="overflow-y: auto; background: #f8f9fa;">
                <div id="cart-items-container"></div>
            </div>
            <div class="card-footer bg-white p-3 border-top">
                <div class="d-flex justify-content-between mb-3">
                    <span class="fs-4 fw-bold">Total</span>
                    <span class="fs-4 fw-bold text-success" id="cart-total">Q 0.00</span>
                </div>
                <button class="btn btn-success w-100 btn-lg" onclick="openPaymentModal()" id="btn-pay">PAGAR</button>
            </div>
        </div>
    </div>
</div>