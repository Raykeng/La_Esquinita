<?php
// modulos/clientes.php
// Módulo de gestión de clientes
?>

<div class="dashboard-main-body">
    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Gestión de Clientes</h4>
        <button class="btn btn-primary" onclick="abrirModalCliente()">
            <i class="fas fa-plus me-2"></i>Nuevo Cliente
        </button>
    </div>

    <!-- Estadísticas de clientes -->
    <div class="row gy-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h4 id="total-clientes-stat">0</h4>
                    <small>Total Clientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <h4 id="clientes-activos">0</h4>
                    <small>Clientes Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <h4 id="puntos-totales">0</h4>
                    <small>Puntos Totales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill fa-2x mb-2"></i>
                    <h4 id="ventas-totales">Q0</h4>
                    <small>Ventas Totales</small>
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
                        <input type="text" class="form-control" id="buscar-cliente" placeholder="Buscar por nombre, teléfono o email...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-secondary btn-sm" onclick="exportarClientes()">
                            <i class="fas fa-download me-1"></i>Exportar
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="cargarClientes()">
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
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Compras</th>
                            <th>Total Gastado</th>
                            <th>Puntos</th>
                            <th>Última Compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-clientes">
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando clientes...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    <span id="modal-titulo">Nuevo Cliente</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-cliente">
                    <input type="hidden" id="cliente-id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cliente-nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="cliente-telefono" placeholder="5555-5555">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="cliente-email" placeholder="cliente@ejemplo.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Puntos Acumulados</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="cliente-puntos" value="0" min="0">
                                    <span class="input-group-text">pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" id="cliente-direccion" rows="2" placeholder="Dirección completa del cliente"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial Cliente -->
<div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-history me-2"></i>
                    Historial de Compras - <span id="historial-cliente-nombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 id="historial-total-compras">0</h5>
                                <small>Total Compras</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 id="historial-total-gastado">Q0</h5>
                                <small>Total Gastado</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 id="historial-puntos">0</h5>
                                <small>Puntos Actuales</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 id="historial-promedio">Q0</h5>
                                <small>Promedio por Compra</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Método Pago</th>
                                <th>Items</th>
                                <th>Descuento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-historial">
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2">Cargando historial...</p>
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
let clientes = [];
let clienteSeleccionado = null;

// Inicializar módulo
document.addEventListener('DOMContentLoaded', function() {
    cargarClientes();
    configurarBusqueda();
});

