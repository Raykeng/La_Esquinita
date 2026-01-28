/**
 * POS.JS - Sistema de Punto de Venta La Esquinita
 * Autor: Marlon
 * Funciones para el módulo POS
 */

// Variables globales del POS
let carrito = [];
let productos = [];
let totalCarrito = 0;
let descuentoTotal = 0;

// Función para obtener la URL base de la API
function getApiBaseUrl() {
    const pathname = window.location.pathname;
    const dirs = pathname.split('/').filter(Boolean);

    // Si termina en index.php, lo quitamos
    if (dirs[dirs.length - 1] === 'index.php') {
        dirs.pop();
    }

    const baseUrl = '/' + dirs.join('/') + '/';
    console.log('🔧 API Base URL calculada:', baseUrl);
    return baseUrl;
}

// Inicializar POS cuando se carga la página
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('pos-grid')) {
        inicializarPOS();
    }
});

/**
 * Inicializar el sistema POS
 */
function inicializarPOS() {
    cargarProductos();
    cargarCategorias(); // Agregar carga de categorías
    configurarBusqueda();
    configurarFiltrosCategorias();
    actualizarCarrito();
    verificarProductosVencimiento();
}

/**
 * Cargar productos desde la API
 */
async function cargarProductos() {
    console.log('🔄 Iniciando carga de productos...');

    try {
        // Calcula la URL base dinámicamente
        const apiBaseUrl = getApiBaseUrl();
        const url = window.location.origin + apiBaseUrl + 'api/productos.php';
        console.log('📡 Fetching from:', url);

        const response = await fetch(url);
        console.log('📡 Respuesta de la API:', response);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('📦 Datos recibidos:', data);

        if (data.success) {
            productos = data.data;
            console.log('✅ Productos cargados:', productos.length, 'productos');
            mostrarProductos(productos);
        } else {
            console.error('❌ Error al cargar productos:', data.message);
            console.log('🔄 Mostrando productos de ejemplo...');
            mostrarProductosEjemplo();
        }
    } catch (error) {
        console.error('💥 Error en la conexión:', error);
        console.log('🔄 Mostrando productos de ejemplo...');
        mostrarProductosEjemplo();
    }
}

/**
 * Cargar categorías desde la API
 */
async function cargarCategorias() {
    try {
        const apiBaseUrl = getApiBaseUrl();
        const url = window.location.origin + apiBaseUrl + 'api/categorias.php';
        console.log('📂 Cargando categorías desde:', url);

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            mostrarCategorias(data.data);
        } else {
            console.error('Error al cargar categorías:', data.message);
            mostrarCategoriasEjemplo();
        }
    } catch (error) {
        console.error('Error cargando categorías:', error);
        mostrarCategoriasEjemplo();
    }
}

/**
 * Mostrar categorías en el header
 */
function mostrarCategorias(categorias) {
    const container = document.getElementById('category-pills');
    if (!container) return;

    let html = `
        <span class="text-muted small fw-bold me-3">Filtrar por:</span>
        <button class="btn btn-primary rounded-pill px-3 py-2 active" data-cat="">
            <i class="fas fa-th-large me-2"></i>Todos
        </button>
    `;

    categorias.forEach(categoria => {
        html += `
            <button class="btn btn-outline-primary rounded-pill px-3 py-2" data-cat="${categoria.nombre}">
                <i class="fas fa-tag me-2"></i>${categoria.nombre}
            </button>
        `;
    });

    container.innerHTML = html;
}

/**
 * Mostrar categorías de ejemplo
 */
