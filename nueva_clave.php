<?php
// Verificar si hay un token en la URL
$token = $_GET['token'] ?? '';
$tokenValid = false;
$userName = '';
$userEmail = '';
$errorMessage = '';

if (!empty($token)) {
    // Verificar token con la API
    $apiUrl = 'http://localhost/La_Esquinita/La_Esquinita/api/password-reset.php';
    $postData = json_encode([
        'action' => 'verify_token',
        'token' => $token
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        if ($result && $result['success']) {
            $tokenValid = true;
            $userName = $result['data']['user_name'];
            $userEmail = $result['data']['user_email'];
        } else {
            $errorMessage = $result['message'] ?? 'Token inválido';
        }
    } else {
        $errorMessage = 'Error de conexión con el servidor';
    }
} else {
    $errorMessage = 'Token no proporcionado';
}
?>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo text-success">
                <i class="fas fa-lock-open"></i>
            </div>
            <h4 class="fw-bold mb-1">Nueva Contraseña</h4>
            <?php if ($tokenValid): ?>
                <p class="text-muted mb-0">Hola <?php echo htmlspecialchars($userName); ?>, crea una contraseña segura</p>
            <?php else: ?>
                <p class="text-danger mb-0">Error de validación</p>
            <?php endif; ?>
        </div>

        <div class="auth-content">
            <?php if ($tokenValid): ?>
                <form id="resetForm" onsubmit="event.preventDefault(); guardarClave();">
                    <input type="hidden" id="resetToken" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="pass1" class="form-control" placeholder="••••••••" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('pass1')">
                                <i class="fas fa-eye" id="eye1"></i>
                            </button>
                        </div>
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <input type="password" id="pass2" class="form-control" placeholder="••••••••" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('pass2')">
                                <i class="fas fa-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success btn-lg fw-bold text-white" id="submitBtn">
                            <i class="fas fa-save me-2"></i>ACTUALIZAR CONTRASEÑA
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> <?php echo htmlspecialchars($errorMessage); ?>
                </div>
                <div class="d-grid">
                    <a href="recuperar.php" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left me-2"></i>Solicitar Nuevo Token
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById('eye' + fieldId.slice(-1));
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        async function guardarClave() {
            const p1 = document.getElementById('pass1').value;
            const p2 = document.getElementById('pass2').value;
            const token = document.getElementById('resetToken').value;
            const submitBtn = document.getElementById('submitBtn');

            // Validaciones
            if (p1.length < 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 6 caracteres'
                });
                return;
            }

            if (p1 !== p2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden'
                });
                return;
            }

            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';

            try {
                // Enviar solicitud a la API
                const response = await fetch('api/password-reset.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'reset_password',
                        token: token,
                        new_password: p1
                    })
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Contraseña actualizada correctamente',
                        confirmButtonColor: '#28a745'
                    });
                    
                    window.location.href = 'login.php';
                } else {
                    throw new Error(result.message || 'Error desconocido');
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al actualizar la contraseña'
                });
            } finally {
                // Rehabilitar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>ACTUALIZAR CONTRASEÑA';
            }
        }

        // Validación en tiempo real
        document.getElementById('pass2')?.addEventListener('input', function() {
            const p1 = document.getElementById('pass1').value;
            const p2 = this.value;
            
            if (p2.length > 0) {
                if (p1 === p2) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    </script>
</body>
</html>
