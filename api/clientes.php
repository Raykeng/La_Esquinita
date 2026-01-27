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
            listarClientes();
            break;
        case 'crear':
            crearCliente();
            break;
        case 'actualizar':
            actualizarCliente();
            break;
        case 'eliminar':
            eliminarCliente();
            break;
        case 'obtener':
            obtenerCliente();
            break;
        case 'historial':
            obtenerHistorialCliente();
            break;
        case 'buscar':
            buscarClientes();
            break;
        case 'actualizar_puntos':
            actualizarPuntos();
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

function listarClientes() {
    global $pdo;
    
    try {
        // Verificar si la tabla clientes existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'clientes'");
        if ($stmt->rowCount() == 0) {
            // Crear tabla clientes si no existe
            crearTablaClientes();
        }
        
        $sql = "SELECT 
                    c.id,
                    c.nombre,
                    c.telefono,
                    c.email,
                    c.direccion,
                    c.puntos_acumulados,
                    c.fecha_registro,
                    (SELECT COUNT(*) FROM ventas v WHERE v.cliente_id = c.id) as total_compras,
                    (SELECT SUM(v.total) FROM ventas v WHERE v.cliente_id = c.id) as total_gastado,
                    (SELECT MAX(v.fecha_venta) FROM ventas v WHERE v.cliente_id = c.id) as ultima_compra
                FROM clientes c
                WHERE c.estado = 'activo'
                ORDER BY c.nombre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($clientes as &$cliente) {
            $cliente['id'] = (int) $cliente['id'];
            $cliente['puntos_acumulados'] = (int) $cliente['puntos_acumulados'];
            $cliente['total_compras'] = (int) $cliente['total_compras'];
            $cliente['total_gastado'] = (float) ($cliente['total_gastado'] ?? 0);
            
            if ($cliente['ultima_compra']) {
                $cliente['ultima_compra'] = date('d/m/Y', strtotime($cliente['ultima_compra']));
            }
            
            if ($cliente['fecha_registro']) {
                $cliente['fecha_registro'] = date('d/m/Y', strtotime($cliente['fecha_registro']));
            }
        }
        
        echo json_encode([
            'success' => true,
            'clientes' => $clientes
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al listar clientes: ' . $e->getMessage());
    }
}

function crearCliente() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nombre = trim($input['nombre'] ?? '');
    $telefono = trim($input['telefono'] ?? '');
    $email = trim($input['email'] ?? '');
    $direccion = trim($input['direccion'] ?? '');
    
    if (empty($nombre)) {
        throw new Exception('El nombre del cliente es requerido');
    }
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido');
    }
    
    try {
        // Verificar si ya existe un cliente con el mismo nombre y teléfono
        if (!empty($telefono)) {
            $checkSql = "SELECT id FROM clientes WHERE nombre = ? AND telefono = ? AND estado = 'activo'";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([$nombre, $telefono]);
            
            if ($checkStmt->rowCount() > 0) {
                throw new Exception('Ya existe un cliente con ese nombre y teléfono');
            }
        }
        
        $sql = "INSERT INTO clientes (nombre, telefono, email, direccion, puntos_acumulados, estado, fecha_registro) 
                VALUES (?, ?, ?, ?, 0, 'activo', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $telefono, $email, $direccion]);
        
        $clienteId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cliente creado exitosamente',
            'cliente_id' => $clienteId
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al crear cliente: ' . $e->getMessage());
    }
}

function actualizarCliente() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = (int) ($input['id'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $telefono = trim($input['telefono'] ?? '');
    $email = trim($input['email'] ?? '');
    $direccion = trim($input['direccion'] ?? '');
    $puntos = (int) ($input['puntos_acumulados'] ?? 0);
    
    if (!$id || empty($nombre)) {
        throw new Exception('ID y nombre del cliente son requeridos');
    }
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido');
    }
    
    try {
        $sql = "UPDATE clientes 
                SET nombre = ?, telefono = ?, email = ?, direccion = ?, puntos_acumulados = ?
                WHERE id = ? AND estado = 'activo'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $telefono, $email, $direccion, $puntos, $id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception('Cliente no encontrado');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Cliente actualizado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al actualizar cliente: ' . $e->getMessage());
    }
}

function eliminarCliente() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('ID del cliente es requerido');
    }
    
    try {
        // Verificar si el cliente tiene ventas asociadas
        $checkSql = "SELECT COUNT(*) as total FROM ventas WHERE cliente_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            // Si tiene ventas, solo desactivar (soft delete)
            $sql = "UPDATE clientes SET estado = 'inactivo' WHERE id = ?";
        } else {
            // Si no tiene ventas, eliminar completamente
            $sql = "DELETE FROM clientes WHERE id = ?";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception('Cliente no encontrado');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al eliminar cliente: ' . $e->getMessage());
    }
}

