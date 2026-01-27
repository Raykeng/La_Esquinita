<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">📦 Gestión de Inventario</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <i class="fas fa-home text-secondary"></i>
                    Inicio
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Inventario</li>
        </ul>
    </div>

    <!-- Estadísticas del Inventario -->
    <div class="row gy-4 mb-24">
        <div class="col-xxl-3 col-sm-6">
            <div class="card shadow-none border bg-gradient-start-1 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Productos</p>
                            <h6 class="mb-0" id="total-productos">0</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                            <i class="fas fa-boxes text-white text-2xl mb-0"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Stock Bajo</p>
                            <h6 class="mb-0 text-danger" id="stock-bajo">0</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                            <i class="fas fa-exclamation-triangle text-white text-2xl mb-0"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Por Vencer</p>
                            <h6 class="mb-0 text-warning" id="por-vencer">0</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-warning rounded-circle d-flex justify-content-center align-items-center">
                            <i class="fas fa-clock text-white text-2xl mb-0"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Valor Total</p>
                            <h6 class="mb-0 text-success" id="valor-total">Q 0.00</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-success rounded-circle d-flex justify-content-center align-items-center">
                            <i class="fas fa-dollar-sign text-white text-2xl mb-0"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles y Filtros -->
    <div class="card">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <h6 class="text-lg fw-semibold mb-0">Lista de Productos</h6>
                    <span class="badge bg-primary-50 text-primary-600 radius-4 px-8 py-4" id="productos-count">0 productos</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-success btn-sm" onclick="abrirModalAgregarStock()">
                        <i class="fas fa-plus me-1"></i> Agregar Stock
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="abrirModalNuevoProducto()">
                        <i class="fas fa-box me-1"></i> Nuevo Producto
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="escanearCodigoBarras()">
                        <i class="fas fa-barcode me-1"></i> Escanear
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body p-24">
            <!-- Filtros -->
            <div class="row mb-20">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="buscar-producto" placeholder="🔍 Buscar por nombre o código...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-categoria">
                        <option value="">📂 Todas las categorías</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-stock">
                        <option value="">📊 Todos los stocks</option>
                        <option value="bajo">⚠️ Stock bajo</option>
                        <option value="normal">✅ Stock normal</option>
                        <option value="alto">📈 Stock alto</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>Precio Costo</th>
                            <th>Precio Venta</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-inventario">
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando inventario...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Stock -->
<div class="modal fade" id="modalAgregarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📦 Agregar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarStock">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select class="form-select" id="producto-stock" required>
                            <option value="">Seleccionar producto...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad a Agregar</label>
                        <input type="number" class="form-control" id="cantidad-agregar" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <select class="form-select" id="motivo-stock">
                            <option value="compra">Compra a proveedor</option>
                            <option value="ajuste">Ajuste de inventario</option>
                            <option value="devolucion">Devolución</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones-stock" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="procesarAgregarStock()">
                    <i class="fas fa-plus"></i> Agregar Stock
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Escanear Código de Barras -->
<div class="modal fade" id="modalEscanear" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📱 Escanear Código de Barras</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-barcode fa-4x text-primary mb-3"></i>
                    <p>Ingresa el código de barras manualmente o usa un lector:</p>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control form-control-lg text-center" 
                           id="codigo-barras-input" placeholder="Código de barras..." 
                           onkeypress="if(event.key==='Enter') buscarPorCodigoBarras()">
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" onclick="buscarPorCodigoBarras()">
                        <i class="fas fa-search"></i> Buscar Producto
                    </button>
                </div>
                <div id="resultado-escaneo" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let productosInventario = [];
let categorias = [];

// Inicializar inventario
document.addEventListener('DOMContentLoaded', function() {
    cargarInventario();
    cargarCategorias();
    configurarFiltros();
});

