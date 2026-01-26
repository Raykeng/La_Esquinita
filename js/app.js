/**
 * js/app.js

 */

// === MOCK DATA INITIALIZATION ===
const MOCK_DB = {
    productos: [
        { id: 1, codigo: '7501', nombre: 'Coca Cola 2.5L', precio: 15.00, costo: 12.00, stock: 45, unidad: 'Botella', categoria: 'Bebidas', proveedor: 'Coca Cola', vencimiento: '2026-05-20', promocion: true },
        { id: 2, codigo: '7502', nombre: 'Pepsi 2L', precio: 12.00, costo: 9.50, stock: 32, unidad: 'Botella', categoria: 'Bebidas', proveedor: 'PepsiCo', vencimiento: '2026-06-15', promocion: false },
        { id: 3, codigo: '1001', nombre: 'Arroz Blanco 1lb', precio: 4.50, costo: 3.25, stock: 150, unidad: 'Libra', categoria: 'Granos', proveedor: 'Arrocera San Francisco', vencimiento: '2026-12-01', promocion: false },
        { id: 4, codigo: '1002', nombre: 'Frijol Negro 1lb', precio: 6.00, costo: 4.50, stock: 80, unidad: 'Libra', categoria: 'Granos', proveedor: 'Granos del Campo', vencimiento: '2027-01-30', promocion: false },
        { id: 5, codigo: '2001', nombre: 'Aceite Ideal 1L', precio: 18.00, costo: 15.00, stock: 24, unidad: 'Botella', categoria: 'Abarrotes', proveedor: 'Aceites S.A.', vencimiento: '2025-11-10', promocion: true },
        { id: 6, codigo: '3001', nombre: 'Leche Klim Entera 360g', precio: 35.00, costo: 28.00, stock: 3, unidad: 'Lata', categoria: 'Lácteos', proveedor: 'Nestlé', vencimiento: '2025-10-05', promocion: false },
        { id: 7, codigo: '4001', nombre: 'Papel Higiénico Rosal 4u', precio: 12.00, costo: 9.00, stock: 60, unidad: 'Paquete', categoria: 'Limpieza', proveedor: 'Papelera', vencimiento: null, promocion: true },
        { id: 8, codigo: '5001', nombre: 'Jabón Protex Avena', precio: 8.50, costo: 6.00, stock: 12, unidad: 'Barra', categoria: 'Cuidado Personal', proveedor: 'Colgate', vencimiento: '2026-08-20', promocion: false },
        { id: 9, codigo: '6001', nombre: 'Galletas Chiky', precio: 1.50, costo: 1.00, stock: 100, unidad: 'Paquete', categoria: 'Snacks', proveedor: 'Pozuelo', vencimiento: '2025-02-15', promocion: false },
        { id: 10, codigo: '7001', nombre: 'Agua Pura Salvavidas', precio: 5.00, costo: 3.50, stock: 200, unidad: 'Botella', categoria: 'Bebidas', proveedor: 'Salvavidas', vencimiento: '2025-12-30', promocion: false }
    ],
    clientes: [
        { id: 1, nombre: 'Cliente Casual', telefono: '', puntos: 0, tipo: 'Casual' },
        { id: 2, nombre: 'Juan Pérez', telefono: '5555-1234', puntos: 150, tipo: 'Frecuente' },
        { id: 3, nombre: 'María López', telefono: '4444-5678', puntos: 320, tipo: 'VIP' },
        { id: 4, nombre: 'Tienda Vecina', telefono: '3333-9090', puntos: 850, tipo: 'Mayorista' }
    ],
    ventas: [
        // Ventas precargadas del día para simular "Cierre de Caja"
        { id: 1001, fecha: new Date().setHours(8, 30, 0), total: 45.00, metodo: 'efectivo', items: 3 },
        { id: 1002, fecha: new Date().setHours(9, 15, 0), total: 112.50, metodo: 'tarjeta', items: 8 },
        { id: 1003, fecha: new Date().setHours(10, 45, 0), total: 22.00, metodo: 'efectivo', items: 2 },
        { id: 1004, fecha: new Date().setHours(11, 20, 0), total: 350.00, metodo: 'efectivo', items: 15 },
        { id: 1005, fecha: new Date().setHours(12, 10, 0), total: 18.00, metodo: 'efectivo', items: 1 }
    ],
    config: {
        iva: 0.12,
        moneda: 'Q'
    }
};

let carrito = [];

// === APP INITIALIZATION ===
document.addEventListener('DOMContentLoaded', () => {
    loadView('dashboard'); // Vista por defecto
});


