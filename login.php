<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - La Esquinita</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-body">

    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fas fa-store"></i>
            </div>
            <h4 class="fw-bold mb-1">La Esquinita</h4>
            <p class="text-muted mb-0">Sistema de Punto de Venta</p>
        </div>

        <div class="auth-content">
            <!-- Form points to index.php for demo purposes, in real app should point to auth logic -->
            <form action="index.php?vista=dashboard" method="POST">
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="cajero@laesquinita.com"
                            value="admin@laesquinita.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Contraseña</label>
                        <a href="recuperar.php" class="text-xs text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" value="123456" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                        INGRESAR <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="auth-footer">
            <p class="mb-0 text-muted">&copy; 2026 Abarrotera La Esquinita</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
