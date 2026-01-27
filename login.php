<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>
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
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --body-bg: #f5f7fb;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        .auth-body {
            background: linear-gradient(135deg, var(--primary-color), #0a58ca);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .auth-header {
            background: var(--light-color);
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .auth-logo {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .auth-content {
            padding: 30px;
        }

        .auth-footer {
            text-align: center;
            padding: 20px;
            background: var(--light-color);
            font-size: 0.9rem;
            border-top: 1px solid var(--border-color);
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
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
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control" id="username" placeholder="admin@laesquinita.com"
                            value="admin@laesquinita.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Contraseña</label>
                        <a href="forgot-password.html" class="text-xs text-decoration-none">¿Olvidaste tu
                            contraseña?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" placeholder="••••••••" value="password" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btnLogin">
                        <span id="btnText">INGRESAR</span> <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
            
            <div id="alertContainer" class="mt-3"></div>
        </div>

        <div class="auth-footer">
            <p class="mb-0 text-muted">&copy; 2026 Abarrotera La Esquinita</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Manejar envío del formulario
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const btnLogin = document.getElementById('btnLogin');
            const btnText = document.getElementById('btnText');
            
            if (!username || !password) {
                mostrarAlerta('Por favor completa todos los campos', 'warning');
                return;
            }
            
            // Mostrar loading
            btnLogin.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Iniciando...';
            
            try {
                const response = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta('¡Login exitoso! Redirigiendo...', 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    mostrarAlerta(data.message || 'Credenciales incorrectas', 'danger');
                }
                
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión. Intenta nuevamente.', 'danger');
            } finally {
                // Restaurar botón
                btnLogin.disabled = false;
                btnText.innerHTML = 'INGRESAR';
            }
        });
        
        // Mostrar alertas
        function mostrarAlerta(mensaje, tipo) {
            const container = document.getElementById('alertContainer');
            const alertClass = tipo === 'success' ? 'alert-success' : 
                              tipo === 'warning' ? 'alert-warning' : 'alert-danger';
            
            container.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Auto-ocultar después de 4 segundos
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 4000);
        }
        
        // Focus automático en el campo usuario
        document.getElementById('username').focus();
        
        // Enter en username pasa a password
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });
        
        // Datos de prueba para desarrollo
        console.log('Usuarios de prueba:');
        console.log('Admin: admin@laesquinita.com / password');
        console.log('Usuario: mj3u7000@hotmail.com / password');
    </script>
</body>

</html>