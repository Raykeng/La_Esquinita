<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - La Esquinita</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo text-success">
                <i class="fas fa-lock-open"></i>
            </div>
            <h4 class="fw-bold mb-1">Nueva Contraseña</h4>
            <p class="text-muted mb-0">Crea una contraseña segura</p>
        </div>

        <div class="auth-content">
            <form onsubmit="event.preventDefault(); guardarClave();">
                
                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="pass1" class="form-control" placeholder="••••••••" required minlength="6">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <input type="password" id="pass2" class="form-control" placeholder="••••••••" required minlength="6">
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success btn-lg fw-bold text-white">
                        ACTUALIZAR CONTRASEÑA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function guardarClave() {
            const p1 = document.getElementById('pass1').value;
            const p2 = document.getElementById('pass2').value;

            if(p1 !== p2) {
                alert('Las contraseñas no coinciden.');
                return;
            }

            alert('¡Contraseña actualizada correctamente!');
            window.location.href = 'login.php';
        }
    </script>
</body>
</html>