function mostrarCategoriasEjemplo() {
    const container = document.getElementById('category-pills');
    if (!container) return;

    const categoriasEjemplo = ['Bebidas', 'Abarrotes', 'Frescos', 'Lácteos', 'Limpieza'];

    let html = `
        <span class="text-muted small fw-bold me-3">Filtrar por:</span>
        <button class="btn btn-primary rounded-pill px-3 py-2 active" data-cat="">
            <i class="fas fa-th-large me-2"></i>Todos
        </button>
    `;

    categoriasEjemplo.forEach(categoria => {
        html += `
            <button class="btn btn-outline-primary rounded-pill px-3 py-2" data-cat="${categoria}">
                <i class="fas fa-tag me-2"></i>${categoria}
            </button>
        `;
    });

    container.innerHTML = html;
}
function mostrarProductos(productosArray) {
    console.log('🎨 Mostrando productos en el grid:', productosArray.length, 'productos');
    const grid = document.getElementById('pos-grid');

    if (!grid) {
        console.error('❌ No se encontró el elemento pos-grid');
        return;
    }

    grid.innerHTML = '';

    productosArray.forEach((producto, index) => {
        console.log(`📦 Procesando producto ${index + 1}:`, producto.nombre);
        const precioConDescuento = calcularPrecioConDescuento(producto);
        const tieneDescuento = precioConDescuento < producto.precio_venta;

        const card = document.createElement('div');
        card.className = 'col';
        card.innerHTML = `
            <div class="product-card shadow-sm position-relative">
                ${tieneDescuento ? '<span class="product-badge badge bg-warning">¡Oferta!</span>' : ''}
                ${producto.stock_actual <= producto.stock_minimo ? '<span class="product-badge badge bg-danger" style="top: 4px; left: 4px;">Stock Bajo</span>' : ''}
                
                <div class="text-center">
                    <i class="fas fa-box-open text-primary product-icon"></i>
                </div>
                
                <h6 class="fw-bold text-truncate product-name" title="${producto.nombre}">${producto.nombre}</h6>
                
                <div class="d-flex justify-content-between align-items-center product-details">
                    <small class="text-muted">${producto.categoria_nombre || 'Sin categoría'}</small>
                    <small class="text-muted">Stock: ${producto.stock_actual}</small>
                </div>
                
                <div class="price-section mb-2">
                    ${tieneDescuento ? `
                        <div class="text-decoration-line-through text-muted" style="font-size: 0.7rem;">Q ${parseFloat(producto.precio_venta).toFixed(2)}</div>
                        <div class="product-price text-success">Q ${precioConDescuento.toFixed(2)}</div>
                    ` : `
                        <div class="product-price">Q ${parseFloat(producto.precio_venta).toFixed(2)}</div>
                    `}
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-danger btn-sm rounded-circle p-1" onclick="event.stopPropagation(); quitarDelCarrito(${producto.id})" style="width: 28px; height: 28px; font-size: 0.7rem;">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="badge bg-primary" id="qty-${producto.id}">0</span>
                    <button class="btn btn-outline-success btn-sm rounded-circle p-1" onclick="event.stopPropagation(); agregarAlCarrito(${producto.id})" style="width: 28px; height: 28px; font-size: 0.7rem;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                
                ${producto.por_vencer ? '<small class="text-warning mt-1"><i class="fas fa-clock"></i> Próximo a vencer</small>' : ''}
            </div>
        `;
        grid.appendChild(card);
    });

    console.log('✅ Grid actualizado con', productosArray.length, 'productos');
}

/**
 * Calcular precio con descuento por vencimiento
 */
function calcularPrecioConDescuento(producto) {
    if (!producto.fecha_vencimiento || !producto.por_vencer) {
        return parseFloat(producto.precio_venta);
    }

    const hoy = new Date();
    const fechaVencimiento = new Date(producto.fecha_vencimiento);
    const diasRestantes = Math.ceil((fechaVencimiento - hoy) / (1000 * 60 * 60 * 24));

    let porcentajeDescuento = 0;

    if (diasRestantes <= 6) {
        porcentajeDescuento = 50;
    } else if (diasRestantes <= 14) {
        porcentajeDescuento = 30;
    } else if (diasRestantes <= 29) {
        porcentajeDescuento = 20;
    } else if (diasRestantes <= 60) {
        porcentajeDescuento = 10;
    }

    const precioOriginal = parseFloat(producto.precio_venta);
    return precioOriginal * (1 - porcentajeDescuento / 100);
}

/**
 * Configurar búsqueda de productos
 */
