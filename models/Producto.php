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

    /**
     * Crear un nuevo producto
     */
    public function crear($datos)
    {
        try {
            $sql = "INSERT INTO productos (
                        codigo_barras, nombre, categoria_id, proveedor_id, 
                        precio_compra, precio_venta, stock_actual, stock_minimo, 
                        unidad_medida, fecha_vencimiento
                    ) VALUES (
                        :codigo, :nombre, :categoria, :proveedor,
                        :costo, :precio, :stock, :minimo,
                        :unidad, :vencimiento
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':codigo' => $datos['codigo_barras'],
                ':nombre' => $datos['nombre'],
                ':categoria' => $datos['categoria_id'],
                ':proveedor' => !empty($datos['proveedor_id']) ? $datos['proveedor_id'] : null,
                ':costo' => !empty($datos['precio_compra']) ? $datos['precio_compra'] : 0,
                ':precio' => $datos['precio_venta'],
                ':stock' => !empty($datos['stock_actual']) ? $datos['stock_actual'] : 0,
                ':minimo' => !empty($datos['stock_minimo']) ? $datos['stock_minimo'] : 5,
                ':unidad' => $datos['unidad_medida'],
                ':vencimiento' => !empty($datos['fecha_vencimiento']) ? $datos['fecha_vencimiento'] : null
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Error en Producto::crear: " . $e->getMessage());
            throw $e; // Re-lanzar para que el controller lo maneje
        }
    }

    /**
     * Actualizar un producto existente
     */
    public function actualizar($id, $datos)
    {
        try {
            $sql = "UPDATE productos SET
                        codigo_barras = :codigo,
                        nombre = :nombre,
                        categoria_id = :categoria,
                        proveedor_id = :proveedor,
                        precio_compra = :costo,
                        precio_venta = :precio,
                        stock_actual = :stock,
                        stock_minimo = :minimo,
                        unidad_medida = :unidad,
                        fecha_vencimiento = :vencimiento
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':codigo' => $datos['codigo_barras'],
                ':nombre' => $datos['nombre'],
                ':categoria' => $datos['categoria_id'],
                ':proveedor' => !empty($datos['proveedor_id']) ? $datos['proveedor_id'] : null,
                ':costo' => !empty($datos['precio_compra']) ? $datos['precio_compra'] : 0,
                ':precio' => $datos['precio_venta'],
                ':stock' => !empty($datos['stock_actual']) ? $datos['stock_actual'] : 0,
                ':minimo' => !empty($datos['stock_minimo']) ? $datos['stock_minimo'] : 5,
                ':unidad' => $datos['unidad_medida'],
                ':vencimiento' => !empty($datos['fecha_vencimiento']) ? $datos['fecha_vencimiento'] : null
            ]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error en Producto::actualizar: " . $e->getMessage());
            throw $e;
        }
    }
}
?>