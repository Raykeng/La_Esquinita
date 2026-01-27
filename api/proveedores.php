<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'listar':
            listarProveedores();
            break;
        case 'crear':
            crearProveedor();
            break;
        case 'actualizar':
            actualizarProveedor();
            break;
        case 'eliminar':
            eliminarProveedor();
            break;
        case 'obtener':
            obtenerProveedor();
            break;
        case 'productos':
            obtenerProductosProveedor();
            break;
        case 'buscar':
            buscarProveedores();
            break;
        case 'estadisticas':
            obtenerEstadisticasProveedor();
            break;
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function listarProveedores() {
    global $pdo;
    
    try {
        // Verificar si la tabla proveedores existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'proveedores'");
        if ($stmt->rowCount() == 0) {
            // Crear tabla proveedores si no existe
            crearTablaProveedores();
        }
        
        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.contacto,
                    p.telefono,
                    p.email,
                    p.direccion,
                    p.categoria,
                    p.estado,
                    p.fecha_registro,
                    (SELECT COUNT(*) FROM productos pr WHERE pr.proveedor_id = p.id) as total_productos,
                    (SELECT SUM(pr.cantidad * pr.precio_compra) FROM productos pr WHERE pr.proveedor_id = p.id) as valor_inventario
                FROM proveedores p
                WHERE p.estado = 'activo'
                ORDER BY p.nombre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($proveedores as &$proveedor) {
            $proveedor['id'] = (int) $proveedor['id'];
            $proveedor['total_productos'] = (int) $proveedor['total_productos'];
            $proveedor['valor_inventario'] = (float) ($proveedor['valor_inventario'] ?? 0);
            
            if ($proveedor['fecha_registro']) {
                $proveedor['fecha_registro'] = date('d/m/Y', strtotime($proveedor['fecha_registro']));
            }
        }
        
        echo json_encode([
            'success' => true,
            'proveedores' => $proveedores
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al listar proveedores: ' . $e->getMessage());
    }
}

function crearProveedor() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nombre = trim($input['nombre'] ?? '');
    $contacto = trim($input['contacto'] ?? '');
    $telefono = trim($input['telefono'] ?? '');
    $email = trim($input['email'] ?? '');
    $direccion = trim($input['direccion'] ?? '');
    $categoria = trim($input['categoria'] ?? '');
    
    if (empty($nombre)) {
        throw new Exception('El nombre del proveedor es requerido');
    }
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido');
    }
    
    try {
        // Verificar si ya existe un proveedor con el mismo nombre
        $checkSql = "SELECT id FROM proveedores WHERE nombre = ? AND estado = 'activo'";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$nombre]);
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception('Ya existe un proveedor con ese nombre');
        }
        
        $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, categoria, estado, fecha_registro) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $categoria]);
        
        $proveedorId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Proveedor creado exitosamente',
            'proveedor_id' => $proveedorId
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al crear proveedor: ' . $e->getMessage());
    }
}

function actualizarProveedor() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = (int) ($input['id'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $contacto = trim($input['contacto'] ?? '');
    $telefono = trim($input['telefono'] ?? '');
    $email = trim($input['email'] ?? '');
    $direccion = trim($input['direccion'] ?? '');
    $categoria = trim($input['categoria'] ?? '');
    
    if (!$id || empty($nombre)) {
        throw new Exception('ID y nombre del proveedor son requeridos');
    }
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido');
    }
    
    try {
        // Verificar si ya existe otro proveedor con el mismo nombre
        $checkSql = "SELECT id FROM proveedores WHERE nombre = ? AND id != ? AND estado = 'activo'";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$nombre, $id]);
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception('Ya existe otro proveedor con ese nombre');
        }
        
        $sql = "UPDATE proveedores 
                SET nombre = ?, contacto = ?, telefono = ?, email = ?, direccion = ?, categoria = ?
                WHERE id = ? AND estado = 'activo'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $categoria, $id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception('Proveedor no encontrado');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al actualizar proveedor: ' . $e->getMessage());
    }
}

function eliminarProveedor() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('ID del proveedor es requerido');
    }
    
    try {
        // Verificar si el proveedor tiene productos asociados
        $checkSql = "SELECT COUNT(*) as total FROM productos WHERE proveedor_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            // Si tiene productos, solo desactivar (soft delete)
            $sql = "UPDATE proveedores SET estado = 'inactivo' WHERE id = ?";
            $mensaje = 'Proveedor desactivado (tiene productos asociados)';
        } else {
            // Si no tiene productos, eliminar completamente
            $sql = "DELETE FROM proveedores WHERE id = ?";
            $mensaje = 'Proveedor eliminado exitosamente';
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception('Proveedor no encontrado');
        }
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al eliminar proveedor: ' . $e->getMessage());
    }
}

function obtenerProveedor() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('ID del proveedor es requerido');
    }
    
    try {
        $sql = "SELECT 
                    p.*,
                    (SELECT COUNT(*) FROM productos pr WHERE pr.proveedor_id = p.id) as total_productos,
                    (SELECT SUM(pr.cantidad * pr.precio_compra) FROM productos pr WHERE pr.proveedor_id = p.id) as valor_inventario
                FROM proveedores p
                WHERE p.id = ? AND p.estado = 'activo'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$proveedor) {
            throw new Exception('Proveedor no encontrado');
        }
        
        // Formatear datos
        $proveedor['id'] = (int) $proveedor['id'];
        $proveedor['total_productos'] = (int) $proveedor['total_productos'];
        $proveedor['valor_inventario'] = (float) ($proveedor['valor_inventario'] ?? 0);
        
        echo json_encode([
            'success' => true,
            'proveedor' => $proveedor
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener proveedor: ' . $e->getMessage());
    }
}

