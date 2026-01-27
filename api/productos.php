<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir la configuración de la base de datos
require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            obtenerProductos();
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $accion = $input['accion'] ?? '';
            
            switch ($accion) {
                case 'crear':
                    crearProducto($input);
                    break;
                case 'actualizar':
                    actualizarProducto($input);
                    break;
                default:
                    throw new Exception('Acción no válida');
            }
            break;
        default:
            throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function obtenerProductos() {
    global $pdo;
    
    // Consulta para obtener productos con información de categoría
    $sql = "SELECT 
                p.id,
                p.codigo_barras,
                p.nombre,
                p.descripcion,
                p.precio_compra as precio_costo,
                p.precio_venta,
                p.stock_actual,
                p.stock_minimo,
                p.stock_maximo,
                p.unidad_medida,
                p.fecha_vencimiento,
                p.estado,
                c.nombre as categoria_nombre,
                CASE 
                    WHEN p.fecha_vencimiento IS NOT NULL 
                    AND DATEDIFF(p.fecha_vencimiento, CURDATE()) <= 60 
                    AND DATEDIFF(p.fecha_vencimiento, CURDATE()) > 0
                    THEN 1 
                    ELSE 0 
                END as por_vencer
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.estado = 'activo'
            ORDER BY p.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir tipos de datos para JavaScript
    foreach ($productos as &$producto) {
        $producto['id'] = (int) $producto['id'];
        $producto['precio_costo'] = (float) $producto['precio_costo'];
        $producto['precio_venta'] = (float) $producto['precio_venta'];
        $producto['stock_actual'] = (int) $producto['stock_actual'];
        $producto['stock_minimo'] = (int) $producto['stock_minimo'];
        $producto['stock_maximo'] = (int) $producto['stock_maximo'];
        $producto['por_vencer'] = (bool) $producto['por_vencer'];

        // Agregar campos por defecto
        $producto['descuento_manual'] = 0;
        $producto['promocion'] = false;
    }

    echo json_encode([
        'success' => true,
        'data' => $productos,
        'total' => count($productos)
    ]);
}

function crearProducto($datos) {
    global $pdo;
    
    // Validar datos requeridos
    $camposRequeridos = ['nombre', 'categoria_id', 'precio_costo', 'precio_venta', 'stock_inicial', 'stock_minimo'];
    foreach ($camposRequeridos as $campo) {
        if (empty($datos[$campo])) {
            throw new Exception("El campo $campo es requerido");
        }
    }
    
    // Validar que el precio de venta sea mayor al costo
    if ($datos['precio_venta'] <= $datos['precio_costo']) {
        throw new Exception('El precio de venta debe ser mayor al precio de costo');
    }
    
    // Verificar si el código de barras ya existe (si se proporciona)
    if (!empty($datos['codigo_barras'])) {
        $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo_barras = ?");
        $stmt->execute([$datos['codigo_barras']]);
        if ($stmt->fetch()) {
            throw new Exception('Ya existe un producto con ese código de barras');
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Insertar producto
        $sql = "INSERT INTO productos (
            nombre, 
            descripcion, 
            codigo_barras, 
            categoria_id, 
            proveedor_id,
            precio_compra, 
            precio_venta, 
            stock_actual, 
            stock_minimo, 
            stock_maximo,
            fecha_vencimiento,
            unidad_medida,
            estado,
            fecha_creacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['codigo_barras'] ?? null,
            $datos['categoria_id'],
            $datos['proveedor_id'] ?? null,
            $datos['precio_costo'],
            $datos['precio_venta'],
            $datos['stock_inicial'],
            $datos['stock_minimo'],
            $datos['stock_minimo'] * 3, // stock_maximo por defecto
            $datos['fecha_vencimiento'] ?? null,
            'unidad', // unidad_medida por defecto
            $datos['activo'] ? 'activo' : 'inactivo'
        ]);
        
        $productoId = $pdo->lastInsertId();
        
        // Registrar movimiento de inventario inicial
        if ($datos['stock_inicial'] > 0) {
            $sqlMovimiento = "INSERT INTO movimientos_inventario (
                producto_id, 
                tipo_movimiento, 
                cantidad, 
                motivo, 
                fecha_movimiento
            ) VALUES (?, 'entrada', ?, 'Stock inicial', NOW())";
            
            $stmtMovimiento = $pdo->prepare($sqlMovimiento);
            $stmtMovimiento->execute([$productoId, $datos['stock_inicial']]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'producto_id' => $productoId
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Error al crear producto: ' . $e->getMessage());
    }
}

function actualizarProducto($datos) {
    global $pdo;
    
    if (empty($datos['id'])) {
        throw new Exception('ID del producto es requerido');
    }
    
    // Construir query dinámico
    $campos = [];
    $valores = [];
    
    $camposPermitidos = [
        'nombre', 'descripcion', 'codigo_barras', 'categoria_id', 'proveedor_id',
        'precio_compra', 'precio_venta', 'stock_minimo', 'stock_maximo',
        'fecha_vencimiento', 'unidad_medida', 'estado'
    ];
    
    foreach ($camposPermitidos as $campo) {
        if (isset($datos[$campo])) {
            $campos[] = "$campo = ?";
            $valores[] = $datos[$campo];
        }
    }
    
    if (empty($campos)) {
        throw new Exception('No hay campos para actualizar');
    }
    
    $valores[] = $datos['id'];
    
    $sql = "UPDATE productos SET " . implode(', ', $campos) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($valores);
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado exitosamente'
    ]);
}
?>