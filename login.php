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
            <!-- Alertas -->
            <div id="alert-container"></div>

            <form id="login-form" action="index.php?vista=dashboard" method="POST">
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" 
                               placeholder="cajero@laesquinita.com" required>
                    </div>
                    <div class="invalid-feedback" id="email-error"></div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Contraseña</label>
                        <a href="recuperar.php" class="text-xs text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="••••••••" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="password-error"></div>
                    <div class="form-text">Mínimo 6 caracteres</div>
                </div>

                <div class="d-grid">
                    <button type="submit" id="login-btn" class="btn btn-primary btn-lg fw-bold">
                        INGRESAR <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>

                <div class="text-center mt-3">
                    <p class="mb-0 text-muted">
                        ¿No tienes cuenta? 
                        <a href="registro.php" class="text-decoration-none fw-bold">Registrarse</a>
                    </p>
                </div>
            </form>
        </div>

        <div class="auth-footer">
            <p class="mb-0 text-muted">&copy; 2026 Abarrotera La Esquinita</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Validación del formulario de login
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('login-btn');
            
            // Limpiar errores previos
            clearErrors();
            
            let isValid = true;
            
            // Validar email
            if (!email) {
                showFieldError('email', 'El correo electrónico es requerido');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showFieldError('email', 'Ingresa un correo electrónico válido');
                isValid = false;
            }
            
            // Validar contraseña
            if (!password) {
                showFieldError('password', 'La contraseña es requerida');
                isValid = false;
            } else if (password.length < 6) {
                showFieldError('password', 'La contraseña debe tener al menos 6 caracteres');
                isValid = false;
            }
            
            if (!isValid) {
                return;
            }
            
            // Mostrar loading
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ingresando...';
            
            // Simular validación (aquí iría la lógica real de autenticación)
            setTimeout(() => {
                // Por ahora, solo validamos formato y enviamos el formulario
                this.submit();
            }, 1000);
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
            const fields = ['email', 'password'];
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const errorDiv = document.getElementById(fieldId + '-error');
                
                field.classList.remove('is-invalid', 'is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            });
        }
        
        // Validar email en tiempo real
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.trim();
            
            if (email && isValidEmail(email)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('email-error').style.display = 'none';
            }
        });
        
        // Validar contraseña en tiempo real
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            
            if (password.length >= 6) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('password-error').style.display = 'none';
            }
        });
        
        // Función para validar formato de email
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Función para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('password-eye');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        // Mostrar alerta
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
