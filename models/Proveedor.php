<?php
/**
 * models/Proveedor.php
 * Gestión de Proveedores
 */
require_once __DIR__ . '/../config/db.php';

class Proveedor
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM proveedores ORDER BY nombre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>