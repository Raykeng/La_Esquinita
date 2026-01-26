<?php
class Producto
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        try {
            // por si los datos insertados no tienen estado definido.
            $sql = "SELECT 
                        p.id, 
                        p.codigo_barras, 
                        p.nombre, 
                        p.precio_venta, 
                        p.stock_actual, 
                        p.stock_minimo,
                        p.fecha_vencimiento,
                        p.categoria_id,
                        c.nombre as categoria_nombre,
                        CASE 
                            WHEN p.fecha_vencimiento IS NOT NULL 
                                 AND p.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 60 DAY) 
                            THEN 1 ELSE 0 
                        END as por_vencer
                    FROM productos p
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    ORDER BY p.nombre ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Producto::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }
}
?>