function configurarBusqueda() {
    const searchInput = document.getElementById('pos-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const termino = this.value.toLowerCase();
            const productosFiltrados = productos.filter(producto =>
                producto.nombre.toLowerCase().includes(termino) ||
                producto.codigo_barras.includes(termino)
            );
            mostrarProductos(productosFiltrados);
        });
    }
}

/**
 * Configurar filtros de categorías
 */
function configurarFiltrosCategorias() {
    // Usar delegación de eventos para botones dinámicos
    document.addEventListener('click', function (e) {
        if (e.target.matches('#category-pills button[data-cat]')) {
            const categoryPills = document.querySelectorAll('#category-pills button');

            // Remover active de todos
            categoryPills.forEach(p => {
                p.classList.remove('active', 'btn-primary');
                p.classList.add('btn-outline-secondary');
            });

            // Activar el seleccionado
            e.target.classList.add('active', 'btn-primary');
            e.target.classList.remove('btn-outline-secondary');

            const categoria = e.target.getAttribute('data-cat');
            filtrarPorCategoria(categoria);
        }
    });
}

/**
 * Filtrar productos por categoría
 */
function filtrarPorCategoria(categoria) {
    if (!categoria) {
        mostrarProductos(productos);
        return;
    }

    const productosFiltrados = productos.filter(producto =>
        producto.categoria_nombre === categoria
    );
    mostrarProductos(productosFiltrados);
}

/**
 * Quitar producto del carrito (botón -)
 */
function quitarDelCarrito(productoId) {
    const itemExistente = carrito.find(item => item.id === productoId);

    if (itemExistente) {
        if (itemExistente.cantidad > 1) {
            itemExistente.cantidad--;
        } else {
            // Si solo hay 1, eliminar completamente
            const index = carrito.findIndex(item => item.id === productoId);
            carrito.splice(index, 1);
        }
        actualizarCarrito();
        actualizarCantidadEnTarjeta(productoId);
    }
}

/**
 * Actualizar la cantidad mostrada en la tarjeta del producto
 */
function actualizarCantidadEnTarjeta(productoId) {
    const badge = document.getElementById(`qty-${productoId}`);
    if (badge) {
        const item = carrito.find(item => item.id === productoId);
        const cantidad = item ? item.cantidad : 0;
        badge.textContent = cantidad;
        badge.style.display = cantidad > 0 ? 'inline' : 'none';
    }
}
function agregarAlCarrito(productoId) {
    const producto = productos.find(p => p.id === productoId);
    if (!producto) return;

    if (producto.stock_actual <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin Stock',
            text: 'Este producto no tiene stock disponible'
        });
        return;
    }

    const itemExistente = carrito.find(item => item.id === productoId);

    if (itemExistente) {
        if (itemExistente.cantidad < producto.stock_actual) {
            itemExistente.cantidad++;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Insuficiente',
                text: `Solo hay ${producto.stock_actual} unidades disponibles`
            });
            return;
        }
    } else {
        const precioConDescuento = calcularPrecioConDescuento(producto);
        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio_original: parseFloat(producto.precio_venta),
            precio_actual: precioConDescuento,
            cantidad: 1,
            stock_disponible: producto.stock_actual,
            tiene_descuento: precioConDescuento < parseFloat(producto.precio_venta)
        });
    }

    actualizarCarrito();
    actualizarCantidadEnTarjeta(productoId);
}

/**
 * Actualizar visualización del carrito
 */
