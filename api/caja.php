<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'turno_actual':
            obtenerTurnoActual();
            break;
        case 'cerrar':
            procesarCierre();
            break;
        case 'historial':
            obtenerHistorial();
            break;
        case 'guardar_movimiento':
            guardarMovimiento();
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

function obtenerTurnoActual() {
    global $pdo;
    
    try {
        // Verificar si las tablas existen
        verificarTablasCaja();
        
        $fechaHoy = date('Y-m-d');
        
        // Obtener ventas del día actual
        $sqlVentas = "SELECT 
                        COUNT(*) as num_ventas,
                        COALESCE(SUM(total), 0) as total_ventas,
                        COALESCE(SUM(CASE WHEN metodo_pago = 'efectivo' THEN total ELSE 0 END), 0) as total_efectivo,
                        COALESCE(SUM(CASE WHEN metodo_pago = 'tarjeta' THEN total ELSE 0 END), 0) as total_tarjeta,
                        COALESCE(SUM(CASE WHEN metodo_pago = 'vales' THEN total ELSE 0 END), 0) as total_vales
                      FROM ventas 
                      WHERE DATE(fecha_venta) = ?";
        
        $stmt = $pdo->prepare($sqlVentas);
        $stmt->execute([$fechaHoy]);
        $resumenVentas = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener ingresos adicionales del día
        $sqlIngresos = "SELECT * FROM movimientos_caja 
                        WHERE tipo = 'ingreso' AND DATE(fecha) = ? 
                        ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sqlIngresos);
        $stmt->execute([$fechaHoy]);
        $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener egresos del día
        $sqlEgresos = "SELECT * FROM movimientos_caja 
                       WHERE tipo = 'egreso' AND DATE(fecha) = ? 
                       ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sqlEgresos);
        $stmt->execute([$fechaHoy]);
        $egresos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener ventas detalladas
        $sqlVentasDetalle = "SELECT * FROM ventas 
                            WHERE DATE(fecha_venta) = ? 
                            ORDER BY fecha_venta DESC";
        $stmt = $pdo->prepare($sqlVentasDetalle);
        $stmt->execute([$fechaHoy]);
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'resumen' => $resumenVentas,
            'ventas' => $ventas,
            'ingresos' => $ingresos,
            'egresos' => $egresos
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener datos del turno: ' . $e->getMessage());
    }
}

function procesarCierre() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        $pdo->beginTransaction();
        
        // Insertar registro de cierre
        $sql = "INSERT INTO cierres_caja 
                (fecha_cierre, turno, total_ventas, total_efectivo, total_tarjeta, total_vales, 
                 ingresos_adicionales, egresos, total_final, usuario, detalles, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cerrado')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $input['fecha'],
            $input['turno'],
            $input['total_ventas'],
            $input['total_ventas'], // Efectivo por defecto
            0, // Tarjeta
            0, // Vales
            $input['total_ingresos'],
            $input['total_egresos'],
            $input['total_final'],
            $input['usuario'],
            json_encode($input['detalles'])
        ]);
        
        $cierreId = $pdo->lastInsertId();
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cierre procesado exitosamente',
            'cierre_id' => $cierreId
        ]);
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw new Exception('Error al procesar cierre: ' . $e->getMessage());
    }
}

function obtenerHistorial() {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM cierres_caja 
                ORDER BY fecha_cierre DESC, id DESC 
                LIMIT 20";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $cierres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'cierres' => $cierres
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al obtener historial: ' . $e->getMessage());
    }
}

function guardarMovimiento() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        $sql = "INSERT INTO movimientos_caja (tipo, concepto, monto, usuario, fecha) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $input['tipo'],
            $input['concepto'],
            $input['monto'],
            $input['usuario']
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Movimiento guardado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al guardar movimiento: ' . $e->getMessage());
    }
}

function verificarTablasCaja() {
    global $pdo;
    
    // Crear tabla cierres_caja si no existe
    $sqlCierres = "CREATE TABLE IF NOT EXISTS cierres_caja (
        id INT PRIMARY KEY AUTO_INCREMENT,
        fecha_cierre DATE NOT NULL,
        turno ENUM('matutino', 'vespertino', 'nocturno', 'Diurno') DEFAULT 'Diurno',
        total_ventas DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_efectivo DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_tarjeta DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_vales DECIMAL(10,2) NOT NULL DEFAULT 0,
        ingresos_adicionales DECIMAL(10,2) DEFAULT 0,
        egresos DECIMAL(10,2) DEFAULT 0,
        total_final DECIMAL(10,2) NOT NULL DEFAULT 0,
        diferencia DECIMAL(10,2) DEFAULT 0,
        usuario VARCHAR(50) NOT NULL,
        detalles JSON,
        notas TEXT,
        estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sqlCierres);
    
    // Crear tabla movimientos_caja si no existe
    $sqlMovimientos = "CREATE TABLE IF NOT EXISTS movimientos_caja (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tipo ENUM('ingreso', 'egreso') NOT NULL,
        concepto VARCHAR(255) NOT NULL,
        monto DECIMAL(10,2) NOT NULL,
        usuario VARCHAR(50) NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sqlMovimientos);
}
?>