// Cargar lista de clientes
async function cargarClientes() {
    try {
        const response = await fetch('api/clientes.php?action=listar');
        const data = await response.json();
        
        if (data.success) {
            clientes = data.clientes;
            mostrarClientes(clientes);
            actualizarEstadisticas();
        } else {
            console.error('Error al cargar clientes:', data.message);
            mostrarClientesEjemplo();
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarClientesEjemplo();
    }
}

// Mostrar clientes en la tabla
function mostrarClientes(clientesArray) {
    const tbody = document.getElementById('tabla-clientes');
    
    if (clientesArray.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                    <p>No hay clientes registrados</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    clientesArray.forEach(cliente => {
        const ultimaCompra = cliente.ultima_compra || 'Nunca';
        const totalGastado = cliente.total_gastado ? `Q${cliente.total_gastado.toFixed(2)}` : 'Q0.00';
        
        html += `
            <tr>
                <td>${cliente.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            ${cliente.nombre.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <strong>${cliente.nombre}</strong>
                            ${cliente.email ? `<br><small class="text-muted">${cliente.email}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    ${cliente.telefono ? `<i class="fas fa-phone text-muted me-1"></i>${cliente.telefono}` : '<span class="text-muted">Sin teléfono</span>'}
                </td>
                <td>
                    <span class="badge bg-info">${cliente.total_compras || 0} compras</span>
                </td>
                <td>
                    <strong class="text-success">${totalGastado}</strong>
                </td>
                <td>
                    <span class="badge bg-warning text-dark">${cliente.puntos_acumulados} pts</span>
                </td>
                <td>
                    <small>${ultimaCompra}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editarCliente(${cliente.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="verHistorial(${cliente.id})" title="Ver Historial">
                            <i class="fas fa-history"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="gestionarPuntos(${cliente.id})" title="Gestionar Puntos">
                            <i class="fas fa-star"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="eliminarCliente(${cliente.id})" title="Eliminar">
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
    const total = clientes.length;
    const activos = clientes.filter(c => c.total_compras > 0).length;
    const puntosTotales = clientes.reduce((sum, c) => sum + c.puntos_acumulados, 0);
    const ventasTotales = clientes.reduce((sum, c) => sum + (c.total_gastado || 0), 0);
    
    document.getElementById('total-clientes-stat').textContent = total;
    document.getElementById('clientes-activos').textContent = activos;
    document.getElementById('puntos-totales').textContent = puntosTotales.toLocaleString();
    document.getElementById('ventas-totales').textContent = `Q${ventasTotales.toFixed(2)}`;
}

// Configurar búsqueda
function configurarBusqueda() {
    document.getElementById('buscar-cliente').addEventListener('input', function() {
        const termino = this.value.toLowerCase();
        
        if (termino === '') {
            mostrarClientes(clientes);
            return;
        }
        
        const clientesFiltrados = clientes.filter(cliente => 
            cliente.nombre.toLowerCase().includes(termino) ||
            (cliente.telefono && cliente.telefono.includes(termino)) ||
            (cliente.email && cliente.email.toLowerCase().includes(termino))
        );
        
        mostrarClientes(clientesFiltrados);
    });
}

// Abrir modal para nuevo cliente
function abrirModalCliente(clienteId = null) {
    const modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    const form = document.getElementById('form-cliente');
    const titulo = document.getElementById('modal-titulo');
    
    // Limpiar formulario
    form.reset();
    document.getElementById('cliente-id').value = '';
    
    if (clienteId) {
        // Editar cliente existente
        const cliente = clientes.find(c => c.id === clienteId);
        if (cliente) {
            titulo.textContent = 'Editar Cliente';
            document.getElementById('cliente-id').value = cliente.id;
            document.getElementById('cliente-nombre').value = cliente.nombre;
            document.getElementById('cliente-telefono').value = cliente.telefono || '';
            document.getElementById('cliente-email').value = cliente.email || '';
            document.getElementById('cliente-direccion').value = cliente.direccion || '';
            document.getElementById('cliente-puntos').value = cliente.puntos_acumulados;
        }
    } else {
        // Nuevo cliente
        titulo.textContent = 'Nuevo Cliente';
        document.getElementById('cliente-puntos').value = 0;
    }
    
    modal.show();
}

// Guardar cliente
async function guardarCliente() {
    const clienteId = document.getElementById('cliente-id').value;
    const isEdit = !!clienteId;
    
    const clienteData = {
        nombre: document.getElementById('cliente-nombre').value.trim(),
        telefono: document.getElementById('cliente-telefono').value.trim(),
        email: document.getElementById('cliente-email').value.trim(),
        direccion: document.getElementById('cliente-direccion').value.trim(),
        puntos_acumulados: parseInt(document.getElementById('cliente-puntos').value) || 0
    };
    
    if (isEdit) {
        clienteData.id = parseInt(clienteId);
    }
    
    // Validaciones
    if (!clienteData.nombre) {
        Swal.fire('Error', 'El nombre del cliente es requerido', 'error');
        return;
    }
    
    try {
        const url = isEdit ? 'api/clientes.php?action=actualizar' : 'api/clientes.php?action=crear';
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(clienteData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', result.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            cargarClientes();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo guardar el cliente', 'error');
    }
}

// Editar cliente
function editarCliente(clienteId) {
    abrirModalCliente(clienteId);
}

// Ver historial de cliente
async function verHistorial(clienteId) {
    const cliente = clientes.find(c => c.id === clienteId);
    if (!cliente) return;
    
    const modal = new bootstrap.Modal(document.getElementById('modalHistorial'));
    document.getElementById('historial-cliente-nombre').textContent = cliente.nombre;
    
    // Actualizar estadísticas del cliente
    document.getElementById('historial-total-compras').textContent = cliente.total_compras || 0;
    document.getElementById('historial-total-gastado').textContent = `Q${(cliente.total_gastado || 0).toFixed(2)}`;
    document.getElementById('historial-puntos').textContent = cliente.puntos_acumulados;
    
    const promedio = cliente.total_compras > 0 ? (cliente.total_gastado / cliente.total_compras) : 0;
    document.getElementById('historial-promedio').textContent = `Q${promedio.toFixed(2)}`;
    
    modal.show();
    
    // Cargar historial
    try {
        const response = await fetch(`api/clientes.php?action=historial&id=${clienteId}&limite=20`);
        const data = await response.json();
        
        if (data.success) {
            mostrarHistorial(data.historial);
        } else {
            document.getElementById('tabla-historial').innerHTML = `
                <tr><td colspan="6" class="text-center text-muted">No hay historial disponible</td></tr>
            `;
        }
    } catch (error) {
        document.getElementById('tabla-historial').innerHTML = `
            <tr><td colspan="6" class="text-center text-danger">Error al cargar historial</td></tr>
        `;
    }
}

// Mostrar historial en tabla
function mostrarHistorial(historial) {
    const tbody = document.getElementById('tabla-historial');
    
    if (historial.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="6" class="text-center text-muted">No hay compras registradas</td></tr>
        `;
        return;
    }
    
    let html = '';
    historial.forEach(venta => {
        html += `
            <tr>
                <td>${venta.fecha_venta}</td>
                <td><strong class="text-success">Q${venta.total.toFixed(2)}</strong></td>
                <td>
                    <span class="badge bg-secondary">${venta.metodo_pago || 'Efectivo'}</span>
                </td>
                <td>${venta.items_comprados} items</td>
                <td>
                    ${venta.descuento_aplicado > 0 ? 
                        `<span class="text-warning">-Q${venta.descuento_aplicado.toFixed(2)}</span>` : 
                        '<span class="text-muted">Sin descuento</span>'
                    }
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="verDetalleVenta(${venta.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Gestionar puntos del cliente
function gestionarPuntos(clienteId) {
    const cliente = clientes.find(c => c.id === clienteId);
    if (!cliente) return;
    
    Swal.fire({
        title: `Gestionar Puntos - ${cliente.nombre}`,
        html: `
            <div class="text-start">
                <p><strong>Puntos actuales:</strong> ${cliente.puntos_acumulados}</p>
                <div class="mb-3">
                    <label class="form-label">Operación:</label>
                    <select class="form-select" id="operacion-puntos">
                        <option value="sumar">Sumar puntos</option>
                        <option value="restar">Restar puntos</option>
                        <option value="establecer">Establecer puntos</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" id="cantidad-puntos" min="0" value="0">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const operacion = document.getElementById('operacion-puntos').value;
            const cantidad = parseInt(document.getElementById('cantidad-puntos').value) || 0;
            
            if (cantidad <= 0) {
                Swal.showValidationMessage('La cantidad debe ser mayor a 0');
                return false;
            }
            
            return { operacion, cantidad };
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch('api/clientes.php?action=actualizar_puntos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        cliente_id: clienteId,
                        puntos: result.value.cantidad,
                        operacion: result.value.operacion
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success');
                    cargarClientes();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudieron actualizar los puntos', 'error');
            }
        }
    });
}

// Eliminar cliente
function eliminarCliente(clienteId) {
    const cliente = clientes.find(c => c.id === clienteId);
    if (!cliente) return;
    
    Swal.fire({
        title: '¿Eliminar cliente?',
        text: `Se eliminará a ${cliente.nombre}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`api/clientes.php?action=eliminar&id=${clienteId}`);
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire('Eliminado', data.message, 'success');
                    cargarClientes();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar el cliente', 'error');
            }
        }
    });
}

// Ver detalle de venta
function verDetalleVenta(ventaId) {
    // Esta función se implementará cuando tengamos el módulo de ventas completo
    Swal.fire('Info', 'Función de detalle de venta en desarrollo', 'info');
}

// Exportar clientes
function exportarClientes() {
    // Implementar exportación a CSV/Excel
    Swal.fire('Info', 'Función de exportación en desarrollo', 'info');
}

// Datos de ejemplo para desarrollo
function mostrarClientesEjemplo() {
    clientes = [
        {
            id: 1,
            nombre: 'María González',
            telefono: '5555-1234',
            email: 'maria@ejemplo.com',
            puntos_acumulados: 150,
            total_compras: 5,
            total_gastado: 250.50,
            ultima_compra: '25/01/2026'
        },
        {
            id: 2,
            nombre: 'Carlos Rodríguez',
            telefono: '5555-5678',
            email: 'carlos@ejemplo.com',
            puntos_acumulados: 75,
            total_compras: 3,
            total_gastado: 180.25,
            ultima_compra: '24/01/2026'
        }
    ];
    mostrarClientes(clientes);
    actualizarEstadisticas();
}
</script>