function actualizarCarrito() {
    const container = document.getElementById('cart-items-container');
    const btnPay = document.getElementById('btn-pay');

    if (carrito.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                <p>Carrito vacío</p>
                <small>Selecciona productos para agregar</small>
            </div>
        `;
        btnPay.disabled = true;
    } else {
        let html = '';
        totalCarrito = 0;
        descuentoTotal = 0;

        carrito.forEach((item, index) => {
            const subtotal = item.precio_actual * item.cantidad;
            const descuentoItem = (item.precio_original - item.precio_actual) * item.cantidad;

            totalCarrito += subtotal;
            descuentoTotal += descuentoItem;

            html += `
                <div class="cart-item bg-light p-3 rounded mb-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">${item.nombre}</h6>
                            ${item.tiene_descuento ? `
                                <small class="text-success"><i class="fas fa-tag"></i> Con descuento</small>
                            ` : ''}
                        </div>
                        <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="eliminarDelCarrito(${index})" style="width: 30px; height: 30px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
                            <span class="btn btn-outline-secondary disabled">${item.cantidad}</span>
                            <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
                        </div>
                        
                        <div class="text-end">
                            ${item.tiene_descuento ? `
                                <div class="text-decoration-line-through text-muted small">Q ${(item.precio_original * item.cantidad).toFixed(2)}</div>
                            ` : ''}
                            <div class="fw-bold">Q ${subtotal.toFixed(2)}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        btnPay.disabled = false;
    }

    // Actualizar totales
    document.getElementById('cart-subtotal-display').textContent = `Q ${(totalCarrito + descuentoTotal).toFixed(2)}`;
    document.getElementById('cart-discount-display').textContent = `- Q ${descuentoTotal.toFixed(2)}`;
    document.getElementById('cart-total').textContent = `Q ${totalCarrito.toFixed(2)}`;

    // Actualizar cantidades en las tarjetas
    productos.forEach(producto => {
        actualizarCantidadEnTarjeta(producto.id);
    });
}

/**
 * Cambiar cantidad de un item del carrito
 */
function cambiarCantidad(index, cambio) {
    const item = carrito[index];
    const nuevaCantidad = item.cantidad + cambio;

    if (nuevaCantidad <= 0) {
        eliminarDelCarrito(index);
        return;
    }

    if (nuevaCantidad > item.stock_disponible) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock Insuficiente',
            text: `Solo hay ${item.stock_disponible} unidades disponibles`
        });
        return;
    }

    item.cantidad = nuevaCantidad;
    actualizarCarrito();
}

/**
 * Eliminar item del carrito
 */
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

/**
 * Limpiar carrito completo
 */
function clearCart() {
    if (carrito.length === 0) return;

    Swal.fire({
        title: '¿Limpiar carrito?',
        text: 'Se eliminarán todos los productos del carrito',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            carrito = [];
            actualizarCarrito();
        }
    });
}

/**
 * Abrir modal de vencimientos
 */
function openExpirationModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalVencimientos'));
    cargarProductosVencimiento();
    modal.show();
}

/**
 * Cargar productos próximos a vencer
 */
