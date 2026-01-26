/**
 * CLIENTES.JS - Gestión de Clientes La Esquinita
 * Autor: Marlon
 * Funciones para el módulo de clientes
 */

let clientes = [];

// Inicializar módulo de clientes
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('tabla-clientes')) {
        inicializarClientes();
    }
});

/**
 * Inicializar módulo de clientes
 */
function inicializarClientes() {
    cargarClientes();
    configurarBusquedaClientes();
}

/**
 * Cargar lista de clientes
 */
async function cargarClientes() {
    try {
        const response = await fetch('api/clientes.php?action=listar');
        const data = await response.json();
        
        if (data.success) {
            clientes = data.clientes;
            mostrarClientes(clientes);
        } else {
            console.error('Error al cargar clientes:', data.message);
            mostrarClientesEjemplo();
        }
    } catch (error) {
        console.error('Error en la conexión:', error);
        mostrarClientesEjemplo();
    }
}

/**
 * Mostrar clientes en la tabla
 */
function mostrarClientes(clientesArray) {
    const tbody = document.getElementById('tabla-clientes');
    const totalElement = document.getElementById('total-clientes');
    
    if (totalElement) {
        totalElement.textContent = clientesArray.length;
    }
    
    if (clientesArray.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                    <p>No hay clientes registrados</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    clientesArray.forEach(cliente => {
        html += `
            <tr>
                <td>${cliente.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            ${cliente.nombre.charAt(0).toUpperCase()}
                        </div>
                        ${cliente.nombre}
                    </div>
                </td>
                <td>${cliente.telefono || 'No registrado'}</td>
                <td>
                    <span class="badge bg-success">${cliente.puntos_acumulados || 0} pts</span>
                </td>
                <td>${cliente.ultima_compra || 'Nunca'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editarCliente(${cliente.id})" title="Editar">
                            <i class="fas fa-edit"></i>
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

/**
 * Configurar búsqueda de clientes
 */
function configurarBusquedaClientes() {
    const searchInput = document.getElementById('buscar-cliente');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const termino = this.value.toLowerCase();
            const clientesFiltrados = clientes.filter(cliente => 
                cliente.nombre.toLowerCase().includes(termino) ||
                (cliente.telefono && cliente.telefono.includes(termino))
            );
            mostrarClientes(clientesFiltrados);
        });
    }
}

/**
 * Abrir modal para nuevo cliente
 */
function abrirModalCliente(clienteId = null) {
    const modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    const form = document.getElementById('form-cliente');
    
    // Limpiar formulario
    form.reset();
    document.getElementById('cliente-id').value = '';
    
    if (clienteId) {
        // Cargar datos del cliente para editar
        const cliente = clientes.find(c => c.id === clienteId);
        if (cliente) {
            document.getElementById('cliente-id').value = cliente.id;
            document.getElementById('cliente-nombre').value = cliente.nombre;
            document.getElementById('cliente-telefono').value = cliente.telefono || '';
            document.getElementById('cliente-puntos').value = cliente.puntos_acumulados || 0;
        }
    }
    
    modal.show();
}

/**
 * Guardar cliente (crear o actualizar)
 */
async function guardarCliente() {
    const form = document.getElementById('form-cliente');
    const formData = new FormData(form);
    
    const clienteData = {
        id: document.getElementById('cliente-id').value,
        nombre: document.getElementById('cliente-nombre').value.trim(),
        telefono: document.getElementById('cliente-telefono').value.trim(),
        puntos_acumulados: parseInt(document.getElementById('cliente-puntos').value) || 0
    };
    
    // Validaciones
    if (!clienteData.nombre) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre del cliente es requerido'
        });
        return;
    }
    
    try {
        const url = clienteData.id ? 'api/clientes.php?action=actualizar' : 'api/clientes.php?action=crear';
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(clienteData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: clienteData.id ? 'Cliente actualizado correctamente' : 'Cliente creado correctamente',
                timer: 1500
            });
            
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            cargarClientes();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'Error al guardar el cliente'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo guardar el cliente'
        });
    }
}

/**
 * Editar cliente
 */
function editarCliente(clienteId) {
    abrirModalCliente(clienteId);
}

/**
 * Eliminar cliente
 */
function eliminarCliente(clienteId) {
    const cliente = clientes.find(c => c.id === clienteId);
    if (!cliente) return;
    
    Swal.fire({
        title: '¿Eliminar cliente?',
        text: `Se eliminará a ${cliente.nombre}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`api/clientes.php?action=eliminar&id=${clienteId}`, {
                    method: 'DELETE'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'Cliente eliminado correctamente',
                        timer: 1500
                    });
                    cargarClientes();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al eliminar el cliente'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo eliminar el cliente'
                });
            }
        }
    });
}

/**
 * Mostrar clientes de ejemplo si falla la conexión
 */
function mostrarClientesEjemplo() {
    clientes = [
        {
            id: 1,
            nombre: 'Cliente Frecuente',
            telefono: '12345678',
            puntos_acumulados: 150,
            ultima_compra: '2026-01-25'
        },
        {
            id: 2,
            nombre: 'María González',
            telefono: '87654321',
            puntos_acumulados: 75,
            ultima_compra: '2026-01-20'
        }
    ];
    mostrarClientes(clientes);
}