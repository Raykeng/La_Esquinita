<?php
// index.php
// Capturamos la 'llave' de la URL. Si no hay llave, vamos a 'dashboard'.
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Esquinita - Sistema POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="main-wrapper">
        <?php include 'layout/header.php'; ?>

        <div class="content-wrapper">
            <div id="view-container">
                <?php
                //  Un 'Switch' es como un semáforo inteligente.
                // Según lo que diga $vista, cargamos un archivo diferente de la carpeta módulos.
                switch ($vista) {
                    case 'dashboard':
                        include 'modulos/dashboard.php';
                        break;
                    case 'pos':
                        include 'modulos/pos.php';
                        break;
                    case 'productos':
                        include 'modulos/productos.php';
                        break;
                    case 'inventario':
                        include 'modulos/inventario.php';
                        break;
                    case 'clientes':
                        include 'modulos/clientes.php';
                        break;
                    case 'caja':
                        include 'modulos/caja.php';
                        break;
                    default:
                        echo "<h2>Error 404: Página no encontrada</h2>";
                        break;
                }
                ?>
            </div>
        </div>
    </div>

    <?php include 'layout/footer.php'; ?>

</body>

</html>