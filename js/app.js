/**
 * js/app.js
 * Frontend Logic para La Esquinita POS v2.1
 * - Conexión API Real
 * - Búsqueda insensible a acentos
 * - Categorías dinámicas
 * - Manejo robusto de errores
 */

// === ESTADO GLOBAL DE LA APP ===
let APP_STATE = {
    productos: [],
    categorias: [],
    carrito: []
};

// === INICIO DE LA APLICACIÓN ===
document.addEventListener('DOMContentLoaded', () => {
    // Detectar en qué vista estamos
    if (document.getElementById('pos-grid')) {
        setupPOS();
    } else if (document.getElementById('inv-table-body')) {
        setupInventario();
    } else if (document.getElementById('btn-cierre-caja')) {
        renderCaja();
    }
});


// === LÓGICA DEL POS (PUNTO DE VENTA) ===

async function setupPOS() {
    // 1. Configurar listeners básicos INMEDIATAMENTE (para que el buscador no esté muerto si falla la API)
    setupPOSListeners();

    try {
        // 2. Cargar datos reales
        await loadProductosFromAPI();

        // 3. Renderizar UI con datos
        checkExpirations();
        renderPOSGrid(APP_STATE.productos);
        renderCategories(); // Generar botones de categoría basados en la DB
        renderCart();

    } catch (error) {
        console.error("Error iniciando POS:", error);
        // Mostrar error amigable pero informativo
        showToast(`Error de conexión: ${error.message}`, "danger");
    }
}

