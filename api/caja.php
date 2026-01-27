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
        case 'reporte':
            generarReporteCierre();
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
                      WHERE DATE(fecha_venta) = ? AND estado = 'completada'";
        
        $stmt = $pdo->prepare($sqlVentas);
        $stmt->execute([$fechaHoy]);
        $resumenVentas = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener ingresos adicionales del día
        $ingresos = [];
        $egresos = [];
        
        try {
            // Verificar que la tabla movimientos_caja existe
            $checkTable = "SHOW TABLES LIKE 'movimientos_caja'";
            $tableExists = $pdo->query($checkTable)->rowCount() > 0;
            
            if ($tableExists) {
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
            }
        } catch (Exception $e) {
            // Si hay error con movimientos_caja, continuar con arrays vacíos
            error_log("Error al obtener movimientos: " . $e->getMessage());
            $ingresos = [];
            $egresos = [];
        }
        
        // Obtener ventas detalladas del día
        $sqlVentasDetalle = "SELECT v.*, u.nombre_completo as cajero, c.nombre as cliente_nombre
                            FROM ventas v 
                            LEFT JOIN usuarios u ON v.usuario_id = u.id
                            LEFT JOIN clientes c ON v.cliente_id = c.id
                            WHERE DATE(v.fecha_venta) = ? AND v.estado = 'completada'
                            ORDER BY v.fecha_venta DESC";
        $stmt = $pdo->prepare($sqlVentasDetalle);
        $stmt->execute([$fechaHoy]);
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular totales de ingresos y egresos
        $totalIngresos = array_sum(array_column($ingresos, 'monto'));
        $totalEgresos = array_sum(array_column($egresos, 'monto'));
        
        echo json_encode([
            'success' => true,
            'resumen' => $resumenVentas,
            'ventas' => $ventas,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'total_ingresos' => $totalIngresos,
            'total_egresos' => $totalEgresos
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
        
        // Insertar registro de cierre usando la tabla cierres_caja
        $sql = "INSERT INTO cierres_caja 
                (fecha_cierre, turno, total_ventas, total_efectivo, total_tarjeta, total_vales, 
                 ingresos_adicionales, egresos, total_final, usuario, detalles, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cerrado')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $input['fecha'],
            $input['turno'] ?? 'Diurno',
            $input['total_ventas'],
            $input['total_efectivo'] ?? $input['total_ventas'], // Si no se especifica, asumir todo efectivo
            $input['total_tarjeta'] ?? 0,
            $input['total_vales'] ?? 0,
            $input['total_ingresos'] ?? 0,
            $input['total_egresos'] ?? 0,
            $input['total_final'],
            $input['usuario'] ?? 'Sistema',
            json_encode($input['detalles'] ?? [])
        ]);
        
        $cierreId = $pdo->lastInsertId();
        
        // También crear un registro en turnos_caja si no existe uno abierto
        $sqlTurno = "SELECT id FROM turnos_caja WHERE DATE(fecha_apertura) = ? AND estado = 'abierto' LIMIT 1";
        $stmtTurno = $pdo->prepare($sqlTurno);
        $stmtTurno->execute([date('Y-m-d')]);
        $turnoExistente = $stmtTurno->fetch();
        
        if (!$turnoExistente) {
            // Crear turno y cerrarlo inmediatamente
            $sqlCrearTurno = "INSERT INTO turnos_caja 
                             (usuario_id, monto_inicial, monto_final, total_ventas, total_efectivo, 
                              total_tarjeta, total_vales, ingresos_extra, retiros, diferencia, 
                              estado, fecha_apertura, fecha_cierre) 
                             VALUES (1, 0, ?, ?, ?, ?, ?, ?, ?, 0, 'cerrado', ?, NOW())";
            
            $stmtCrearTurno = $pdo->prepare($sqlCrearTurno);
            $stmtCrearTurno->execute([
                $input['total_final'],
                $input['total_ventas'],
                $input['total_efectivo'] ?? $input['total_ventas'],
                $input['total_tarjeta'] ?? 0,
                $input['total_vales'] ?? 0,
                $input['total_ingresos'] ?? 0,
                $input['total_egresos'] ?? 0,
                $input['fecha'] . ' 00:00:00'
            ]);
        } else {
            // Actualizar turno existente
            $sqlActualizarTurno = "UPDATE turnos_caja SET 
                                  monto_final = ?, total_ventas = ?, total_efectivo = ?, 
                                  total_tarjeta = ?, total_vales = ?, ingresos_extra = ?, 
                                  retiros = ?, estado = 'cerrado', fecha_cierre = NOW()
                                  WHERE id = ?";
            
            $stmtActualizar = $pdo->prepare($sqlActualizarTurno);
            $stmtActualizar->execute([
                $input['total_final'],
                $input['total_ventas'],
                $input['total_efectivo'] ?? $input['total_ventas'],
                $input['total_tarjeta'] ?? 0,
                $input['total_vales'] ?? 0,
                $input['total_ingresos'] ?? 0,
                $input['total_egresos'] ?? 0,
                $turnoExistente['id']
            ]);
        }
        
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
        // Usar la tabla movimientos_caja independiente (sin turno_id)
        $sql = "INSERT INTO movimientos_caja (tipo, concepto, monto, usuario, fecha) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $input['tipo'],
            $input['concepto'],
            $input['monto'],
            $input['usuario'] ?? 'Sistema'
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Movimiento guardado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al guardar movimiento: ' . $e->getMessage());
    }
}

