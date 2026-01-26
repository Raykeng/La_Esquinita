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

// Inicializar POS cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('pos-grid')) {
        inicializarPOS();
    }
});

/**
 * Inicializar el sistema POS
 */
function inicializarPOS() {
    cargarProductos();
    configurarBusqueda();
    configurarFiltrosCategorias();
    actualizarCarrito();
    verificarProductosVencimiento();
}

/**
 * Cargar productos desde la API
 */
async function cargarProductos() {
    try {
        const response = await fetch('api/productos.php?action=listar');
        const data = await response.json();
        
        if (data.success) {
            productos = data.productos;
            mostrarProductos(productos);
        } else {
            console.error('Error al cargar productos:', data.message);
        }
    } catch (error) {
        console.error('Error en la conexión:', error);
        // Mostrar productos de ejemplo si falla la conexión
        mostrarProductosEjemplo();
    }
}

/**
 * Mostrar productos en el grid
 */
function mostrarProductos(productosArray) {
    const grid = document.getElementById('pos-grid');
    grid.innerHTML = '';

    productosArray.forEach(producto => {
        const precioConDescuento = calcularPrecioConDescuento(producto);
        const tieneDescuento = precioConDescuento < producto.precio_venta;
        
        const card = document.createElement('div');
        card.className = 'col';
        card.innerHTML = `
            <div class="product-card shadow-sm position-relative" onclick="agregarAlCarrito(${producto.id})">
                ${tieneDescuento ? '<span class="product-badge badge bg-warning">¡Oferta!</span>' : ''}
                ${producto.stock_actual <= producto.stock_minimo ? '<span class="product-badge badge bg-danger" style="top: 8px; right: 8px;">Stock Bajo</span>' : ''}
                
                <div class="text-center mb-3">
                    <i class="fas fa-box-open text-primary" style="font-size: 2rem;"></i>
                </div>
                
                <h6 class="fw-bold mb-2 text-truncate" title="${producto.nombre}">${producto.nombre}</h6>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">${producto.categoria_nombre || 'Sin categoría'}</small>
                    <small class="text-muted">Stock: ${producto.stock_actual}</small>
                </div>
                
                <div class="price-section">
                    ${tieneDescuento ? `
                        <div class="text-decoration-line-through text-muted small">Q ${parseFloat(producto.precio_venta).toFixed(2)}</div>
                        <div class="product-price text-success">Q ${precioConDescuento.toFixed(2)}</div>
                    ` : `
                        <div class="product-price">Q ${parseFloat(producto.precio_venta).toFixed(2)}</div>
                    `}
                </div>
                
                ${producto.por_vencer ? '<small class="text-warning"><i class="fas fa-clock"></i> Próximo a vencer</small>' : ''}
            </div>
        `;
        grid.appendChild(card);
    });
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
        searchInput.addEventListener('input', function() {
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
    const categoryPills = document.querySelectorAll('#category-pills button');
    categoryPills.forEach(pill => {
        pill.addEventListener('click', function() {
            // Remover active de todos
            categoryPills.forEach(p => {
                p.classList.remove('active', 'btn-primary');
                p.classList.add('btn-outline-secondary');
            });
            
            // Activar el seleccionado
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');
            
            const categoria = this.getAttribute('data-cat');
            filtrarPorCategoria(categoria);
        });
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
 * Agregar producto al carrito
 */
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
    
    if (metodoPago === 'efectivo') {
        const recibido = parseFloat(document.getElementById('input-pago-recibido').value) || 0;
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
        items: carrito.map(item => ({
            producto_id: item.id,
            cantidad: item.cantidad,
            precio_unitario: item.precio_actual,
            subtotal: item.precio_actual * item.cantidad
        }))
    };
    
    try {
        const response = await fetch('api/ventas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(ventaData)
        });
        
        const result = await response.json();
        
        if (result.success) {
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
 * Mostrar productos de ejemplo si falla la conexión
 */
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