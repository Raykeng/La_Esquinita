<?php
// modulos/proveedores.php
// Módulo de gestión de proveedores
?>

<div class="dashboard-main-body">
    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Gestión de Proveedores</h4>
        <button class="btn btn-primary" onclick="abrirModalProveedor()">
            <i class="fas fa-plus me-2"></i>Nuevo Proveedor
        </button>
    </div>

    <!-- Estadísticas de proveedores -->
    <div class="row gy-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-2x mb-2"></i>
                    <h4 id="total-proveedores">0</h4>
                    <small>Total Proveedores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-2x mb-2"></i>
                    <h4 id="total-productos-prov">0</h4>
                    <small>Productos Totales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x mb-2"></i>
                    <h4 id="categorias-prov">0</h4>
                    <small>Categorías</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill fa-2x mb-2"></i>
                    <h4 id="valor-inventario">Q0</h4>
                    <small>Valor Inventario</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="buscar-proveedor" placeholder="Buscar por nombre, contacto o categoría...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <select class="form-select form-select-sm" id="filtro-categoria" style="max-width: 150px;">
                            <option value="">Todas las categorías</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" onclick="exportarProveedores()">
                            <i class="fas fa-download me-1"></i>Exportar
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="cargarProveedores()">
                            <i class="fas fa-sync me-1"></i>Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Proveedor</th>
                            <th>Contacto</th>
                            <th>Categoría</th>
                            <th>Productos</th>
                            <th>Valor Inventario</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-proveedores">
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando proveedores...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Proveedor -->
<div class="modal fade" id="modalProveedor" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-truck me-2"></i>
                    <span id="modal-titulo">Nuevo Proveedor</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-proveedor">
                    <input type="hidden" id="proveedor-id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="proveedor-nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Persona de Contacto</label>
                                <input type="text" class="form-control" id="proveedor-contacto" placeholder="Nombre del representante">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="proveedor-telefono" placeholder="2234-5678">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="proveedor-email" placeholder="contacto@proveedor.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Categoría Principal</label>
                                <select class="form-select" id="proveedor-categoria">
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
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" id="proveedor-direccion" rows="2" placeholder="Dirección completa del proveedor"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarProveedor()">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Productos del Proveedor -->
<div class="modal fade" id="modalProductosProveedor" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-boxes me-2"></i>
                    Productos - <span id="productos-proveedor-nombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 id="productos-total">0</h5>
                                <small>Total Productos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 id="productos-stock">0</h5>
                                <small>Stock Total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 id="productos-valor">Q0</h5>
                                <small>Valor Inventario</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 id="productos-margen">Q0</h5>
                                <small>Margen Promedio</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>P. Compra</th>
                                <th>P. Venta</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-productos-proveedor">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2">Cargando productos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let proveedores = [];
let proveedorSeleccionado = null;

// Inicializar módulo
document.addEventListener('DOMContentLoaded', function() {
    cargarProveedores();
    configurarFiltros();
});

