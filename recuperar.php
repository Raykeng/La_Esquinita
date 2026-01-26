<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - La Esquinita</title>
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
            <div class="auth-logo text-warning">
                <i class="fas fa-key"></i>
            </div>
            <h4 class="fw-bold mb-1">Recuperar Acceso</h4>
            <p class="text-muted mb-0">Ingresa tu correo para restablecer</p>
        </div>

        <div class="auth-content">
            <!-- Alertas -->
            <div id="alert-container"></div>

            <form id="recovery-form">
                <div class="alert alert-info text-sm">
                    <i class="fas fa-info-circle me-1"></i> Te enviaremos un enlace de recuperación a tu correo.
                </div>

                <div class="mb-4">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" class="form-control" 
                               placeholder="ejemplo@correo.com" required>
                    </div>
                    <div class="invalid-feedback" id="email-error"></div>
                    <div class="form-text">Ingresa el correo asociado a tu cuenta</div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" id="btn-submit" class="btn btn-warning btn-lg fw-bold text-white">
                        <i class="fas fa-paper-plane me-2"></i>ENVIAR ENLACE
                    </button>
                </div>

                <div class="text-center">
                    <a href="login.php" class="text-decoration-none text-muted">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Iniciar Sesión
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <!-- Email Config -->
    <script src="api/email-config.js"></script>

    <script>
        document.getElementById('recovery-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const btn = document.getElementById('btn-submit');
            const emailField = document.getElementById('email');
            const email = emailField.value.trim();
            const alertContainer = document.getElementById('alert-container');
            
            // Limpiar errores previos
            clearErrors();
            
            // Validar email
            if (!email) {
                showFieldError('email', 'El correo electrónico es requerido');
                return;
            }
            
            if (!isValidEmail(email)) {
                showFieldError('email', 'Ingresa un correo electrónico válido');
                return;
            }
            
            // UI Loading
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
            btn.disabled = true;
            alertContainer.innerHTML = '';
            
            try {
                // 1. Solicitar token de recuperación al backend
                const response = await fetch('api/password-reset.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'request_reset',
                        email: email
                    })
                });
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message);
                }
                
                // 2. Si el backend devuelve datos del usuario, enviar email
                if (result.data) {
                    const emailResult = await sendPasswordResetEmail(
                        result.data.user_email,
                        result.data.user_name,
                        result.data.reset_token,
                        result.data.reset_link
                    );
                    
                    if (emailResult.success) {
                        showAlert('¡Email enviado exitosamente! Revisa tu bandeja de entrada (y spam).', 'success');
                        emailField.value = '';
                        emailField.classList.add('is-valid');
                    } else {
                        throw new Error(emailResult.message);
                    }
                } else {
                    // Usuario no encontrado, pero no revelamos esa información
                    showAlert('Si el email existe en nuestro sistema, recibirás instrucciones de recuperación.', 'info');
                    emailField.value = '';
                }
                
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al procesar la solicitud: ' + error.message, 'danger');
            } finally {
                // Restaurar botón
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>ENVIAR ENLACE';
                btn.disabled = false;
            }
        });
        
        // Validación en tiempo real del email
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value.trim();
            
            if (email.length > 0) {
                if (isValidEmail(email)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    document.getElementById('email-error').style.display = 'none';
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    document.getElementById('email-error').textContent = 'Formato de email inválido';
                    document.getElementById('email-error').style.display = 'block';
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
                document.getElementById('email-error').style.display = 'none';
            }
        });
        
        // Función para mostrar errores en campos
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '-error');
            
            field.classList.add('is-invalid');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
        
        // Función para limpiar errores
        function clearErrors() {
            const field = document.getElementById('email');
            const errorDiv = document.getElementById('email-error');
            
            field.classList.remove('is-invalid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
        
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'danger' ? 'alert-danger' : 
                              type === 'success' ? 'alert-success' : 
                              type === 'info' ? 'alert-info' : 'alert-warning';
            
            const icon = type === 'danger' ? 'fa-exclamation-triangle' : 
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'info' ? 'fa-info-circle' : 'fa-exclamation-circle';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    </script>
</body>
</html>
