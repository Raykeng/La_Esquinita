<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Recuperación - La Esquinita</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-body">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-tools me-2"></i>Panel de Pruebas - Recuperación de Contraseñas</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Generar Token -->
                        <div class="mb-4">
                            <h5><i class="fas fa-key me-2"></i>1. Generar Token de Recuperación</h5>
                            <p class="text-muted">Genera un token para el email: <strong>mj3u7000@hotmail.com</strong></p>
                            <button id="generate-token" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Generar Token
                            </button>
                            <div id="token-result" class="mt-3"></div>
                        </div>

                        <hr>

                        <!-- Enlace Directo -->
                        <div class="mb-4">
                            <h5><i class="fas fa-link me-2"></i>2. Enlace Directo (Sin esperar email)</h5>
                            <p class="text-muted">Usa este enlace para probar directamente:</p>
                            <div id="direct-link" class="alert alert-info" style="display: none;">
                                <strong>Enlace de prueba:</strong><br>
                                <a id="test-link" href="#" target="_blank" class="btn btn-primary mt-2">
                                    <i class="fas fa-external-link-alt me-2"></i>Ir a Cambiar Contraseña
                                </a>
                            </div>
                        </div>

                        <hr>

                        <!-- Información -->
                        <div class="mb-4">
                            <h5><i class="fas fa-info-circle me-2"></i>3. Información del Sistema</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>📧 Email de Prueba:</h6>
                                            <code>mj3u7000@hotmail.com</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>⏰ Tiempo de Expiración:</h6>
                                            <code>1 hora</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enlaces Útiles -->
                        <div class="mb-4">
                            <h5><i class="fas fa-external-link-alt me-2"></i>4. Enlaces Útiles</h5>
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="recuperar.php" class="btn btn-outline-warning">
                                    <i class="fas fa-key me-2"></i>Página de Recuperación
                                </a>
                                <a href="login.php" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Página de Login
                                </a>
                                <a href="index.php" class="btn btn-outline-success">
                                    <i class="fas fa-home me-2"></i>Inicio
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('generate-token').addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';

            try {
                const response = await fetch('api/password-reset.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'request_reset',
                        email: 'mj3u7000@hotmail.com'
                    })
                });

                const result = await response.json();

                if (result.success && result.data) {
                    // Mostrar resultado
                    document.getElementById('token-result').innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>Token generado exitosamente</h6>
                            <p><strong>Token:</strong> <code>${result.data.reset_token}</code></p>
                            <p><strong>Expira:</strong> ${result.data.expires_at}</p>
                        </div>
                    `;

                    // Mostrar enlace directo
                    const directLink = document.getElementById('direct-link');
                    const testLink = document.getElementById('test-link');
                    
                    testLink.href = result.data.reset_link;
                    directLink.style.display = 'block';

                    Swal.fire({
                        icon: 'success',
                        title: '¡Token generado!',
                        text: 'Ahora puedes usar el enlace directo para probar',
                        confirmButtonColor: '#28a745'
                    });

                } else {
                    throw new Error(result.message || 'Error desconocido');
                }

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('token-result').innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Error</h6>
                        <p>${error.message}</p>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus me-2"></i>Generar Token';
            }
        });
    </script>
</body>
</html>