function obtenerCliente() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('ID del cliente es requerido');
    }
    
    try {
        $sql = "SELECT 
                    c.*,
                    (SELECT COUNT(*) FROM ventas v WHERE v.cliente_id = c.id) as total_compras,
                    (SELECT SUM(v.total) FROM ventas v WHERE v.cliente_id = c.id) as total_gastado,
                    (SELECT MAX(v.fecha_venta) FROM ventas v WHERE v.cliente_id = c.id) as ultima_compra
                FROM clientes c
                WHERE c.id = ? AND c.estado = 'activo'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            throw new Exception('Cliente no encontrado');
        }
        
        // Formatear datos
        $cliente['id'] = (int) $cliente['id'];
        $cliente['puntos_acumulados'] = (int) $cliente['puntos_acumulados'];
        $cliente['total_compras'] = (int) $cliente['total_compras'];
        $cliente['total_gastado'] = (float) ($cliente['total_gastado'] ?? 0);
        
        echo json_encode([
            'success' => true,
            'cliente' => $cliente
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener cliente: ' . $e->getMessage());
    }
}

function obtenerHistorialCliente() {
    global $pdo;
    
    $id = (int) ($_GET['id'] ?? 0);
    $limite = (int) ($_GET['limite'] ?? 10);
    
    if (!$id) {
        throw new Exception('ID del cliente es requerido');
    }
    
    try {
        $sql = "SELECT 
                    v.id,
                    v.fecha_venta,
                    v.total,
                    v.metodo_pago,
                    v.descuento_aplicado,
                    COUNT(dv.id) as items_comprados
                FROM ventas v
                LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
                WHERE v.cliente_id = ?
                GROUP BY v.id
                ORDER BY v.fecha_venta DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $limite]);
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($historial as &$venta) {
            $venta['id'] = (int) $venta['id'];
            $venta['total'] = (float) $venta['total'];
            $venta['descuento_aplicado'] = (float) ($venta['descuento_aplicado'] ?? 0);
            $venta['items_comprados'] = (int) $venta['items_comprados'];
            $venta['fecha_venta'] = date('d/m/Y H:i', strtotime($venta['fecha_venta']));
        }
        
        echo json_encode([
            'success' => true,
            'historial' => $historial
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener historial: ' . $e->getMessage());
    }
}

function buscarClientes() {
    global $pdo;
    
    $termino = trim($_GET['q'] ?? '');
    
    if (empty($termino)) {
        throw new Exception('Término de búsqueda es requerido');
    }
    
    try {
        $sql = "SELECT 
                    id, nombre, telefono, email, puntos_acumulados
                FROM clientes 
                WHERE estado = 'activo' 
                AND (nombre LIKE ? OR telefono LIKE ? OR email LIKE ?)
                ORDER BY nombre ASC
                LIMIT 10";
        
        $searchTerm = "%$termino%";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($clientes as &$cliente) {
            $cliente['id'] = (int) $cliente['id'];
            $cliente['puntos_acumulados'] = (int) $cliente['puntos_acumulados'];
        }
        
        echo json_encode([
            'success' => true,
            'clientes' => $clientes
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en búsqueda: ' . $e->getMessage());
    }
}

function actualizarPuntos() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $clienteId = (int) ($input['cliente_id'] ?? 0);
    $puntos = (int) ($input['puntos'] ?? 0);
    $operacion = $input['operacion'] ?? 'sumar'; // sumar, restar, establecer
    
    if (!$clienteId) {
        throw new Exception('ID del cliente es requerido');
    }
    
    try {
        $pdo->beginTransaction();
        
        // Obtener puntos actuales
        $sql = "SELECT puntos_acumulados FROM clientes WHERE id = ? AND estado = 'activo'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            throw new Exception('Cliente no encontrado');
        }
        
        $puntosActuales = (int) $cliente['puntos_acumulados'];
        
        // Calcular nuevos puntos según la operación
        switch ($operacion) {
            case 'sumar':
                $nuevos_puntos = $puntosActuales + $puntos;
                break;
            case 'restar':
                $nuevos_puntos = max(0, $puntosActuales - $puntos);
                break;
            case 'establecer':
                $nuevos_puntos = max(0, $puntos);
                break;
            default:
                throw new Exception('Operación no válida');
        }
        
        // Actualizar puntos
        $updateSql = "UPDATE clientes SET puntos_acumulados = ? WHERE id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$nuevos_puntos, $clienteId]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Puntos actualizados exitosamente',
            'puntos_anteriores' => $puntosActuales,
            'puntos_nuevos' => $nuevos_puntos
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Error al actualizar puntos: ' . $e->getMessage());
    }
}

function crearTablaClientes() {
    global $pdo;
    
    $sql = "CREATE TABLE IF NOT EXISTS clientes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        telefono VARCHAR(20),
        email VARCHAR(100),
        direccion TEXT,
        puntos_acumulados INT DEFAULT 0,
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
}
?>