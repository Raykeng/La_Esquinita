<?php
require_once __DIR__ . '/../models/Producto.php';

class ProductoController
{
    private $modelo;

    public function __construct($pdo)
    {
        $this->modelo = new Producto($pdo);
    }

    public function listarProductosPOS()
    {
        $productos = $this->modelo->obtenerTodos();
        return $productos;
    }
}
?>