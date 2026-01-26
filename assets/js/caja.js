/**
 * CAJA.JS - Cierre de Caja La Esquinita
 * Autor: Marlon
 * Funciones para el módulo de cierre de caja
 */

let ventasDelTurno = [];
let ingresosAdicionales = [];
let egresos = [];

// Inicializar módulo de caja
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('total-ventas')) {
        inicializarCaja();
    }
});

/**
 * Inicializar módulo de caja
 */
function inicializarCaja() {
    cargarDatosTurno();
    cargarHistorialCierres();
}

/**
 * Cargar datos del turno actual
 */
async function cargarDatosTurno() {
    try {
        const response = await fetch('api/caja.php?action=turno_actual');
        const data = await response.json();
        
        if (data.success) {
            actualizarResumenTurno(data.resumen);
            ventasDelTurno = data.ventas || [];
            ingresosAdicionales = data.ingresos || [];
            egresos = data.egresos || [];
        } else {
            console.error('Error al cargar datos del turno:', data.message);
            mostrarDatosEjemplo();
        }
    } catch (error) {
        console.error('Error en la conexión:', error);
        mostrarDatosEjemplo();
    }
    
    actualizarListaIngresos();
    actualizarListaEgresos();
}

/**
 * Actualizar resumen del turno
 */
function actualizarResumenTurno(resumen) {
    document.getElementById('total-ventas').textContent = `Q ${(resumen.total_ventas || 0).toFixed(2)}`;
    document.getElementById('total-efectivo').textContent = `Q ${(resumen.total_efectivo || 0).toFixed(2)}`;
    document.getElementById('total-tarjeta').textContent = `Q ${(resumen.total_tarjeta || 0).toFixed(2)}`;
    document.getElementById('total-vales').textContent = `Q ${(resumen.total_vales || 0).toFixed(2)}`;
}

/**
 * Actualizar lista de ingresos adicionales
 */