// Cargar lista de proveedores
async function cargarProveedores() {
    try {
        const response = await fetch('api/proveedores.php?action=listar');
        const data = await response.json();
        
        if (data.success) {
            proveedores = data.proveedores;
            mostrarProveedores(proveedores);
            actualizarEstadisticas();
            actualizarFiltrosCategorias();
        } else {
            console.error('Error al cargar proveedores:', data.message);
            mostrarProveedoresEjemplo();
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarProveedoresEjemplo();
    }
}

// Mostrar proveedores en la tabla
function mostrarProveedores(proveedoresArray) {
    const tbody = document.getElementById('tabla-proveedores');
    
    if (proveedoresArray.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-truck fa-2x mb-2 opacity-50"></i>
                    <p>No hay proveedores registrados</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    proveedoresArray.forEach(proveedor => {
        const valorInventario = proveedor.valor_inventario ? `Q${proveedor.valor_inventario.toFixed(2)}` : 'Q0.00';
        const categoria = proveedor.categoria || 'Sin categoría';
        
        html += `
            <tr>
                <td>${proveedor.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            ${proveedor.nombre.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <strong>${proveedor.nombre}</strong>
                            ${proveedor.email ? `<br><small class="text-muted">${proveedor.email}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    ${proveedor.contacto ? `<strong>${proveedor.contacto}</strong><br>` : ''}
                    ${proveedor.telefono ? `<i class="fas fa-phone text-muted me-1"></i>${proveedor.telefono}` : '<span class="text-muted">Sin teléfono</span>'}
                </td>
                <td>
                    <span class="badge bg-secondary">${categoria}</span>
                </td>
                <td>
                    <span class="badge bg-info">${proveedor.total_productos} productos</span>
                </td>
                <td>
                    <strong class="text-success">${valorInventario}</strong>
                </td>
                <td>
                    <small>${proveedor.fecha_registro}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editarProveedor(${proveedor.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="verProductos(${proveedor.id})" title="Ver Productos">
                            <i class="fas fa-boxes"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="verEstadisticas(${proveedor.id})" title="Estadísticas">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="eliminarProveedor(${proveedor.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Actualizar estadísticas
function actualizarEstadisticas() {
    const total = proveedores.length;
    const totalProductos = proveedores.reduce((sum, p) => sum + p.total_productos, 0);
    const valorTotal = proveedores.reduce((sum, p) => sum + (p.valor_inventario || 0), 0);
    const categorias = [...new Set(proveedores.map(p => p.categoria).filter(c => c))].length;
    
    document.getElementById('total-proveedores').textContent = total;
    document.getElementById('total-productos-prov').textContent = totalProductos;
    document.getElementById('categorias-prov').textContent = categorias;
    document.getElementById('valor-inventario').textContent = `Q${valorTotal.toFixed(2)}`;
}

// Actualizar filtros de categorías
function actualizarFiltrosCategorias() {
    const select = document.getElementById('filtro-categoria');
    const categorias = [...new Set(proveedores.map(p => p.categoria).filter(c => c))];
    
    // Limpiar opciones existentes (excepto la primera)
    while (select.children.length > 1) {
        select.removeChild(select.lastChild);
    }
    
    // Agregar categorías
    categorias.forEach(categoria => {
        const option = document.createElement('option');
        option.value = categoria;
        option.textContent = categoria;
        select.appendChild(option);
    });
}

// Configurar filtros
function configurarFiltros() {
    // Búsqueda
    document.getElementById('buscar-proveedor').addEventListener('input', filtrarProveedores);
    
    // Filtro por categoría
    document.getElementById('filtro-categoria').addEventListener('change', filtrarProveedores);
}

// Filtrar proveedores
function filtrarProveedores() {
    const busqueda = document.getElementById('buscar-proveedor').value.toLowerCase();
    const categoriaFiltro = document.getElementById('filtro-categoria').value;
    
    let proveedoresFiltrados = proveedores.filter(proveedor => {
        const coincideBusqueda = !busqueda || 
            proveedor.nombre.toLowerCase().includes(busqueda) ||
            (proveedor.contacto && proveedor.contacto.toLowerCase().includes(busqueda)) ||
            (proveedor.telefono && proveedor.telefono.includes(busqueda)) ||
            (proveedor.email && proveedor.email.toLowerCase().includes(busqueda));
        
        const coincideCategoria = !categoriaFiltro || proveedor.categoria === categoriaFiltro;
        
        return coincideBusqueda && coincideCategoria;
    });
    
    mostrarProveedores(proveedoresFiltrados);
}

// Abrir modal para nuevo proveedor
function abrirModalProveedor(proveedorId = null) {
    const modal = new bootstrap.Modal(document.getElementById('modalProveedor'));
    const form = document.getElementById('form-proveedor');
    const titulo = document.getElementById('modal-titulo');
    
    // Limpiar formulario
    form.reset();
    document.getElementById('proveedor-id').value = '';
    
    if (proveedorId) {
        // Editar proveedor existente
        const proveedor = proveedores.find(p => p.id === proveedorId);
        if (proveedor) {
            titulo.textContent = 'Editar Proveedor';
            document.getElementById('proveedor-id').value = proveedor.id;
            document.getElementById('proveedor-nombre').value = proveedor.nombre;
            document.getElementById('proveedor-contacto').value = proveedor.contacto || '';
            document.getElementById('proveedor-telefono').value = proveedor.telefono || '';
            document.getElementById('proveedor-email').value = proveedor.email || '';
            document.getElementById('proveedor-direccion').value = proveedor.direccion || '';
            document.getElementById('proveedor-categoria').value = proveedor.categoria || '';
        }
    } else {
        // Nuevo proveedor
        titulo.textContent = 'Nuevo Proveedor';
    }
    
    modal.show();
}

// Guardar proveedor
async function guardarProveedor() {
    const proveedorId = document.getElementById('proveedor-id').value;
    const isEdit = !!proveedorId;
    
    const proveedorData = {
        nombre: document.getElementById('proveedor-nombre').value.trim(),
        contacto: document.getElementById('proveedor-contacto').value.trim(),
        telefono: document.getElementById('proveedor-telefono').value.trim(),
        email: document.getElementById('proveedor-email').value.trim(),
        direccion: document.getElementById('proveedor-direccion').value.trim(),
        categoria: document.getElementById('proveedor-categoria').value
    };
    
    if (isEdit) {
        proveedorData.id = parseInt(proveedorId);
    }
    
    // Validaciones
    if (!proveedorData.nombre) {
        Swal.fire('Error', 'El nombre del proveedor es requerido', 'error');
        return;
    }
    
    try {
        const url = isEdit ? 'api/proveedores.php?action=actualizar' : 'api/proveedores.php?action=crear';
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(proveedorData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', result.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalProveedor')).hide();
            cargarProveedores();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo guardar el proveedor', 'error');
    }
}

// Editar proveedor
function editarProveedor(proveedorId) {
    abrirModalProveedor(proveedorId);
}

// Ver productos del proveedor
async function verProductos(proveedorId) {
    const proveedor = proveedores.find(p => p.id === proveedorId);
    if (!proveedor) return;
    
    const modal = new bootstrap.Modal(document.getElementById('modalProductosProveedor'));
    document.getElementById('productos-proveedor-nombre').textContent = proveedor.nombre;
    
    modal.show();
    
    // Cargar estadísticas y productos
    try {
        const [estadisticasResponse, productosResponse] = await Promise.all([
            fetch(`api/proveedores.php?action=estadisticas&id=${proveedorId}`),
            fetch(`api/proveedores.php?action=productos&id=${proveedorId}&limite=50`)
        ]);
        
        const estadisticasData = await estadisticasResponse.json();
        const productosData = await productosResponse.json();
        
        if (estadisticasData.success) {
            const stats = estadisticasData.estadisticas;
            document.getElementById('productos-total').textContent = stats.total_productos;
            document.getElementById('productos-stock').textContent = stats.total_stock;
            document.getElementById('productos-valor').textContent = `Q${stats.valor_inventario.toFixed(2)}`;
            document.getElementById('productos-margen').textContent = `Q${stats.margen_promedio.toFixed(2)}`;
        }
        
        if (productosData.success) {
            mostrarProductosProveedor(productosData.productos);
        }
        
    } catch (error) {
        console.error('Error al cargar datos del proveedor:', error);
    }
}

// Mostrar productos del proveedor
function mostrarProductosProveedor(productos) {
    const tbody = document.getElementById('tabla-productos-proveedor');
    
    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="8" class="text-center text-muted">No hay productos registrados</td></tr>
        `;
        return;
    }
    
    let html = '';
    productos.forEach(producto => {
        const vencimiento = producto.fecha_vencimiento || 'Sin fecha';
        const categoria = producto.categoria || 'Sin categoría';
        
        html += `
            <tr>
                <td><code>${producto.codigo_barras || 'N/A'}</code></td>
                <td><strong>${producto.nombre}</strong></td>
                <td><span class="badge bg-secondary">${categoria}</span></td>
                <td>
                    <span class="badge ${producto.cantidad > 10 ? 'bg-success' : producto.cantidad > 0 ? 'bg-warning' : 'bg-danger'}">
                        ${producto.cantidad}
                    </span>
                </td>
                <td>Q${producto.precio_compra.toFixed(2)}</td>
                <td>Q${producto.precio_venta.toFixed(2)}</td>
                <td><small>${vencimiento}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editarProducto(${producto.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Ver estadísticas del proveedor
function verEstadisticas(proveedorId) {
    // Esta función se implementará cuando tengamos el módulo de reportes completo
    Swal.fire('Info', 'Función de estadísticas en desarrollo', 'info');
}

// Eliminar proveedor
function eliminarProveedor(proveedorId) {
    const proveedor = proveedores.find(p => p.id === proveedorId);
    if (!proveedor) return;
    
    Swal.fire({
        title: '¿Eliminar proveedor?',
        text: `Se eliminará a ${proveedor.nombre}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`api/proveedores.php?action=eliminar&id=${proveedorId}`);
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire('Eliminado', data.message, 'success');
                    cargarProveedores();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar el proveedor', 'error');
            }
        }
    });
}

// Editar producto (función placeholder)
function editarProducto(productoId) {
    // Esta función se implementará cuando tengamos el módulo de productos completo
    Swal.fire('Info', 'Función de edición de producto en desarrollo', 'info');
}

// Exportar proveedores
function exportarProveedores() {
    // Implementar exportación a CSV/Excel
    Swal.fire('Info', 'Función de exportación en desarrollo', 'info');
}

// Datos de ejemplo para desarrollo
function mostrarProveedoresEjemplo() {
    proveedores = [
        {
            id: 1,
            nombre: 'Distribuidora Central',
            contacto: 'Juan Pérez',
            telefono: '2234-5678',
            email: 'ventas@distcentral.com',
            categoria: 'Bebidas',
            total_productos: 25,
            valor_inventario: 15000.50,
            fecha_registro: '15/01/2026'
        },
        {
            id: 2,
            nombre: 'Abarrotes del Norte',
            contacto: 'María López',
            telefono: '2345-6789',
            email: 'info@abarrotesnorte.com',
            categoria: 'Abarrotes',
            total_productos: 18,
            valor_inventario: 8500.25,
            fecha_registro: '20/01/2026'
        }
    ];
    mostrarProveedores(proveedores);
    actualizarEstadisticas();
    actualizarFiltrosCategorias();
}
</script>