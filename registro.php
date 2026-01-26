<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - La Esquinita</title>
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
            <div class="auth-logo text-success">
                <i class="fas fa-user-plus"></i>
            </div>
            <h4 class="fw-bold mb-1">Crear Cuenta</h4>
            <p class="text-muted mb-0">Únete al Sistema La Esquinita</p>
        </div>

        <div class="auth-content">
            <!-- Alertas -->
            <div id="alert-container"></div>

            <form id="register-form">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="nombre" name="nombre" class="form-control" 
                                   placeholder="Juan" required minlength="2">
                        </div>
                        <div class="invalid-feedback" id="nombre-error"></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="apellido" name="apellido" class="form-control" 
                                   placeholder="Pérez" required minlength="2">
                        </div>
                        <div class="invalid-feedback" id="apellido-error"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" 
                               placeholder="juan.perez@ejemplo.com" required>
                    </div>
                    <div class="invalid-feedback" id="email-error"></div>
                    <div class="form-text">Usarás este correo para iniciar sesión</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="telefono" name="telefono" class="form-control" 
                               placeholder="3001234567" required minlength="10" maxlength="10">
                    </div>
                    <div class="invalid-feedback" id="telefono-error"></div>
                    <div class="form-text">Solo números, 10 dígitos</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="••••••••" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="password-error"></div>
                    <div class="form-text">Mínimo 6 caracteres</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                               placeholder="••••••••" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye" id="confirm_password-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="confirm_password-error"></div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a>
                        </label>
                        <div class="invalid-feedback" id="terms-error"></div>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" id="register-btn" class="btn btn-success btn-lg fw-bold">
                        <i class="fas fa-user-plus me-2"></i>CREAR CUENTA
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0 text-muted">
                        ¿Ya tienes cuenta? 
                        <a href="login.php" class="text-decoration-none fw-bold">Iniciar Sesión</a>
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
        // Validación del formulario de registro
        document.getElementById('register-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = {
                nombre: document.getElementById('nombre').value.trim(),
                apellido: document.getElementById('apellido').value.trim(),
                email: document.getElementById('email').value.trim(),
                telefono: document.getElementById('telefono').value.trim(),
                password: document.getElementById('password').value,
                confirm_password: document.getElementById('confirm_password').value,
                terms: document.getElementById('terms').checked
            };
            
            const registerBtn = document.getElementById('register-btn');
            
            // Limpiar errores previos
            clearErrors();
            
            let isValid = true;
            
            // Validar nombre
            if (!formData.nombre) {
                showFieldError('nombre', 'El nombre es requerido');
                isValid = false;
            } else if (formData.nombre.length < 2) {
                showFieldError('nombre', 'El nombre debe tener al menos 2 caracteres');
                isValid = false;
            } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(formData.nombre)) {
                showFieldError('nombre', 'El nombre solo puede contener letras');
                isValid = false;
            }
            
            // Validar apellido
            if (!formData.apellido) {
                showFieldError('apellido', 'El apellido es requerido');
                isValid = false;
            } else if (formData.apellido.length < 2) {
                showFieldError('apellido', 'El apellido debe tener al menos 2 caracteres');
                isValid = false;
            } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(formData.apellido)) {
                showFieldError('apellido', 'El apellido solo puede contener letras');
                isValid = false;
            }
            
            // Validar email
            if (!formData.email) {
                showFieldError('email', 'El correo electrónico es requerido');
                isValid = false;
            } else if (!isValidEmail(formData.email)) {
                showFieldError('email', 'Ingresa un correo electrónico válido');
                isValid = false;
            }
            
            // Validar teléfono
            if (!formData.telefono) {
                showFieldError('telefono', 'El teléfono es requerido');
                isValid = false;
            } else if (!/^\d{10}$/.test(formData.telefono)) {
                showFieldError('telefono', 'El teléfono debe tener exactamente 10 dígitos');
                isValid = false;
            }
            
            // Validar contraseña
            if (!formData.password) {
                showFieldError('password', 'La contraseña es requerida');
                isValid = false;
            } else if (formData.password.length < 6) {
                showFieldError('password', 'La contraseña debe tener al menos 6 caracteres');
                isValid = false;
            } else if (!isStrongPassword(formData.password)) {
                showFieldError('password', 'La contraseña debe contener al menos una letra y un número');
                isValid = false;
            }
            
            // Validar confirmación de contraseña
            if (!formData.confirm_password) {
                showFieldError('confirm_password', 'Confirma tu contraseña');
                isValid = false;
            } else if (formData.password !== formData.confirm_password) {
                showFieldError('confirm_password', 'Las contraseñas no coinciden');
                isValid = false;
            }
            
            // Validar términos y condiciones
            if (!formData.terms) {
                showFieldError('terms', 'Debes aceptar los términos y condiciones');
                isValid = false;
            }
            
            if (!isValid) {
                return;
            }
            
            // Mostrar loading
            registerBtn.disabled = true;
            registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando cuenta...';
            
            try {
                // Enviar datos a la API
                const response = await fetch('api/registro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre: formData.nombre,
                        apellido: formData.apellido,
                        email: formData.email,
                        telefono: formData.telefono,
                        password: formData.password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Cuenta creada exitosamente!',
                        text: 'Ya puedes iniciar sesión con tu nueva cuenta',
                        confirmButtonColor: '#28a745'
                    });
                    
                    window.location.href = 'login.php';
                } else {
                    throw new Error(result.message || 'Error desconocido');
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al crear la cuenta: ' + error.message, 'danger');
            } finally {
                // Rehabilitar botón
                registerBtn.disabled = false;
                registerBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>CREAR CUENTA';
            }
        });
        
        // Validaciones en tiempo real
        document.getElementById('nombre').addEventListener('input', function() {
            const nombre = this.value.trim();
            if (nombre.length >= 2 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('nombre-error').style.display = 'none';
            }
        });
        
        document.getElementById('apellido').addEventListener('input', function() {
            const apellido = this.value.trim();
            if (apellido.length >= 2 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellido)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('apellido-error').style.display = 'none';
            }
        });
        
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && isValidEmail(email)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('email-error').style.display = 'none';
            }
        });
        
        document.getElementById('telefono').addEventListener('input', function() {
            // Solo permitir números
            this.value = this.value.replace(/\D/g, '');
            
            const telefono = this.value;
            if (telefono.length === 10) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('telefono-error').style.display = 'none';
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            if (password.length >= 6 && isStrongPassword(password)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('password-error').style.display = 'none';
            }
            
            // Validar confirmación si ya tiene valor
            const confirmPassword = document.getElementById('confirm_password').value;
            if (confirmPassword && password === confirmPassword) {
                document.getElementById('confirm_password').classList.remove('is-invalid');
                document.getElementById('confirm_password').classList.add('is-valid');
                document.getElementById('confirm_password-error').style.display = 'none';
            }
        });
        
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password === confirmPassword) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                document.getElementById('confirm_password-error').style.display = 'none';
            } else if (confirmPassword) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                document.getElementById('confirm_password-error').textContent = 'Las contraseñas no coinciden';
                document.getElementById('confirm_password-error').style.display = 'block';
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
            const fields = ['nombre', 'apellido', 'email', 'telefono', 'password', 'confirm_password', 'terms'];
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const errorDiv = document.getElementById(fieldId + '-error');
                
                field.classList.remove('is-invalid', 'is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            });
        }
        
        // Función para validar formato de email
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Función para validar contraseña fuerte
        function isStrongPassword(password) {
            // Al menos una letra y un número
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /\d/.test(password);
            return hasLetter && hasNumber;
        }
        
        // Función para mostrar/ocultar contraseña
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');
            
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