function actualizarListaIngresos() {
    const container = document.getElementById('lista-ingresos');
    const totalElement = document.getElementById('total-ingresos');
    
    if (ingresosAdicionales.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-plus-circle fa-2x mb-2 opacity-50"></i>
                <p class="mb-0">No hay ingresos adicionales</p>
            </div>
        `;
    } else {
        let html = '';
        let total = 0;
        
        ingresosAdicionales.forEach((ingreso, index) => {
            total += parseFloat(ingreso.monto);
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <div>
                        <div class="fw-bold">${ingreso.concepto}</div>
                        <small class="text-muted">${ingreso.fecha}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-success">Q ${parseFloat(ingreso.monto).toFixed(2)}</span>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarIngreso(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        totalElement.textContent = `Q ${total.toFixed(2)}`;
    }
}

/**
 * Actualizar lista de egresos
 */
function actualizarListaEgresos() {
    const container = document.getElementById('lista-egresos');
    const totalElement = document.getElementById('total-egresos');
    
    if (egresos.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-minus-circle fa-2x mb-2 opacity-50"></i>
                <p class="mb-0">No hay egresos registrados</p>
            </div>
        `;
    } else {
        let html = '';
        let total = 0;
        
        egresos.forEach((egreso, index) => {
            total += parseFloat(egreso.monto);
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <div>
                        <div class="fw-bold">${egreso.concepto}</div>
                        <small class="text-muted">${egreso.fecha}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-danger">Q ${parseFloat(egreso.monto).toFixed(2)}</span>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarEgreso(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        totalElement.textContent = `Q ${total.toFixed(2)}`;
    }
}

/**
 * Agregar ingreso adicional
 */
function agregarIngreso() {
    Swal.fire({
        title: 'Agregar Ingreso',
        html: `
            <div class="mb-3">
                <label class="form-label">Concepto</label>
                <input type="text" id="ingreso-concepto" class="form-control" placeholder="Ej: Venta de producto especial">
            </div>
            <div class="mb-3">
                <label class="form-label">Monto (Q)</label>
                <input type="number" id="ingreso-monto" class="form-control" placeholder="0.00" step="0.01" min="0">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const concepto = document.getElementById('ingreso-concepto').value.trim();
            const monto = parseFloat(document.getElementById('ingreso-monto').value);
            
            if (!concepto) {
                Swal.showValidationMessage('El concepto es requerido');
                return false;
            }
            
            if (!monto || monto <= 0) {
                Swal.showValidationMessage('El monto debe ser mayor a 0');
                return false;
            }
            
            return { concepto, monto };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevoIngreso = {
                concepto: result.value.concepto,
                monto: result.value.monto,
                fecha: new Date().toLocaleString('es-GT'),
                usuario: 'Marlon'
            };
            
            ingresosAdicionales.push(nuevoIngreso);
            actualizarListaIngresos();
            
            // Guardar en base de datos
            guardarMovimiento('ingreso', nuevoIngreso);
        }
    });
}

/**
 * Agregar egreso
 */
function agregarEgreso() {
    Swal.fire({
        title: 'Agregar Egreso',
        html: `
            <div class="mb-3">
                <label class="form-label">Concepto</label>
                <input type="text" id="egreso-concepto" class="form-control" placeholder="Ej: Compra de suministros">
            </div>
            <div class="mb-3">
                <label class="form-label">Monto (Q)</label>
                <input type="number" id="egreso-monto" class="form-control" placeholder="0.00" step="0.01" min="0">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const concepto = document.getElementById('egreso-concepto').value.trim();
            const monto = parseFloat(document.getElementById('egreso-monto').value);
            
            if (!concepto) {
                Swal.showValidationMessage('El concepto es requerido');
                return false;
            }
            
            if (!monto || monto <= 0) {
                Swal.showValidationMessage('El monto debe ser mayor a 0');
                return false;
            }
            
            return { concepto, monto };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevoEgreso = {
                concepto: result.value.concepto,
                monto: result.value.monto,
                fecha: new Date().toLocaleString('es-GT'),
                usuario: 'Marlon'
            };
            
            egresos.push(nuevoEgreso);
            actualizarListaEgresos();
            
            // Guardar en base de datos
            guardarMovimiento('egreso', nuevoEgreso);
        }
    });
}

/**
 * Eliminar ingreso
 */
function eliminarIngreso(index) {
    Swal.fire({
        title: '¿Eliminar ingreso?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            ingresosAdicionales.splice(index, 1);
            actualizarListaIngresos();
        }
    });
}

/**
 * Eliminar egreso
 */
function eliminarEgreso(index) {
    Swal.fire({
        title: '¿Eliminar egreso?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            egresos.splice(index, 1);
            actualizarListaEgresos();
        }
    });
}

/**
 * Procesar cierre de caja
 */
function procesarCierreCaja() {
    const totalVentas = parseFloat(document.getElementById('total-ventas').textContent.replace('Q ', ''));
    const totalIngresos = ingresosAdicionales.reduce((sum, ing) => sum + parseFloat(ing.monto), 0);
    const totalEgresos = egresos.reduce((sum, egr) => sum + parseFloat(egr.monto), 0);
    const totalFinal = totalVentas + totalIngresos - totalEgresos;
    
    Swal.fire({
        title: 'Confirmar Cierre de Caja',
        html: `
            <div class="text-start">
                <div class="row mb-2">
                    <div class="col-8">Total Ventas:</div>
                    <div class="col-4 text-end fw-bold">Q ${totalVentas.toFixed(2)}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-8">Ingresos Adicionales:</div>
                    <div class="col-4 text-end fw-bold text-success">+ Q ${totalIngresos.toFixed(2)}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-8">Egresos:</div>
                    <div class="col-4 text-end fw-bold text-danger">- Q ${totalEgresos.toFixed(2)}</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-8"><strong>Total Final:</strong></div>
                    <div class="col-4 text-end fw-bold fs-5 text-primary">Q ${totalFinal.toFixed(2)}</div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar Cierre',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const cierreData = {
                    fecha: new Date().toISOString().split('T')[0],
                    turno: 'Diurno', // O determinar según la hora
                    total_ventas: totalVentas,
                    total_ingresos: totalIngresos,
                    total_egresos: totalEgresos,
                    total_final: totalFinal,
                    usuario: 'Marlon',
                    detalles: {
                        ventas: ventasDelTurno,
                        ingresos: ingresosAdicionales,
                        egresos: egresos
                    }
                };
                
                const response = await fetch('api/caja.php?action=cerrar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(cierreData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cierre Procesado',
                        text: `Cierre de caja #${data.cierre_id} registrado exitosamente`,
                        timer: 2000
                    });
                    
                    // Reiniciar datos del turno
                    reiniciarTurno();
                    cargarHistorialCierres();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al procesar el cierre'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo procesar el cierre'
                });
            }
        }
    });
}