function cargarProductosVencimiento() {
    const tbody = document.getElementById('vencimientos-table-body');
    const productosVencimiento = productos.filter(p => p.por_vencer);

    let html = '';
    productosVencimiento.forEach(producto => {
        const diasRestantes = calcularDiasRestantes(producto.fecha_vencimiento);
        const precioConDescuento = calcularPrecioConDescuento(producto);
        const porcentajeDescuento = Math.round((1 - precioConDescuento / producto.precio_venta) * 100);

        html += `
            <tr>
                <td class="ps-4">${producto.nombre}</td>
                <td>${producto.fecha_vencimiento}</td>
                <td>
                    <span class="badge ${diasRestantes <= 7 ? 'bg-danger' : diasRestantes <= 15 ? 'bg-warning' : 'bg-info'}">
                        ${diasRestantes} días
                    </span>
                </td>
                <td>Q ${parseFloat(producto.precio_venta).toFixed(2)}</td>
                <td>
                    <select class="form-select form-select-sm" onchange="aplicarDescuentoPersonalizado(${producto.id}, this.value)">
                        <option value="0">Sin descuento</option>
                        <option value="10" ${porcentajeDescuento === 10 ? 'selected' : ''}>10%</option>
                        <option value="20" ${porcentajeDescuento === 20 ? 'selected' : ''}>20%</option>
                        <option value="30" ${porcentajeDescuento === 30 ? 'selected' : ''}>30%</option>
                        <option value="50" ${porcentajeDescuento === 50 ? 'selected' : ''}>50%</option>
                    </select>
                </td>
                <td class="pe-4 text-end fw-bold">Q ${precioConDescuento.toFixed(2)}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html || '<tr><td colspan="6" class="text-center text-muted">No hay productos próximos a vencer</td></tr>';
}

/**
 * Calcular días restantes hasta vencimiento
 */
function calcularDiasRestantes(fechaVencimiento) {
    const hoy = new Date();
    const fecha = new Date(fechaVencimiento);
    return Math.ceil((fecha - hoy) / (1000 * 60 * 60 * 24));
}

/**
 * Verificar productos próximos a vencimiento
 */
function verificarProductosVencimiento() {
    const productosVencimiento = productos.filter(p => p.por_vencer);
    const badge = document.getElementById('expiration-badge');

    if (productosVencimiento.length > 0) {
        badge.textContent = productosVencimiento.length;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

/**
 * Abrir modal de pago
 */
function openPaymentModal() {
    if (carrito.length === 0) return;

    document.getElementById('modal-total-pagar').textContent = `Q ${totalCarrito.toFixed(2)}`;
    document.getElementById('input-pago-recibido').value = '';
    document.getElementById('lbl-cambio').textContent = 'Q 0.00';

    const modal = new bootstrap.Modal(document.getElementById('modalCobrar'));
    modal.show();
}

/**
 * Alternar sección de pago según método
 */
function togglePagoInput(metodo) {
    const seccionEfectivo = document.getElementById('seccion-efectivo');

    if (metodo === 'efectivo') {
        seccionEfectivo.style.display = 'block';
    } else {
        seccionEfectivo.style.display = 'none';
    }
}

/**
 * Calcular cambio
 */
function calcularCambio() {
    const recibido = parseFloat(document.getElementById('input-pago-recibido').value) || 0;
    const cambio = recibido - totalCarrito;

    document.getElementById('lbl-cambio').textContent = `Q ${Math.max(0, cambio).toFixed(2)}`;

    if (cambio < 0) {
        document.getElementById('lbl-cambio').className = 'fw-bold text-danger';
    } else {
        document.getElementById('lbl-cambio').className = 'fw-bold text-success';
    }
}

/**
 * Procesar venta confirmada
 */
async function procesarVentaConfirmada() {
    const metodoPago = document.querySelector('input[name="metodoPago"]:checked').value;
    const clienteId = document.getElementById('select-cliente-venta').value;
    let recibido = totalCarrito; // Valor por defecto

    if (metodoPago === 'efectivo') {
        recibido = parseFloat(document.getElementById('input-pago-recibido').value) || 0;
        if (recibido < totalCarrito) {
            Swal.fire({
                icon: 'error',
                title: 'Monto Insuficiente',
                text: 'El monto recibido es menor al total'
            });
            return;
        }
    }

    const ventaData = {
        cliente_id: clienteId,
        metodo_pago: metodoPago,
        total: totalCarrito,
        descuento_total: descuentoTotal,
        monto_recibido: recibido,
        items: carrito.map(item => ({
            producto_id: item.id,
            cantidad: item.cantidad,
            precio_unitario: item.precio_actual,
            subtotal: item.precio_actual * item.cantidad
        }))
    };

    try {
        const apiBaseUrl = getApiBaseUrl();
        const url = window.location.origin + apiBaseUrl + 'api/ventas.php';

        console.log('🔄 Procesando venta...', ventaData);
        console.log('📡 URL API:', url);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(ventaData)
        });

        console.log('📡 Respuesta HTTP:', response.status);

        const result = await response.json();
        console.log('📦 Resultado:', result);

        if (result.success) {
            // Generar PDF de la factura
            generarFacturaPDF(result);

            Swal.fire({
                icon: 'success',
                title: 'Venta Procesada',
                text: `Venta #${result.venta_id} registrada exitosamente`,
                timer: 2000
            });

            // Limpiar carrito y cerrar modal
            carrito = [];
            actualizarCarrito();
            bootstrap.Modal.getInstance(document.getElementById('modalCobrar')).hide();

            // Actualizar stock de productos
            cargarProductos();

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'Error al procesar la venta'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo procesar la venta'
        });
    }
}

/**
 * Guardar descuentos personalizados
 */