function obtenerProductosProveedor() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    $limite = (int) ($_GET['limite'] ?? 20);
    
    if (!$id) {
        throw new Exception('ID del proveedor es requerido');
    }
    
    try {
        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.codigo_barras,
                    p.precio_compra,
                    p.precio_venta,
                    p.cantidad,
                    p.fecha_vencimiento,
                    c.nombre as categoria
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.proveedor_id = ?
                ORDER BY p.nombre ASC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $limite]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($productos as &$producto) {
            $producto['id'] = (int) $producto['id'];
            $producto['precio_compra'] = (float) $producto['precio_compra'];
            $producto['precio_venta'] = (float) $producto['precio_venta'];
            $producto['cantidad'] = (int) $producto['cantidad'];
            
            if ($producto['fecha_vencimiento']) {
                $producto['fecha_vencimiento'] = date('d/m/Y', strtotime($producto['fecha_vencimiento']));
            }
        }
        
        echo json_encode([
            'success' => true,
            'productos' => $productos
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener productos: ' . $e->getMessage());
    }
}

function buscarProveedores() {
    global $pdo;
    
    $termino = trim($_GET['q'] ?? '');
    
    if (empty($termino)) {
        throw new Exception('Término de búsqueda es requerido');
    }
    
    try {
        $sql = "SELECT 
                    id, nombre, contacto, telefono, email, categoria
                FROM proveedores 
                WHERE estado = 'activo' 
                AND (nombre LIKE ? OR contacto LIKE ? OR telefono LIKE ? OR email LIKE ?)
                ORDER BY nombre ASC
                LIMIT 10";
        
        $searchTerm = "%$termino%";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($proveedores as &$proveedor) {
            $proveedor['id'] = (int) $proveedor['id'];
        }
        
        echo json_encode([
            'success' => true,
            'proveedores' => $proveedores
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en búsqueda: ' . $e->getMessage());
    }
}

function obtenerEstadisticasProveedor() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('ID del proveedor es requerido');
    }
    
    try {
        // Estadísticas generales
        $sql = "SELECT 
                    COUNT(p.id) as total_productos,
                    SUM(p.cantidad) as total_stock,
                    SUM(p.cantidad * p.precio_compra) as valor_inventario,
                    AVG(p.precio_venta - p.precio_compra) as margen_promedio
                FROM productos p
                WHERE p.proveedor_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Productos más vendidos del proveedor
        $ventasSql = "SELECT 
                        p.nombre,
                        SUM(dv.cantidad) as cantidad_vendida,
                        SUM(dv.subtotal) as total_ventas
                      FROM productos p
                      INNER JOIN detalle_venta dv ON p.id = dv.producto_id
                      INNER JOIN ventas v ON dv.venta_id = v.id
                      WHERE p.proveedor_id = ?
                      AND v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                      GROUP BY p.id, p.nombre
                      ORDER BY cantidad_vendida DESC
                      LIMIT 5";
        
        $ventasStmt = $pdo->prepare($ventasSql);
        $ventasStmt->execute([$id]);
        $productosVendidos = $ventasStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        $estadisticas['total_productos'] = (int) $estadisticas['total_productos'];
        $estadisticas['total_stock'] = (int) ($estadisticas['total_stock'] ?? 0);
        $estadisticas['valor_inventario'] = (float) ($estadisticas['valor_inventario'] ?? 0);
        $estadisticas['margen_promedio'] = (float) ($estadisticas['margen_promedio'] ?? 0);
        
        foreach ($productosVendidos as &$producto) {
            $producto['cantidad_vendida'] = (int) $producto['cantidad_vendida'];
            $producto['total_ventas'] = (float) $producto['total_ventas'];
        }
        
        echo json_encode([
            'success' => true,
            'estadisticas' => $estadisticas,
            'productos_vendidos' => $productosVendidos
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener estadísticas: ' . $e->getMessage());
    }
}

function crearTablaProveedores() {
    global $pdo;
    
    $sql = "CREATE TABLE IF NOT EXISTS proveedores (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        contacto VARCHAR(100),
        telefono VARCHAR(20),
        email VARCHAR(100),
        direccion TEXT,
        categoria VARCHAR(50),
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    // Insertar algunos proveedores de ejemplo
    $proveedoresEjemplo = [
        ['Distribuidora Central', 'Juan Pérez', '2234-5678', 'ventas@distcentral.com', 'Zona 1, Ciudad de Guatemala', 'Bebidas'],
        ['Abarrotes del Norte', 'María López', '2345-6789', 'info@abarrotesnorte.com', 'Zona 18, Ciudad de Guatemala', 'Abarrotes'],
        ['Productos Frescos SA', 'Carlos Mendoza', '2456-7890', 'carlos@frescos.com', 'Villa Nueva', 'Frescos']
    ];
    
    foreach ($proveedoresEjemplo as $proveedor) {
        $checkSql = "SELECT COUNT(*) FROM proveedores WHERE nombre = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$proveedor[0]]);
        
        if ($checkStmt->fetchColumn() == 0) {
            $insertSql = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, categoria) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute($proveedor);
        }
    }
}
?>