function generarReporteCierre() {
    global $pdo;
    
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
    $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
    
    try {
        // Verificar y crear tablas si es necesario
        verificarTablasCaja();
        
        // Obtener cierres en el rango de fechas
        $sql = "SELECT * FROM cierres_caja 
                WHERE fecha_cierre BETWEEN ? AND ? 
                ORDER BY fecha_cierre DESC, id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fechaInicio, $fechaFin]);
        $cierres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular totales
        $totalVentas = array_sum(array_column($cierres, 'total_ventas'));
        $totalEfectivo = array_sum(array_column($cierres, 'total_efectivo'));
        $totalTarjeta = array_sum(array_column($cierres, 'total_tarjeta'));
        $totalVales = array_sum(array_column($cierres, 'total_vales'));
        $totalIngresos = array_sum(array_column($cierres, 'ingresos_adicionales'));
        $totalEgresos = array_sum(array_column($cierres, 'egresos'));
        $totalFinal = array_sum(array_column($cierres, 'total_final'));
        
        // Obtener movimientos en el rango - con manejo de errores mejorado
        $movimientos = [];
        try {
            // Verificar que la tabla existe y tiene la columna fecha
            $sqlCheckColumn = "SHOW COLUMNS FROM movimientos_caja LIKE 'fecha'";
            $columnExists = $pdo->query($sqlCheckColumn)->rowCount() > 0;
            
            if ($columnExists) {
                $sqlMovimientos = "SELECT * FROM movimientos_caja 
                                  WHERE DATE(fecha) BETWEEN ? AND ? 
                                  ORDER BY fecha DESC";
                
                $stmt = $pdo->prepare($sqlMovimientos);
                $stmt->execute([$fechaInicio, $fechaFin]);
                $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            // Si hay error con movimientos, continuar sin ellos
            error_log("Error en consulta movimientos_caja: " . $e->getMessage());
            $movimientos = [];
        }
        
        echo json_encode([
            'success' => true,
            'periodo' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ],
            'cierres' => $cierres,
            'movimientos' => $movimientos,
            'resumen' => [
                'num_cierres' => count($cierres),
                'total_ventas' => $totalVentas,
                'total_efectivo' => $totalEfectivo,
                'total_tarjeta' => $totalTarjeta,
                'total_vales' => $totalVales,
                'total_ingresos' => $totalIngresos,
                'total_egresos' => $totalEgresos,
                'total_final' => $totalFinal
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al generar reporte: ' . $e->getMessage());
    }
}

function verificarTablasCaja() {
    global $pdo;
    
    // Crear tabla cierres_caja si no existe (para el historial de cierres)
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
        estado ENUM('abierto', 'cerrado') DEFAULT 'cerrado',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sqlCierres);
    
    // Crear tabla movimientos_caja independiente si no existe
    $sqlMovimientos = "CREATE TABLE IF NOT EXISTS movimientos_caja (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tipo ENUM('ingreso', 'egreso') NOT NULL,
        concepto VARCHAR(255) NOT NULL,
        monto DECIMAL(10,2) NOT NULL,
        usuario VARCHAR(50) NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sqlMovimientos);
    
    // Verificar si existe la tabla turnos_caja del esquema principal
    $sqlCheckTurnos = "SHOW TABLES LIKE 'turnos_caja'";
    $result = $pdo->query($sqlCheckTurnos);
    
    if ($result->rowCount() == 0) {
        // Crear tabla turnos_caja si no existe (compatible con el esquema principal)
        $sqlTurnos = "CREATE TABLE IF NOT EXISTS turnos_caja (
            id INT PRIMARY KEY AUTO_INCREMENT,
            usuario_id INT NOT NULL DEFAULT 1,
            monto_inicial DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
            monto_final DECIMAL(10, 2),
            total_ventas DECIMAL(10, 2) DEFAULT 0.00,
            total_efectivo DECIMAL(10, 2) DEFAULT 0.00,
            total_tarjeta DECIMAL(10, 2) DEFAULT 0.00,
            total_vales DECIMAL(10, 2) DEFAULT 0.00,
            retiros DECIMAL(10, 2) DEFAULT 0.00,
            ingresos_extra DECIMAL(10, 2) DEFAULT 0.00,
            diferencia DECIMAL(10, 2) DEFAULT 0.00,
            estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
            observaciones TEXT,
            fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_cierre TIMESTAMP NULL,
            INDEX idx_usuario (usuario_id),
            INDEX idx_estado (estado),
            INDEX idx_fecha_apertura (fecha_apertura)
        )";
        
        $pdo->exec($sqlTurnos);
    }
}
?>