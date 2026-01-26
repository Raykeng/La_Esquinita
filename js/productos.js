/**
 * js/productos.js
 * Lógica para la gestión de productos
 */

document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();

    // Listeners para cálculos automáticos
    document.getElementById('prod-costo').addEventListener('input', calcularGanancia);
    document.getElementById('prod-precio').addEventListener('input', calcularGanancia);
});

async function cargarProductos() {
    try {
        const respuesta = await fetch('api/productos.php');
        const data = await respuesta.json();

        const tbody = document.getElementById('lista-productos-body');

        if (!data.success || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No hay productos registrados</td></tr>';
            return;
        }

        tbody.innerHTML = data.data.map(p => `
            <tr>
                <td class="ps-4">
                    <div class="fw-bold text-dark">${p.nombre}</div>
                    <small class="text-muted"><i class="fas fa-barcode me-1"></i>${p.codigo_barras}</small>
                </td>
                <td><span class="badge bg-light text-dark border">${p.categoria_nombre || 'Sin Cat'}</span></td>
                <td class="text-end text-muted">Q ${parseFloat(p.precio_compra || 0).toFixed(2)}</td>
                <td class="text-end fw-bold text-success">Q ${parseFloat(p.precio_venta).toFixed(2)}</td>
                <td class="text-center">
                    <span class="badge ${p.stock_actual <= 5 ? 'bg-danger' : 'bg-success'}">
                        ${p.stock_actual} ${p.unidad_medida || 'u'}
                    </span>
                </td>
                <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-outline-primary" onclick="editarProducto(${p.id})"><i class="fas fa-edit"></i></button>
                </td>
            </tr>
        `).join('');

    } catch (error) {
        console.error('Error cargando productos:', error);
    }
}

async function abrirModalProducto() {
    // Resetear form
    document.getElementById('formProducto').reset();
    document.getElementById('prod-id').value = '';

    // Resetear título del modal
    document.getElementById('modalProductoLabel').innerText = 'Nuevo Producto';

    // Cargar selectores
    await Promise.all([cargarCategorias(), cargarProveedores()]);

    new bootstrap.Modal(document.getElementById('modalProducto')).show();
}

async function cargarCategorias() {
    const res = await fetch('api/categorias.php');
    const json = await res.json();
    const select = document.getElementById('prod-categoria');

    let html = '<option value="">Seleccionar...</option>';
    if (json.success) {
        html += json.data.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
    }
    select.innerHTML = html;
}

async function cargarProveedores() {
    const res = await fetch('api/proveedores.php');
    const json = await res.json();
    const select = document.getElementById('prod-proveedor');

    let html = '<option value="">Seleccionar...</option>';
    if (json.success) {
        html += json.data.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
    }
    select.innerHTML = html;
}

function calcularGanancia() {
    const costo = parseFloat(document.getElementById('prod-costo').value) || 0;
    const precio = parseFloat(document.getElementById('prod-precio').value) || 0;
    const ganancia = precio - costo;
    document.getElementById('prod-ganancia').value = `Q ${ganancia.toFixed(2)}`;
}

async function guardarProducto() {
    const form = document.getElementById('formProducto');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const data = {
        id: document.getElementById('prod-id').value,
        codigo_barras: document.getElementById('prod-codigo').value,
        nombre: document.getElementById('prod-nombre').value,
        categoria_id: document.getElementById('prod-categoria').value,
        proveedor_id: document.getElementById('prod-proveedor').value,
        precio_compra: document.getElementById('prod-costo').value,
        precio_venta: document.getElementById('prod-precio').value,
        stock_actual: document.getElementById('prod-stock').value,
        stock_minimo: document.getElementById('prod-minimo').value,
        unidad_medida: document.getElementById('prod-unidad').value,
        fecha_vencimiento: document.getElementById('prod-vencimiento').value
    };

    try {
        const respuesta = await fetch('api/productos_guardar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await respuesta.json();

        if (result.success) {
            alert('Producto guardado correctamente'); // TODO: Usar Toast
            bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
            cargarProductos();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión al guardar');
    }
}

async function crearCategoriaRapida() {
    const nombre = prompt("Nombre de la nueva categoría:");
    if (!nombre) return;

    try {
        const respuesta = await fetch('api/categorias.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre: nombre })
        });
        const result = await respuesta.json();

        if (result.success) {
            await cargarCategorias();
            // Seleccionar la nueva
            document.getElementById('prod-categoria').value = result.id;
        } else {
            alert("No se pudo crear la categoría");
        }
    } catch (e) {
        console.error(e);
    }
}

async function crearProveedorRapido() {
    const nombre = prompt("Nombre del nuevo proveedor:");
    if (!nombre) return;

    try {
        const respuesta = await fetch('api/proveedores.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre: nombre })
        });
        const result = await respuesta.json();

        if (result.success) {
            await cargarProveedores();
            // Seleccionar el nuevo
            document.getElementById('prod-proveedor').value = result.id;
        } else {
            alert("No se pudo crear el proveedor");
        }
    } catch (e) {
        console.error(e);
    }
}

async function editarProducto(id) {
    try {
        // Cargar datos del producto
        const respuesta = await fetch(`api/producto_detalle.php?id=${id}`);
        const result = await respuesta.json();

        if (!result.success) {
            alert('Error al cargar producto: ' + result.message);
            return;
        }

        const producto = result.data;

        // Cargar selectores primero
        await Promise.all([cargarCategorias(), cargarProveedores()]);

        // Llenar el formulario con los datos del producto
        document.getElementById('prod-id').value = producto.id;
        document.getElementById('prod-codigo').value = producto.codigo_barras;
        document.getElementById('prod-nombre').value = producto.nombre;
        document.getElementById('prod-categoria').value = producto.categoria_id || '';
        document.getElementById('prod-proveedor').value = producto.proveedor_id || '';
        document.getElementById('prod-costo').value = producto.precio_compra || '';
        document.getElementById('prod-precio').value = producto.precio_venta;
        document.getElementById('prod-stock').value = producto.stock_actual;
        document.getElementById('prod-minimo').value = producto.stock_minimo;
        document.getElementById('prod-unidad').value = producto.unidad_medida || 'pieza';
        document.getElementById('prod-vencimiento').value = producto.fecha_vencimiento || '';

        // Calcular ganancia
        calcularGanancia();

        // Cambiar título del modal
        document.getElementById('modalProductoLabel').innerText = 'Editar Producto';

        // Abrir modal
        new bootstrap.Modal(document.getElementById('modalProducto')).show();

    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el producto');
    }
}
