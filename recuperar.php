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
    
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
       (function(){
          // INICIALIZAR EMAILJS
          // Tienes que registrarte en https://www.emailjs.com/
          // Obtener tu "Public Key" y pegarla aquí abajo donde dice "TU_PUBLIC_KEY"
          // emailjs.init("TU_PUBLIC_KEY");
       })();
    </script>
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
            <form id="recovery-form" onsubmit="enviarCorreo(event)">
                <div class="alert alert-info text-sm">
                    <i class="fas fa-info-circle me-1"></i> Te enviaremos un código o enlace a tu correo.
                </div>

                <div class="mb-4">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" class="form-control" placeholder="ejemplo@correo.com" required>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" id="btn-submit" class="btn btn-warning btn-lg fw-bold text-white">
                        ENVIAR ENLACE
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

    <script>
        function enviarCorreo(event) {
            event.preventDefault();
            const btn = document.getElementById('btn-submit');
            const email = document.getElementById('email').value;

            // Simulación o Envío Real
            btn.innerHTML = 'Enviando...';
            btn.disabled = true;

            // Lógica EmailJS (Descomentar y configurar para usar real)
            /*
            var templateParams = {
                to_email: email,
                from_name: "La Esquinita POS",
                message_html: "Haga clic aquí para restablecer: <a href='http://localhost/La_esquinita/La_Esquinita/nueva_clave.php'>Restablecer</a>"
            };

            emailjs.send('YOUR_SERVICE_ID', 'YOUR_TEMPLATE_ID', templateParams)
                .then(function(response) {
                   console.log('SUCCESS!', response.status, response.text);
                   alert('Correo enviado exitosamente (Revisa SPAM si no llega).');
                   window.location.href = 'nueva_clave.php'; // Redirigir para probar
                }, function(error) {
                   console.log('FAILED...', error);
                   alert('Error al enviar correo: ' + JSON.stringify(error));
                   btn.disabled = false;
                   btn.innerHTML = 'ENVIAR ENLACE';
                });
            */
            
            // SIMULACIÓN POR AHORA (Ya que falta la API KEY)
            setTimeout(() => {
                alert('¡Simulación! Correo enviado a ' + email + '.\n\n(Debes configurar EmailJS en el código para envío real).');
                // Redirigimos a la pantalla de "Nueva Contraseña" para que el usuario vea el flujo
                window.location.href = 'nueva_clave.php'; 
            }, 1500);
        }
    </script>
</body>
</html>