async function loadProductosFromAPI() {
    try {
        const response = await fetch('api/productos.php');
        const text = await response.text();

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${text.substring(0, 50)}...`);
        }

        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.warn("Respuesta inválida:", text);
            throw new Error("Respuesta del servidor no válida (No es JSON).");
        }

        if (result.success) {
            APP_STATE.productos = result.data.map(p => ({
                ...p,
                // Asegurar tipos de datos correctos
                precio_venta: parseFloat(p.precio_venta),
                stock_actual: parseInt(p.stock_actual),
                descuento_manual: 0 // Campo local para la sesión actual
            }));

            // Extraer categorías únicas y válidas (sin nulos)
            const cats = APP_STATE.productos.map(p => p.categoria_nombre).filter(Boolean);
            APP_STATE.categorias = [...new Set(cats)].sort();

        } else {
            throw new Error(result.message || 'Error desconocido');
        }
    } catch (error) {
        throw error; // Re-lanzar para que lo muestre el Toast
    }
}

function setupPOSListeners() {
    // Search Filter (Insensible a acentos: jabon == Jabón)
    const searchInput = document.getElementById('pos-search');
    if (searchInput) {
        // Remover listeners anteriores si existen (clonando nodo) es una opción, 
        // pero aquí solo agregamos uno.
        searchInput.addEventListener('input', (e) => {
            const term = normalizeString(e.target.value);

            const filtered = APP_STATE.productos.filter(p =>
                normalizeString(p.nombre).includes(term) ||
                normalizeString(p.codigo_barras || '').includes(term)
            );
            renderPOSGrid(filtered);
        });
    }

    // Keybindings Globales
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            document.getElementById('pos-search')?.focus();
        } else if (e.key === 'F9') {
            e.preventDefault();
            if (APP_STATE.carrito.length > 0) openPaymentModal();
        }
    });
}

function renderCategories() {
    const container = document.getElementById('category-pills');
    if (!container) return;

    // Botón "Todo" inicial
    let html = `
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm active flex-shrink-0" data-cat="">
            <i class="fas fa-th-large me-1"></i> Todo
        </button>
    `;

    // Botones dinámicos
    APP_STATE.categorias.forEach(cat => {
        html += `
            <button class="btn btn-outline-secondary rounded-pill px-4 fw-bold border-0 bg-white shadow-sm flex-shrink-0" data-cat="${cat}">
                ${cat}
            </button>
        `;
    });

    container.innerHTML = html;

    // Asignar eventos a los nuevos botones
    container.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            // UI Update
            container.querySelectorAll('button').forEach(b => {
                b.classList.remove('active', 'btn-primary', 'text-white');
                b.classList.add('btn-outline-secondary', 'bg-white');
            });
            const target = e.currentTarget;
            target.classList.remove('btn-outline-secondary', 'bg-white');
            target.classList.add('active', 'btn-primary', 'text-white');

            // Filter Logic
            const cat = target.dataset.cat;
            const filtered = cat
                ? APP_STATE.productos.filter(p => p.categoria_nombre === cat)
                : APP_STATE.productos;
            renderPOSGrid(filtered);
        });
    });
}

// Helper para búsquedas insensibles a acentos
function normalizeString(str) {
    if (!str) return "";
    return str.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

// --- GESTIÓN DE VENCIMIENTOS ---

function checkExpirations() {
    const today = new Date();
    const limitDate = new Date();
    limitDate.setDate(today.getDate() + 60);

    const expiring = APP_STATE.productos.filter(p => {
        if (!p.fecha_vencimiento) return false;
        const venci = new Date(p.fecha_vencimiento);
        return venci >= today && venci <= limitDate;
    });

    const badge = document.getElementById('expiration-badge');
    if (badge) {
        if (expiring.length > 0) {
            badge.innerText = expiring.length;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

function openExpirationModal() {
    const today = new Date();
    const limitDate = new Date();
    limitDate.setDate(today.getDate() + 60);

    const expiring = APP_STATE.productos.filter(p => {
        if (!p.fecha_vencimiento) return false;
        const venci = new Date(p.fecha_vencimiento);
        return venci >= today && venci <= limitDate;
    });

    const tbody = document.getElementById('vencimientos-table-body');
    if (expiring.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No hay productos por vencer pronto.</td></tr>';
    } else {
        tbody.innerHTML = expiring.map(p => {
            const diasRestantes = Math.ceil((new Date(p.fecha_vencimiento) - today) / (1000 * 60 * 60 * 24));
            const currentDiscount = p.descuento_manual || 0;
            const precioActual = p.precio_venta;
            const nuevoPrecio = precioActual * (1 - (currentDiscount / 100));

            return `
                <tr>
                    <td class="ps-4 fw-bold text-dark">${p.nombre}</td>
                    <td>${p.fecha_vencimiento}</td>
                    <td><span class="badge bg-danger">${diasRestantes} días</span></td>
                    <td>Q ${precioActual.toFixed(2)}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control text-center fw-bold text-primary manual-discount-input" 
                                   data-id="${p.id}" 
                                   value="${currentDiscount}" 
                                   min="0" max="100" 
                                   onchange="updatePreviewPrice(this, ${precioActual})">
                            <span class="input-group-text">%</span>
                        </div>
                    </td>
                    <td class="pe-4 text-end fw-bold text-success preview-price-cell">Q ${nuevoPrecio.toFixed(2)}</td>
                </tr>
            `;
        }).join('');
    }

    new bootstrap.Modal(document.getElementById('modalVencimientos')).show();
}

function updatePreviewPrice(input, precioBase) {
    const descuento = parseFloat(input.value) || 0;
    const nuevoPrecio = precioBase * (1 - (descuento / 100));
    const row = input.closest('tr');
    const priceCell = row.querySelector('.preview-price-cell');
    if (priceCell) {
        priceCell.innerText = `Q ${nuevoPrecio.toFixed(2)}`;
    }
}

function guardarDescuentos() {
    const inputs = document.querySelectorAll('.manual-discount-input');
    inputs.forEach(input => {
        const id = parseInt(input.dataset.id);
        const descuento = parseFloat(input.value) || 0;

        const producto = APP_STATE.productos.find(p => p.id === id);
        if (producto) {
            producto.descuento_manual = descuento;
            producto.promocion = descuento > 0;
        }
    });

    showToast('Descuentos aplicados localmente', 'success');
    bootstrap.Modal.getInstance(document.getElementById('modalVencimientos')).hide();

    renderPOSGrid(APP_STATE.productos);
    renderCart();
}

// --- VISUALIZACIÓN DE PRODUCTOS ---

function renderPOSGrid(productos) {
    const grid = document.getElementById('pos-grid');
    if (productos.length === 0) {
        grid.innerHTML = '<div class="col-12 text-center py-5 text-muted">No se encontraron productos</div>';
        return;
    }

    grid.innerHTML = productos.map(p => {
        const hasDiscount = p.descuento_manual && p.descuento_manual > 0;
        const finalPrice = hasDiscount
            ? p.precio_venta * (1 - (p.descuento_manual / 100))
            : p.precio_venta;

        return `
        <div class="col">
            <div class="product-card" onclick="addToCart(${p.id})">
                <div class="position-relative">
                   ${hasDiscount
                ? `<span class="badge bg-danger text-white product-badge">-${p.descuento_manual}% Oferta</span>`
                : (p.promocion ? '<span class="badge bg-warning text-dark product-badge">Oferta</span>' : '')}
                   
                   ${p.stock_actual <= 5 ? '<span class="badge bg-dark position-absolute top-0 start-0 m-2">Poco Stock</span>' : ''}
                </div>
                
                <div class="fw-bold text-muted mb-1 text-xs mt-2">#${p.codigo_barras || 'S/N'}</div>
                <h6 class="mb-2 text-truncate" title="${p.nombre}">${p.nombre}</h6>
                
                <div class="d-flex justify-content-between align-items-end mt-3">
                    <div>
                        ${hasDiscount
                ? `<div class="text-decoration-line-through text-muted text-xs">Q ${p.precio_venta.toFixed(2)}</div>
                               <div class="product-price text-danger">Q ${finalPrice.toFixed(2)}</div>`
                : `<div class="product-price">Q ${p.precio_venta.toFixed(2)}</div>`
            }
                        <small class="text-muted text-xs">${p.stock_actual} disponibles</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px;height:32px;padding:0">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    `}).join('');
}

function addToCart(productId) {
    const product = APP_STATE.productos.find(p => p.id === productId);
    const existing = APP_STATE.carrito.find(item => item.id === productId);

    if (existing) {
        if (existing.cantidad < product.stock_actual) {
            existing.cantidad++;
        } else {
            showToast(`Solo quedan ${product.stock_actual}`, 'warning');
            return;
        }
    } else {
        APP_STATE.carrito.push({ ...product, cantidad: 1 });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items-container');
    const totalEl = document.getElementById('cart-total');
    const btnPay = document.getElementById('btn-pay');

    if (!container || !totalEl || !btnPay) {
        console.warn('Elementos del carrito no encontrados en el DOM');
        return;
    }

    if (APP_STATE.carrito.length === 0) {
        container.innerHTML = `
            <div class="text-center p-5 text-muted">
                <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                <p>Carrito vacío</p>
            </div>`;
        totalEl.innerText = 'Q 0.00';
        
        // Verificar si existen antes de actualizar
        const subtotalEl = document.getElementById('cart-subtotal-display');
        const discountEl = document.getElementById('cart-discount-display');
        
        if (subtotalEl) subtotalEl.innerText = 'Q 0.00';
        if (discountEl) discountEl.innerText = '- Q 0.00';
        
        btnPay.disabled = true;
        return;
    }

    btnPay.disabled = false;
    let total = 0;
    let totalDescuento = 0;

    container.innerHTML = APP_STATE.carrito.map((item, idx) => {
        const hasDiscount = item.descuento_manual && item.descuento_manual > 0;
        const precioFinal = hasDiscount
            ? item.precio_venta * (1 - (item.descuento_manual / 100))
            : item.precio_venta;

        const itemTotal = precioFinal * item.cantidad;
        const ahorro = (item.precio_venta - precioFinal) * item.cantidad;

        total += itemTotal;
        totalDescuento += ahorro;

        return `
            <div class="cart-item px-3 bg-white border-bottom py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold text-truncate" style="max-width: 150px;">${item.nombre}</div>
                        <small class="text-muted">
                            ${hasDiscount ? `<span class="text-danger fw-bold">Q ${precioFinal.toFixed(2)}</span> <s class="text-xs">Q ${item.precio_venta.toFixed(2)}</s>` : `Q ${item.precio_venta.toFixed(2)}`} x ${item.cantidad}
                        </small>
                    </div>
                    <div class="fw-bold">Q ${itemTotal.toFixed(2)}</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="updateQty(${idx}, -1)"><i class="fas fa-minus"></i></button>
                        <button class="btn btn-outline-secondary disabled fw-bold text-dark" style="min-width: 30px;">${item.cantidad}</button>
                        <button class="btn btn-outline-secondary" onclick="updateQty(${idx}, 1)"><i class="fas fa-plus"></i></button>
                    </div>
                    <button class="btn btn-sm text-danger" onclick="removeFromCart(${idx})"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
    }).join('');

    // Verificar si existen antes de actualizar
    const subtotalEl = document.getElementById('cart-subtotal-display');
    const discountEl = document.getElementById('cart-discount-display');
    
    if (subtotalEl) subtotalEl.innerText = `Q ${(total + totalDescuento).toFixed(2)}`;
    if (discountEl) discountEl.innerText = `- Q ${totalDescuento.toFixed(2)}`;
    totalEl.innerText = `Q ${total.toFixed(2)}`;
}

function updateQty(index, change) {
    const item = APP_STATE.carrito[index];
    const newQty = item.cantidad + change;
    if (newQty > 0 && newQty <= item.stock_actual) {
        item.cantidad = newQty;
        renderCart();
    }
}

function removeFromCart(index) {
    APP_STATE.carrito.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (confirm('¿Borrar todo el carrito?')) {
        APP_STATE.carrito = [];
        renderCart();
    }
}


// === LÓGICA DE PAGOS ===

let totalVentaActual = 0;

function openPaymentModal() {
    totalVentaActual = APP_STATE.carrito.reduce((sum, item) => {
        const hasDiscount = item.descuento_manual && item.descuento_manual > 0;
        const precioFinal = hasDiscount
            ? item.precio_venta * (1 - (item.descuento_manual / 100))
            : item.precio_venta;
        return sum + (precioFinal * item.cantidad);
    }, 0);

    const totalEl = document.getElementById('modal-total-pagar');
    if (totalEl) totalEl.innerText = `Q ${totalVentaActual.toFixed(2)}`;

    // Hardcoded clientes
    const clientes = [
        { id: 1, nombre: 'Público General' },
        { id: 2, nombre: 'Cliente Frecuente' }
    ];
    const select = document.getElementById('select-cliente-venta');
    if (select) select.innerHTML = clientes.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');

    // Resetear UI
    const inputPago = document.getElementById('input-pago-recibido');
    if (inputPago) inputPago.value = '';

    const lblCambio = document.getElementById('lbl-cambio');
    if (lblCambio) lblCambio.innerText = 'Q 0.00';

    const rbEfectivo = document.getElementById('pago-efectivo');
    if (rbEfectivo) rbEfectivo.click();

    const modalEl = document.getElementById('modalCobrar');
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
        setTimeout(() => document.getElementById('input-pago-recibido')?.focus(), 500);
    }
}

function togglePagoInput(metodo) {
    const seccionEfectivo = document.getElementById('seccion-efectivo');
    if (!seccionEfectivo) return;

    if (metodo === 'efectivo') {
        seccionEfectivo.style.display = 'block';
        setTimeout(() => document.getElementById('input-pago-recibido')?.focus(), 100);
    } else {
        seccionEfectivo.style.display = 'none';
        const lblCambio = document.getElementById('lbl-cambio');
        if (lblCambio) lblCambio.innerText = 'Q 0.00';
    }
}

function calcularCambio() {
    const inputEl = document.getElementById('input-pago-recibido');
    const lbl = document.getElementById('lbl-cambio');
    
    if (!inputEl || !lbl) return;
    
    const recibido = parseFloat(inputEl.value) || 0;
    const cambio = recibido - totalVentaActual;

    if (cambio >= 0) {
        lbl.innerText = `Q ${cambio.toFixed(2)}`;
        lbl.className = 'fw-bold text-success';
    } else {
        lbl.innerText = `Faltan Q ${Math.abs(cambio).toFixed(2)}`;
        lbl.className = 'fw-bold text-danger';
    }
}

async function procesarVentaConfirmada() {
    const metodo = document.querySelector('input[name="metodoPago"]:checked').value;

    if (metodo === 'efectivo') {
        const recibido = parseFloat(document.getElementById('input-pago-recibido').value) || 0;
        if (recibido < totalVentaActual) {
            alert('El monto recibido es menor al total.');
            return;
        }
    }

    showToast('¡Venta realizada correctamente!', 'success');

    // Cerrar modal
    const modalEl = document.getElementById('modalCobrar');
    bootstrap.Modal.getInstance(modalEl).hide();

    // Limpiar
    APP_STATE.carrito = [];
    renderCart();

    // Recargar productos
    await setupPOS();
}

// === UTILIDADES ===
function showToast(msg, type = 'success') {
    // Si tienes toastr o un componente de bootstrap
    // Por ahora alert simple o console log para no bloquear interfaz agresivamente
    console.log(`Toast [${type}]: ${msg}`);
    if (type === 'danger') alert(msg);
}