// Cargar inventario desde la API
async function cargarInventario() {
    try {
        const response = await fetch('api/productos.php');
        const data = await response.json();
        
        if (data.success) {
            productosInventario = data.data;
            mostrarInventario(productosInventario);
            actualizarEstadisticas();
            cargarProductosEnSelect();
        } else {
            console.error('Error al cargar inventario:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Mostrar inventario en la tabla
function mostrarInventario(productos) {
    const tbody = document.getElementById('tabla-inventario');
    
    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="mb-0">No hay productos en el inventario</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = productos.map(producto => {
        const stockBajo = producto.stock_actual <= producto.stock_minimo;
        const porVencer = producto.fecha_vencimiento && 
                         new Date(producto.fecha_vencimiento) <= new Date(Date.now() + 30*24*60*60*1000);
        
        let estadoBadge = '';
        if (stockBajo) {
            estadoBadge = '<span class="badge bg-danger">Stock Bajo</span>';
        } else if (porVencer) {
            estadoBadge = '<span class="badge bg-warning">Por Vencer</span>';
        } else {
            estadoBadge = '<span class="badge bg-success">Normal</span>';
        }
        
        return `
            <tr>
                <td><code>${producto.codigo_barras || 'N/A'}</code></td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-box text-primary me-2"></i>
                        <strong>${producto.nombre}</strong>
                    </div>
                </td>
                <td>${producto.categoria_nombre || 'Sin categoría'}</td>
                <td>
                    <span class="badge ${stockBajo ? 'bg-danger' : 'bg-primary'} fs-6">
                        ${producto.stock_actual}
                    </span>
                </td>
                <td>${producto.stock_minimo}</td>
                <td>Q ${parseFloat(producto.precio_costo || 0).toFixed(2)}</td>
                <td>Q ${parseFloat(producto.precio_venta).toFixed(2)}</td>
                <td>${producto.fecha_vencimiento ? new Date(producto.fecha_vencimiento).toLocaleDateString() : 'N/A'}</td>
                <td>${estadoBadge}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-success" onclick="agregarStockRapido(${producto.id})" title="Agregar Stock">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="editarProducto(${producto.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="verHistorial(${producto.id})" title="Historial">
                            <i class="fas fa-history"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    document.getElementById('productos-count').textContent = `${productos.length} productos`;
}

// Actualizar estadísticas
function actualizarEstadisticas() {
    const totalProductos = productosInventario.length;
    const stockBajo = productosInventario.filter(p => p.stock_actual <= p.stock_minimo).length;
    const porVencer = productosInventario.filter(p => {
        if (!p.fecha_vencimiento) return false;
        return new Date(p.fecha_vencimiento) <= new Date(Date.now() + 30*24*60*60*1000);
    }).length;
    const valorTotal = productosInventario.reduce((sum, p) => sum + (p.stock_actual * p.precio_costo), 0);
    
    document.getElementById('total-productos').textContent = totalProductos;
    document.getElementById('stock-bajo').textContent = stockBajo;
    document.getElementById('por-vencer').textContent = porVencer;
    document.getElementById('valor-total').textContent = `Q ${valorTotal.toFixed(2)}`;
}

// Cargar categorías
async function cargarCategorias() {
    try {
        const response = await fetch('api/categorias.php');
        const data = await response.json();
        
        if (data.success) {
            categorias = data.data;
            const select = document.getElementById('filtro-categoria');
            select.innerHTML = '<option value="">📂 Todas las categorías</option>' +
                categorias.map(cat => `<option value="${cat.nombre}">${cat.nombre}</option>`).join('');
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
    }
}

// Configurar filtros
function configurarFiltros() {
    document.getElementById('buscar-producto').addEventListener('input', filtrarInventario);
    document.getElementById('filtro-categoria').addEventListener('change', filtrarInventario);
    document.getElementById('filtro-stock').addEventListener('change', filtrarInventario);
}

// Filtrar inventario
function filtrarInventario() {
    const busqueda = document.getElementById('buscar-producto').value.toLowerCase();
    const categoria = document.getElementById('filtro-categoria').value;
    const stock = document.getElementById('filtro-stock').value;
    
    let productosFiltrados = productosInventario.filter(producto => {
        const coincideBusqueda = !busqueda || 
            producto.nombre.toLowerCase().includes(busqueda) ||
            (producto.codigo_barras && producto.codigo_barras.includes(busqueda));
        
        const coincideCategoria = !categoria || producto.categoria_nombre === categoria;
        
        let coincideStock = true;
        if (stock === 'bajo') {
            coincideStock = producto.stock_actual <= producto.stock_minimo;
        } else if (stock === 'normal') {
            coincideStock = producto.stock_actual > producto.stock_minimo && producto.stock_actual < producto.stock_minimo * 3;
        } else if (stock === 'alto') {
            coincideStock = producto.stock_actual >= producto.stock_minimo * 3;
        }
        
        return coincideBusqueda && coincideCategoria && coincideStock;
    });
    
    mostrarInventario(productosFiltrados);
}

// Limpiar filtros
function limpiarFiltros() {
    document.getElementById('buscar-producto').value = '';
    document.getElementById('filtro-categoria').value = '';
    document.getElementById('filtro-stock').value = '';
    mostrarInventario(productosInventario);
}

// Abrir modal agregar stock
function abrirModalAgregarStock() {
    const modal = new bootstrap.Modal(document.getElementById('modalAgregarStock'));
    modal.show();
}

// Cargar productos en select
function cargarProductosEnSelect() {
    const select = document.getElementById('producto-stock');
    select.innerHTML = '<option value="">Seleccionar producto...</option>' +
        productosInventario.map(p => `<option value="${p.id}">${p.nombre} (Stock: ${p.stock_actual})</option>`).join('');
}

// Procesar agregar stock
async function procesarAgregarStock() {
    const productoId = document.getElementById('producto-stock').value;
    const cantidad = parseInt(document.getElementById('cantidad-agregar').value);
    const motivo = document.getElementById('motivo-stock').value;
    const observaciones = document.getElementById('observaciones-stock').value;
    
    if (!productoId || !cantidad) {
        Swal.fire('Error', 'Complete todos los campos requeridos', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/inventario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accion: 'agregar_stock',
                producto_id: productoId,
                cantidad: cantidad,
                motivo: motivo,
                observaciones: observaciones
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', 'Stock agregado correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalAgregarStock')).hide();
            cargarInventario(); // Recargar inventario
            
            // Limpiar formulario
            document.getElementById('formAgregarStock').reset();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo agregar el stock', 'error');
    }
}

// Escanear código de barras
function escanearCodigoBarras() {
    const modal = new bootstrap.Modal(document.getElementById('modalEscanear'));
    modal.show();
    
    // Focus en el input para facilitar el escaneo
    setTimeout(() => {
        document.getElementById('codigo-barras-input').focus();
    }, 500);
}

// Buscar por código de barras
function buscarPorCodigoBarras() {
    const codigo = document.getElementById('codigo-barras-input').value.trim();
    
    if (!codigo) {
        Swal.fire('Error', 'Ingrese un código de barras', 'error');
        return;
    }
    
    const producto = productosInventario.find(p => p.codigo_barras === codigo);
    const resultado = document.getElementById('resultado-escaneo');
    
    if (producto) {
        resultado.innerHTML = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle"></i> Producto Encontrado</h6>
                <p><strong>${producto.nombre}</strong></p>
                <p>Stock actual: <span class="badge bg-primary">${producto.stock_actual}</span></p>
                <p>Precio: Q ${parseFloat(producto.precio_venta).toFixed(2)}</p>
                <button class="btn btn-sm btn-success" onclick="agregarStockRapido(${producto.id})">
                    <i class="fas fa-plus"></i> Agregar Stock
                </button>
            </div>
        `;
    } else {
        resultado.innerHTML = `
            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle"></i> Producto No Encontrado</h6>
                <p>No se encontró ningún producto con el código: <code>${codigo}</code></p>
                <button class="btn btn-sm btn-primary" onclick="abrirModalNuevoProducto('${codigo}')">
                    <i class="fas fa-plus"></i> Crear Producto
                </button>
            </div>
        `;
    }
}

// Agregar stock rápido
function agregarStockRapido(productoId) {
    Swal.fire({
        title: 'Agregar Stock Rápido',
        input: 'number',
        inputLabel: 'Cantidad a agregar:',
        inputPlaceholder: 'Ej: 10',
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed && result.value) {
            try {
                const response = await fetch('api/inventario.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        accion: 'agregar_stock',
                        producto_id: productoId,
                        cantidad: parseInt(result.value),
                        motivo: 'ajuste',
                        observaciones: 'Ajuste rápido desde inventario'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire('Éxito', 'Stock agregado correctamente', 'success');
                    cargarInventario();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo agregar el stock', 'error');
            }
        }
    });
}

// Placeholder functions
function abrirModalNuevoProducto(codigoBarras = '') {
    // Crear el modal dinámicamente
    const modalHTML = `
        <div class="modal fade" id="modalNuevoProducto" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">📦 Nuevo Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevoProducto">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control" id="nombre-producto" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Código de Barras</label>
                                        <input type="text" class="form-control" id="codigo-barras-producto" value="${codigoBarras}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Categoría *</label>
                                        <select class="form-select" id="categoria-producto" required>
                                            <option value="">Seleccionar categoría...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Proveedor</label>
                                        <div class="input-group">
                                            <select class="form-select" id="proveedor-producto">
                                                <option value="">Seleccionar proveedor...</option>
                                            </select>
                                            <button class="btn btn-outline-primary" type="button" onclick="abrirModalNuevoProveedor()">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Precio Costo *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Q</span>
                                            <input type="number" class="form-control" id="precio-costo" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Precio Venta *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Q</span>
                                            <input type="number" class="form-control" id="precio-venta" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Margen %</label>
                                        <input type="number" class="form-control" id="margen-ganancia" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Stock Inicial *</label>
                                        <input type="number" class="form-control" id="stock-inicial" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Stock Mínimo *</label>
                                        <input type="number" class="form-control" id="stock-minimo" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de Vencimiento</label>
                                        <input type="date" class="form-control" id="fecha-vencimiento">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion-producto" rows="2"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="producto-activo" checked>
                                        <label class="form-check-label" for="producto-activo">
                                            Producto activo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="aplicar-descuento">
                                        <label class="form-check-label" for="aplicar-descuento">
                                            Aplicar descuento por vencimiento
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarNuevoProducto()">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('modalNuevoProducto');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Cargar datos para los selects
    cargarCategoriasEnModal();
    cargarProveedoresEnModal();
    
    // Configurar cálculo automático de margen
    configurarCalculoMargen();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalNuevoProducto'));
    modal.show();
}

// Cargar categorías en el modal
async function cargarCategoriasEnModal() {
    try {
        const response = await fetch('api/categorias.php');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('categoria-producto');
            select.innerHTML = '<option value="">Seleccionar categoría...</option>' +
                data.data.map(cat => `<option value="${cat.id}">${cat.nombre}</option>`).join('');
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
    }
}

// Cargar proveedores en el modal
async function cargarProveedoresEnModal() {
    try {
        const response = await fetch('api/proveedores.php');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('proveedor-producto');
            select.innerHTML = '<option value="">Seleccionar proveedor...</option>' +
                data.data.map(prov => `<option value="${prov.id}">${prov.nombre}</option>`).join('');
        }
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
    }
}

// Configurar cálculo automático de margen
function configurarCalculoMargen() {
    const precioCosto = document.getElementById('precio-costo');
    const precioVenta = document.getElementById('precio-venta');
    const margen = document.getElementById('margen-ganancia');
    
    function calcularMargen() {
        const costo = parseFloat(precioCosto.value) || 0;
        const venta = parseFloat(precioVenta.value) || 0;
        
        if (costo > 0 && venta > 0) {
            const margenPorcentaje = ((venta - costo) / costo * 100).toFixed(2);
            margen.value = margenPorcentaje;
        } else {
            margen.value = '';
        }
    }
    
    precioCosto.addEventListener('input', calcularMargen);
    precioVenta.addEventListener('input', calcularMargen);
}

// Guardar nuevo producto
async function guardarNuevoProducto() {
    const form = document.getElementById('formNuevoProducto');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Recopilar datos
    const datosProducto = {
        nombre: document.getElementById('nombre-producto').value.trim(),
        codigo_barras: document.getElementById('codigo-barras-producto').value.trim(),
        categoria_id: document.getElementById('categoria-producto').value,
        proveedor_id: document.getElementById('proveedor-producto').value || null,
        precio_costo: parseFloat(document.getElementById('precio-costo').value),
        precio_venta: parseFloat(document.getElementById('precio-venta').value),
        stock_inicial: parseInt(document.getElementById('stock-inicial').value),
        stock_minimo: parseInt(document.getElementById('stock-minimo').value),
        fecha_vencimiento: document.getElementById('fecha-vencimiento').value || null,
        descripcion: document.getElementById('descripcion-producto').value.trim(),
        activo: document.getElementById('producto-activo').checked,
        aplicar_descuento: document.getElementById('aplicar-descuento').checked
    };
    
    // Validaciones adicionales
    if (datosProducto.precio_venta <= datosProducto.precio_costo) {
        Swal.fire('Error', 'El precio de venta debe ser mayor al precio de costo', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/productos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accion: 'crear',
                ...datosProducto
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', 'Producto creado correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoProducto')).hide();
            cargarInventario(); // Recargar inventario
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo crear el producto', 'error');
        console.error('Error:', error);
    }
}

// Abrir modal para crear nuevo proveedor desde inventario
function abrirModalNuevoProveedor() {
    const modalHTML = `
        <div class="modal fade" id="modalNuevoProveedorInventario" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-truck me-2"></i>Nuevo Proveedor
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevoProveedorInventario">
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Empresa *</label>
                                <input type="text" class="form-control" id="nuevo-proveedor-nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Persona de Contacto</label>
                                <input type="text" class="form-control" id="nuevo-proveedor-contacto">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="nuevo-proveedor-telefono">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="nuevo-proveedor-email">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categoría Principal</label>
                                <select class="form-select" id="nuevo-proveedor-categoria">
                                    <option value="">Seleccionar categoría...</option>
                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Abarrotes">Abarrotes</option>
                                    <option value="Frescos">Frescos</option>
                                    <option value="Enlatados">Enlatados</option>
                                    <option value="Limpieza">Limpieza</option>
                                    <option value="Panadería">Panadería</option>
                                    <option value="Lácteos">Lácteos</option>
                                    <option value="Carnes">Carnes</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarNuevoProveedorInventario()">
                            <i class="fas fa-save me-2"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('modalNuevoProveedorInventario');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalNuevoProveedorInventario'));
    modal.show();
}

// Guardar nuevo proveedor desde inventario
async function guardarNuevoProveedorInventario() {
    const form = document.getElementById('formNuevoProveedorInventario');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const datosProveedor = {
        nombre: document.getElementById('nuevo-proveedor-nombre').value.trim(),
        contacto: document.getElementById('nuevo-proveedor-contacto').value.trim(),
        telefono: document.getElementById('nuevo-proveedor-telefono').value.trim(),
        email: document.getElementById('nuevo-proveedor-email').value.trim(),
        categoria: document.getElementById('nuevo-proveedor-categoria').value
    };
    
    try {
        const response = await fetch('api/proveedores.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accion: 'crear',
                ...datosProveedor
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', 'Proveedor creado correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoProveedorInventario')).hide();
            
            // Recargar proveedores en el select
            cargarProveedoresEnModal();
            
            // Seleccionar el nuevo proveedor
            setTimeout(() => {
                document.getElementById('proveedor-producto').value = result.proveedor_id;
            }, 500);
            
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo crear el proveedor', 'error');
        console.error('Error:', error);
    }
}

function editarProducto(id) {
    Swal.fire('Info', 'Función de editar producto en desarrollo', 'info');
}

function verHistorial(id) {
    Swal.fire('Info', 'Función de historial en desarrollo', 'info');
}
</script>