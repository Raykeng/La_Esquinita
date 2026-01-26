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

    public function crear($nombre, $contacto = '', $telefono = '', $email = '')
    {
        try {
            $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email) VALUES (:nombre, :contacto, :telefono, :email)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':contacto' => $contacto,
                ':telefono' => $telefono,
                ':email' => $email
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>