function guardarDescuentos() {
    // Implementar guardado de descuentos personalizados
    console.log('Guardar descuentos personalizados');
    bootstrap.Modal.getInstance(document.getElementById('modalVencimientos')).hide();
}

/**
 * Aplicar descuento personalizado
 */
function aplicarDescuentoPersonalizado(productoId, porcentaje) {
    // Implementar aplicación de descuento personalizado
    console.log(`Aplicar ${porcentaje}% de descuento al producto ${productoId}`);
}

/**
 * Generar PDF de factura
 */
function generarFacturaPDF(ventaData) {
    // Usar la nueva versión HTML que funciona mejor
    const apiBaseUrl = getApiBaseUrl();
    const facturaUrl = window.location.origin + apiBaseUrl + `api/generar_factura_html.php?venta_id=${ventaData.venta_id}`;

    // Abrir en nueva ventana
    window.open(facturaUrl, '_blank', 'width=500,height=700,scrollbars=yes');
}

/**
 * Mostrar vista previa de la factura
 */
function mostrarVistaPrevia(ventaData) {
    const fecha = new Date().toLocaleDateString('es-GT');
    const hora = new Date().toLocaleTimeString('es-GT');

    const contenidoFactura = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Factura #${ventaData.venta_id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; font-size: 14px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .info { margin: 20px 0; background: #f9f9f9; padding: 15px; border-radius: 5px; }
                .items { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .items th, .items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .items th { background-color: #f2f2f2; }
                .total { text-align: right; font-size: 16px; margin-top: 20px; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🏪 La Esquinita</h1>
                <p>Sistema de Punto de Venta</p>
                <h2>FACTURA #${String(ventaData.venta_id).padStart(6, '0')}</h2>
            </div>
            
            <div class="info">
                <p><strong>Fecha:</strong> ${fecha} ${hora}</p>
                <p><strong>Cliente:</strong> Público General</p>
                <p><strong>Método de Pago:</strong> ${ventaData.metodo_pago || 'Efectivo'}</p>
            </div>
            
            <table class="items">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${carrito.map(item => `
                        <tr>
                            <td>${item.nombre}</td>
                            <td>${item.cantidad}</td>
                            <td>Q ${item.precio_actual.toFixed(2)}</td>
                            <td>Q ${(item.precio_actual * item.cantidad).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            
            <div class="total">
                <p>Subtotal: Q ${(totalCarrito + descuentoTotal).toFixed(2)}</p>
                ${descuentoTotal > 0 ? `<p style="color: green;">Descuentos: - Q ${descuentoTotal.toFixed(2)}</p>` : ''}
                <p><strong style="font-size: 18px;">TOTAL: Q ${totalCarrito.toFixed(2)}</strong></p>
                ${ventaData.cambio > 0 ? `
                    <p>Recibido: Q ${ventaData.monto_recibido.toFixed(2)}</p>
                    <p>Cambio: Q ${ventaData.cambio.toFixed(2)}</p>
                ` : ''}
            </div>
            
            <div class="footer">
                <p><strong>¡Gracias por su compra!</strong></p>
                <p>La Esquinita - ${fecha}</p>
            </div>
            
            <script>
                // Auto-imprimir después de cargar
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                }
            </script>
        </body>
        </html>
    `;

    // Crear ventana para vista previa e impresión
    const ventana = window.open('', '_blank', 'width=800,height=600');
    ventana.document.write(contenidoFactura);
    ventana.document.close();
}
function mostrarProductosEjemplo() {
    productos = [
        {
            id: 1,
            nombre: 'Coca Cola 600ml',
            precio_venta: 8.00,
            stock_actual: 50,
            stock_minimo: 10,
            categoria_nombre: 'Bebidas',
            codigo_barras: '123456789',
            por_vencer: false
        },
        {
            id: 2,
            nombre: 'Pan Bimbo',
            precio_venta: 12.00,
            stock_actual: 5,
            stock_minimo: 10,
            categoria_nombre: 'Frescos',
            codigo_barras: '987654321',
            por_vencer: true,
            fecha_vencimiento: '2026-02-01'
        }
    ];
    mostrarProductos(productos);
}