// === VIEW RENDERERS ===

function renderDashboard() {
    const totalVentas = MOCK_DB.ventas.reduce((acc, v) => acc + v.total, 0);
    const totalTx = MOCK_DB.ventas.length;
    const bajosStock = MOCK_DB.productos.filter(p => p.stock < 10).length;

    return `
        <div class="page-header">
            <h2 class="page-title">Panel de Control</h2>
            <button class="btn btn-primary"><i class="fas fa-sync"></i> Actualizar</button>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-content">
                        <h6>Ventas Hoy</h6>
                        <h3>Q ${totalVentas.toFixed(2)}</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="stat-content">
                        <h6>Transacciones</h6>
                        <h3>${totalTx}</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-content">
                        <h6>Bajo Stock</h6>
                        <h3>${bajosStock}</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="stat-content">
                        <h6>Clientes Nuevos</h6>
                        <h3>2</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card p-3">
                    <h5 class="card-title mb-3">Ventas Recientes</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hora</th>
                                <th>Método</th>
                                <th>Items</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${MOCK_DB.ventas.slice(-5).reverse().map(v => `
                                <tr>
                                    <td>#${v.id}</td>
                                    <td>${new Date(v.fecha).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td>
                                    <td><span class="badge bg-${v.metodo === 'efectivo' ? 'success' : 'primary'}">${v.metodo}</span></td>
                                    <td>${v.items}</td>
                                    <td class="text-end fw-bold">Q ${v.total.toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5 class="card-title mb-3">Productos Top</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Coca Cola 2.5L
                            <span class="badge bg-primary rounded-pill">12</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Arroz Blanco
                            <span class="badge bg-primary rounded-pill">8</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Papel Higiénico
                            <span class="badge bg-primary rounded-pill">5</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `;
}

function renderPOS() {
    return `
        <div class="row h-100">
            <!-- PRODUCTOS GRID -->
            <div class="col-md-8">
                <div class="d-flex gap-3 mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="pos-search" placeholder="Buscar por nombre o código (F2)">
                    </div>
                    <select class="form-select w-25" id="pos-category-filter">
                        <option value="">Todas las categorías</option>
                        ${[...new Set(MOCK_DB.productos.map(p => p.categoria))].map(c => `<option value="${c}">${c}</option>`).join('')}
                    </select>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-3" id="pos-grid" style="max-height: 80vh; overflow-y: auto;">
                    <!-- Productos inyectados por JS -->
                </div>
            </div>

            <!-- CARRITO LATERAL -->
            <div class="col-md-4">
                <div class="card h-100 d-flex flex-column border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="m-0"><i class="fas fa-shopping-cart me-2"></i> Orden Actual</h5>
                        <small class="opacity-75" id="cart-date">${new Date().toLocaleDateString()}</small>
                    </div>
                    
                    <div class="card-body p-0 flex-grow-1" style="overflow-y: auto; background-color: #f8f9fa;">
                        <div id="cart-items-container">
                            <!-- Items del carrito -->
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold" id="cart-subtotal">Q 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">IVA (12%)</span>
                            <span class="fw-bold" id="cart-tax">Q 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-4 fw-bold text-dark">Total</span>
                            <span class="fs-4 fw-bold text-success" id="cart-total">Q 0.00</span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-lg" onclick="openPaymentModal()" id="btn-pay">
                                <i class="fas fa-wallet me-2"></i> Pagar (F9)
                            </button>
                            <button class="btn btn-outline-danger" onclick="clearCart()">
                                <i class="fas fa-trash me-2"></i> Cancelar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderInventario() {
    return `
        <div class="page-header">
            <h2 class="page-title">Inventario</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary"><i class="fas fa-file-export"></i> Exportar</button>
                <button class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Producto</button>
            </div>
        </div>

        <div class="card p-3">
            <div class="d-flex gap-3 mb-4">
                <input type="text" class="form-control hover-shadow" id="inv-search" placeholder="Buscar producto...">
                <select class="form-select w-25" id="inv-filter-provider">
                    <option value="">Todos los proveedores</option>
                    ${[...new Set(MOCK_DB.productos.map(p => p.proveedor))].map(p => `<option value="${p}">${p}</option>`).join('')}
                </select>
                <div class="form-check form-switch d-flex align-items-center gap-2 border px-3 rounded">
                    <input class="form-check-input" type="checkbox" id="inv-filter-promo">
                    <label class="form-check-label" for="inv-filter-promo">Solo Promociones</label>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>Costo</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Vencimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="inv-table-body">
                        <!-- Llenado por JS -->
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderClientes() {
    return `
        <div class="page-header">
            <h2 class="page-title">Gestión de Clientes</h2>
            <button class="btn btn-primary"><i class="fas fa-user-plus"></i> Nuevo Cliente</button>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Puntos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${MOCK_DB.clientes.map(c => `
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px">
                                                    ${c.nombre.charAt(0)}
                                                </div>
                                                ${c.nombre}
                                            </div>
                                        </td>
                                        <td>${c.telefono || 'N/A'}</td>
                                        <td><span class="badge bg-info text-dark">${c.tipo}</span></td>
                                        <td class="fw-bold text-success">${c.puntos} pts</td>
                                        <td>
                                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-gift fa-3x mb-3 opacity-50"></i>
                        <h4>Programa de Lealtad</h4>
                        <p>1 punto por cada Q10.00 de compra.</p>
                        <p class="mb-0 fw-bold">Próximo canje: 500pts = Q50.00</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderCaja() {
    const totalEfectivo = MOCK_DB.ventas.filter(v => v.metodo === 'efectivo').reduce((sum, v) => sum + v.total, 0);
    const totalTarjeta = MOCK_DB.ventas.filter(v => v.metodo === 'tarjeta').reduce((sum, v) => sum + v.total, 0);
    const totalDia = totalEfectivo + totalTarjeta;

    return `
        <div class="page-header">
            <h2 class="page-title">Cierre de Caja</h2>
            <div class="badge bg-success fs-6">Turno Abierto</div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card p-4 text-center">
                    <h5 class="text-muted mb-4">Resumen del Día</h5>
                    <h1 class="display-3 fw-bold text-dark mb-4">Q ${totalDia.toFixed(2)}</h1>
                    
                    <div class="row mt-4">
                        <div class="col-6 border-end">
                            <p class="text-muted mb-1">Efectivo en Caja</p>
                            <h3 class="text-success fw-bold">Q ${totalEfectivo.toFixed(2)}</h3>
                            <small class="text-muted"><i class="fas fa-arrow-up"></i> ${MOCK_DB.ventas.filter(v => v.metodo === 'efectivo').length} transacciones</small>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Tarjetas / Digital</p>
                            <h3 class="text-primary fw-bold">Q ${totalTarjeta.toFixed(2)}</h3>
                            <small class="text-muted"><i class="fas fa-credit-card"></i> ${MOCK_DB.ventas.filter(v => v.metodo === 'tarjeta').length} transacciones</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button class="btn btn-warning btn-lg w-100" onclick="alert('Cierre de caja simulado correctamente. Reporte generado.')">
                        <i class="fas fa-lock me-2"></i> REALIZAR CIERRE DE TURNO
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h6>Billetes y Monedas (Arqueo)</h6>
                    <form onsubmit="event.preventDefault()">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label>Q 100.00</label>
                            <input type="number" class="form-control form-control-sm w-50" placeholder="0">
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label>Q 50.00</label>
                            <input type="number" class="form-control form-control-sm w-50" placeholder="0">
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label>Q 20.00</label>
                            <input type="number" class="form-control form-control-sm w-50" placeholder="0">
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label>Q 10.00</label>
                            <input type="number" class="form-control form-control-sm w-50" placeholder="0">
                        </div>
                        <div class="d-flex justify-content-between mt-3 fw-bold">
                            <span>Total Contado:</span>
                            <span>Q 0.00</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
}


// === LOGIC HANDLERS ===

// POS LOGIC
function setupPOSListeners() {
    renderPOSGrid(MOCK_DB.productos);

    // Search Filter
    document.getElementById('pos-search').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = MOCK_DB.productos.filter(p =>
            p.nombre.toLowerCase().includes(term) ||
            p.codigo.includes(term)
        );
        renderPOSGrid(filtered);
    });

    // Category Filter
    document.getElementById('pos-category-filter').addEventListener('change', (e) => {
        const cat = e.target.value;
        const filtered = cat
            ? MOCK_DB.productos.filter(p => p.categoria === cat)
            : MOCK_DB.productos;
        renderPOSGrid(filtered);
    });
}

function renderPOSGrid(products) {
    const grid = document.getElementById('pos-grid');
    grid.innerHTML = products.map(p => `
        <div class="col">
            <div class="product-card" onclick="addToCart(${p.id})">
                ${p.promocion ? '<span class="badge bg-warning text-dark product-badge">Oferta</span>' : ''}
                <div class="fw-bold text-muted mb-1 text-xs">#${p.codigo}</div>
                <h6 class="mb-2 text-truncate" title="${p.nombre}">${p.nombre}</h6>
                <div class="d-flex justify-content-between align-items-end mt-3">
                    <div>
                        <div class="product-price">Q ${p.precio.toFixed(2)}</div>
                        <small class="text-muted text-xs">${p.stock} disponibles</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px;height:32px;padding:0">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function addToCart(productId) {
    const product = MOCK_DB.productos.find(p => p.id === productId);
    const existing = carrito.find(item => item.id === productId);

    if (existing) {
        if (existing.cantidad < product.stock) {
            existing.cantidad++;
        } else {
            showToast(`Solo hay ${product.stock} unidades disponibles`, 'warning');
            return;
        }
    } else {
        carrito.push({ ...product, cantidad: 1 });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items-container');
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl = document.getElementById('cart-total');

    if (carrito.length === 0) {
        container.innerHTML = `
            <div class="text-center p-5 text-muted">
                <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                <p>El carrito está vacío</p>
            </div>`;
        subtotalEl.innerText = 'Q 0.00';
        totalEl.innerText = 'Q 0.00';
        document.getElementById('btn-pay').disabled = true;
        return;
    }

    document.getElementById('btn-pay').disabled = false;

    let total = 0;
    container.innerHTML = carrito.map((item, idx) => {
        const itemTotal = item.precio * item.cantidad;
        total += itemTotal;
        return `
            <div class="cart-item px-3 bg-white">
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.nombre}</div>
                    <div class="cart-item-price">Q ${item.precio.toFixed(2)} x ${item.cantidad}</div>
                </div>
                <div class="cart-controls">
                    <div class="qty-btn" onclick="updateQty(${idx}, -1)"><i class="fas fa-minus"></i></div>
                    <span class="fw-bold" style="min-width: 20px; text-align: center">${item.cantidad}</span>
                    <div class="qty-btn" onclick="updateQty(${idx}, 1)"><i class="fas fa-plus"></i></div>
                </div>
                <div class="fw-bold ms-3" style="width: 70px; text-align: right">Q ${itemTotal.toFixed(2)}</div>
                <button class="btn btn-sm text-danger ms-2" onclick="removeFromCart(${idx})"><i class="fas fa-times"></i></button>
            </div>
        `;
    }).join('');

    subtotalEl.innerText = `Q ${total.toFixed(2)}`;
    document.getElementById('cart-tax').innerText = `Q ${(total * 0.12).toFixed(2)}`;
    totalEl.innerText = `Q ${total.toFixed(2)}`;
}

function updateQty(index, change) {
    const item = carrito[index];
    const newQty = item.cantidad + change;

    if (newQty > 0 && newQty <= item.stock) {
        item.cantidad = newQty;
        renderCart();
    }
}

function removeFromCart(index) {
    carrito.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (confirm('¿Vaciar carrito?')) {
        carrito = [];
        renderCart();
    }
}

// POS PAYMENT LOGIC
let ventaActualTotal = 0;

function openPaymentModal() {
    ventaActualTotal = carrito.reduce((sum, i) => sum + (i.precio * i.cantidad), 0);
    document.getElementById('modal-total-pagar').innerText = `Q ${ventaActualTotal.toFixed(2)}`;

    // Populate Clients
    const select = document.getElementById('select-cliente-venta');
    select.innerHTML = MOCK_DB.clientes.map(c => `<option value="${c.id}">${c.nombre} (${c.tipo})</option>`).join('');

    // Reset Inputs
    document.getElementById('input-pago-recibido').value = '';
    document.getElementById('lbl-cambio').innerText = 'Q 0.00';
    document.getElementById('seccion-efectivo').style.display = 'block';
    document.getElementById('pago-efectivo').checked = true;

    new bootstrap.Modal(document.getElementById('modalCobrar')).show();
}

function togglePagoInput(method) {
    const section = document.getElementById('seccion-efectivo');
    section.style.display = method === 'efectivo' ? 'block' : 'none';
}

function calcularCambio() {
    const recibido = parseFloat(document.getElementById('input-pago-recibido').value) || 0;
    const cambio = recibido - ventaActualTotal;
    const lbl = document.getElementById('lbl-cambio');

    if (cambio >= 0) {
        lbl.innerText = `Q ${cambio.toFixed(2)}`;
        lbl.classList.remove('text-danger');
        lbl.classList.add('text-success');
    } else {
        lbl.innerText = `Faltan Q ${Math.abs(cambio).toFixed(2)}`;
        lbl.classList.remove('text-success');
        lbl.classList.add('text-danger');
    }
}

function procesarVentaConfirmada() {
    // 1. Validar pago
    const metodo = document.querySelector('input[name="metodoPago"]:checked').value;
    if (metodo === 'efectivo') {
        const recibido = parseFloat(document.getElementById('input-pago-recibido').value) || 0;
        if (recibido < ventaActualTotal) {
            alert('El pago es insuficiente');
            return;
        }
    }

    // 2. Registrar Venta (Mock)
    const nuevaVenta = {
        id: Date.now(),
        fecha: new Date(),
        total: ventaActualTotal,
        metodo: metodo,
        items: carrito.reduce((sum, i) => sum + i.cantidad, 0)
    };
    MOCK_DB.ventas.push(nuevaVenta);

    // 3. Descontar Stock
    carrito.forEach(cartItem => {
        const product = MOCK_DB.productos.find(p => p.id === cartItem.id);
        if (product) product.stock -= cartItem.cantidad;
    });

    // 4. Reset & Close
    bootstrap.Modal.getInstance(document.getElementById('modalCobrar')).hide();
    carrito = [];
    renderCart(); // Limpia UI
    showToast('¡Venta realizada con éxito!');
    loadView('pos'); // Recargar para actualizar stocks visuales
}

// INVENTORY LOGIC
function setupInventarioListeners() {
    renderInventoryTable(MOCK_DB.productos);

    const filterHandler = () => {
        const term = document.getElementById('inv-search').value.toLowerCase();
        const provider = document.getElementById('inv-filter-provider').value;
        const onlyPromo = document.getElementById('inv-filter-promo').checked;

        const filtered = MOCK_DB.productos.filter(p => {
            const matchesTerm = p.nombre.toLowerCase().includes(term) || p.codigo.includes(term);
            const matchesProv = provider ? p.proveedor === provider : true;
            const matchesPromo = onlyPromo ? p.promocion : true;
            return matchesTerm && matchesProv && matchesPromo;
        });

        renderInventoryTable(filtered);
    };

    document.getElementById('inv-search').addEventListener('input', filterHandler);
    document.getElementById('inv-filter-provider').addEventListener('change', filterHandler);
    document.getElementById('inv-filter-promo').addEventListener('change', filterHandler);
}

function renderInventoryTable(products) {
    const tbody = document.getElementById('inv-table-body');
    tbody.innerHTML = products.map(p => `
        <tr>
            <td><span class="badge bg-light text-dark border">${p.codigo}</span></td>
            <td>
                <div class="fw-bold">${p.nombre}</div>
                ${p.promocion ? '<span class="badge bg-warning text-dark text-xs">Promoción</span>' : ''}
            </td>
            <td>${p.categoria}</td>
            <td>${p.proveedor}</td>
            <td>Q ${p.costo.toFixed(2)}</td>
            <td class="fw-bold">Q ${p.precio.toFixed(2)}</td>
            <td>
                <span class="badge ${p.stock < 10 ? 'badge-stock-low' : 'badge-stock-ok'} p-2">
                    ${p.stock} ${p.unidad}s
                </span>
            </td>
            <td>${p.vencimiento || '-'}</td>
            <td>
                <button class="btn btn-sm btn-light"><i class="fas fa-pen"></i></button>
            </td>
        </tr>
    `).join('');
}


// UTILS
function showToast(msg, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    document.getElementById('toast-body').innerText = msg;
    const header = document.getElementById('toast-header-bg');

    header.className = `toast-header text-white ${type === 'success' ? 'bg-success' : 'bg-danger'}`;

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// === AUTH LOGIC ===
function logout() {
    if (confirm('¿Estás seguro de cerrar sesión?')) {
        window.location.href = 'login.html';
    }
}
// funcion de loadView
document.addEventListener('DOMContentLoaded', () => {
    console.log("Sistema Iniciado en modo PHP");

    // Si estamos en la página de POS (Punto de Venta)
    if(document.getElementById('pos-grid')) {
        setupPOSListeners(); // Activa la búsqueda y carga productos
        renderCart();        // Muestra el carrito vacío
    }

    // Si estamos en la página de Inventario
    if(document.getElementById('inv-table-body')) {
        setupInventarioListeners(); // Carga la tabla
    }
    
    // Si estamos en caja
    if(document.getElementById('caja-resumen')) {
        renderCaja();
    }
});