/**
 * Reiniciar datos del turno
 */
function reiniciarTurno() {
    ventasDelTurno = [];
    ingresosAdicionales = [];
    egresos = [];
    
    actualizarResumenTurno({
        total_ventas: 0,
        total_efectivo: 0,
        total_tarjeta: 0,
        total_vales: 0
    });
    
    actualizarListaIngresos();
    actualizarListaEgresos();
}

/**
 * Cargar historial de cierres
 */
async function cargarHistorialCierres() {
    try {
        const response = await fetch('api/caja.php?action=historial');
        const data = await response.json();
        
        if (data.success) {
            mostrarHistorialCierres(data.cierres);
        } else {
            console.error('Error al cargar historial:', data.message);
            mostrarHistorialEjemplo();
        }
    } catch (error) {
        console.error('Error en la conexión:', error);
        mostrarHistorialEjemplo();
    }
}

/**
 * Mostrar historial de cierres
 */
function mostrarHistorialCierres(cierres) {
    const tbody = document.getElementById('tabla-cierres');
    
    if (cierres.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-history fa-2x mb-2 opacity-50"></i>
                    <p>No hay cierres registrados</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    cierres.forEach(cierre => {
        html += `
            <tr>
                <td>${cierre.fecha}</td>
                <td>${cierre.turno}</td>
                <td>Q ${parseFloat(cierre.total_ventas).toFixed(2)}</td>
                <td>Q ${parseFloat(cierre.total_efectivo || 0).toFixed(2)}</td>
                <td>Q ${parseFloat(cierre.total_tarjeta || 0).toFixed(2)}</td>
                <td>Q ${parseFloat(cierre.total_vales || 0).toFixed(2)}</td>
                <td class="fw-bold">Q ${parseFloat(cierre.total_final).toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="verDetalleCierre(${cierre.id})" title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

/**
 * Ver detalle de un cierre
 */
function verDetalleCierre(cierreId) {
    // Implementar vista de detalle del cierre
    console.log('Ver detalle del cierre:', cierreId);
}

/**
 * Guardar movimiento en base de datos
 */
async function guardarMovimiento(tipo, movimiento) {
    try {
        const response = await fetch('api/caja.php?action=guardar_movimiento', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ tipo, ...movimiento })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Error al guardar movimiento:', data.message);
        }
    } catch (error) {
        console.error('Error al guardar movimiento:', error);
    }
}

/**
 * Mostrar datos de ejemplo si falla la conexión
 */
function mostrarDatosEjemplo() {
    actualizarResumenTurno({
        total_ventas: 1250.00,
        total_efectivo: 800.00,
        total_tarjeta: 350.00,
        total_vales: 100.00
    });
    
    ingresosAdicionales = [
        {
            concepto: 'Venta especial',
            monto: 50.00,
            fecha: '26/01/2026 10:30',
            usuario: 'Marlon'
        }
    ];
    
    egresos = [
        {
            concepto: 'Compra de bolsas',
            monto: 25.00,
            fecha: '26/01/2026 11:00',
            usuario: 'Marlon'
        }
    ];
}

/**
 * Mostrar historial de ejemplo
 */
function mostrarHistorialEjemplo() {
    const cierresEjemplo = [
        {
            id: 1,
            fecha: '2026-01-25',
            turno: 'Diurno',
            total_ventas: 1500.00,
            total_efectivo: 900.00,
            total_tarjeta: 450.00,
            total_vales: 150.00,
            total_final: 1500.00
        }
    ];
    mostrarHistorialCierres(cierresEjemplo);
}