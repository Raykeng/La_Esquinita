<?php
// index.php
// Incluir middleware de autenticación
require_once 'middleware/auth.php';

// Verificar autenticación
verificarAutenticacion();

// Capturamos la 'llave' de la URL. Si no hay llave, vamos a 'dashboard'.
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'dashboard';

// Verificar acceso a la vista según el rol
verificarAccesoVista($vista);

// Obtener información del usuario actual
$usuarioActual = obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Esquinita - Sistema POS</title>

    <!-- IMPORTANTE: Define la API base antes de cargar pos.js -->
    <script>
        // Calcula dinámicamente la ruta base
        const API_BASE_URL = (() => {
            const pathname = window.location.pathname;
            const dirs = pathname.split('/').filter(Boolean); // ['La_esquinita', 'La_Esquinita', 'index.php']
            dirs.pop(); // Quita 'index.php'
            return '/' + dirs.join('/') + '/'; // Devuelve: /La_esquinita/La_Esquinita/
        })();

        console.log('🔧 API Base URL:', API_BASE_URL); // Para debugging
    </script>

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
        <?php include 'includes/sidebar.php'; ?>
    </aside>

    <main class="dashboard-main">

        <div class="dashboard-main-body">

            <div id="view-container">
                <?php
                switch ($vista) {
                    case 'dashboard':
                        include 'views/dashboard.php';
                        break;
                    case 'pos':
                        include 'views/pos.php';
                        break;
                    case 'productos':
                        include 'views/productos.php';
                        break;
                    case 'inventario':
                        include 'views/inventario.php';
                        break;
                    case 'clientes':
                        include 'views/clientes.php';
                        break;
                    case 'proveedores':
                        include 'views/proveedores.php';
                        break;
                    case 'caja':
                        include 'views/caja.php';
                        break;
                    case 'usuarios':
                        include 'views/usuarios.php';
                        break;
                    case 'reportes':
                        include 'views/reportes.php';
                        break;
                    default:
                        echo "<h2>Error 404: Página no encontrada</h2>";
                        break;
                }
                ?>
            </div>

        </div>

        <?php include 'includes/footer.php'; ?>
    </main>

    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts específicos por vista -->
    <?php if ($vista === 'pos'): ?>
        <script>
            console.log('🚀 JavaScript del POS cargado correctamente');
            console.log('📍 URL actual:', window.location.href);
        </script>
        <script src="assets/js/pos.js"></script>
    <?php elseif ($vista === 'clientes'): ?>
        <script src="assets/js/clientes.js"></script>
    <?php elseif ($vista === 'caja'): ?>
        <script src="assets/js/caja.js"></script>
    <?php endif; ?>

</body>

</html>