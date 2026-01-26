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
    
    <!-- Template CSS Assets -->
    <link rel="stylesheet" href="assets/css/remixicon.css">
    <link rel="stylesheet" href="assets/css/lib/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/lib/apexcharts.css">
    <link rel="stylesheet" href="assets/css/lib/dataTables.min.css">
    <link rel="stylesheet" href="assets/css/lib/editor-katex.min.css">
    <link rel="stylesheet" href="assets/css/lib/editor.atom-one-dark.min.css">
    <link rel="stylesheet" href="assets/css/lib/editor.quill.snow.css">
    <link rel="stylesheet" href="assets/css/lib/flatpickr.min.css">
    <link rel="stylesheet" href="assets/css/lib/full-calendar.css">
    <link rel="stylesheet" href="assets/css/lib/jquery-jvectormap-2.0.5.css">
    <link rel="stylesheet" href="assets/css/lib/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/lib/slick.css">
    <link rel="stylesheet" href="assets/css/lib/prism.css">
    <link rel="stylesheet" href="assets/css/lib/file-upload.css">
    <link rel="stylesheet" href="assets/css/lib/audioplayer.css">
    <!-- Main Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <!-- Sidebar content will be loaded here -->
        <?php include 'layout/header.php'; ?>
    </aside>

    <main class="dashboard-main">
        
        <div class="dashboard-main-body">

            <div id="view-container">
                <?php
                switch ($vista) {
                    case 'dashboard':
                        include 'modulos/dashboard.php';
                        break;
                    case 'pos':
                        include 'modulos/pos.php';
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
        
        <?php include 'layout/footer.php'; ?>
    </main>

</body>
</html>