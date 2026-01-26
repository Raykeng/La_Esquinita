<?php
/**
 * models/Categoria.php
 * Gestión de Categorías
 */
require_once __DIR__ . '/../config/db.php';

class Categoria
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function crear($nombre, $descripcion = '')
    {
        try {
            $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>