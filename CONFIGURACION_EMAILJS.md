# 📧 Configuración de EmailJS para Recuperación de Contraseñas

## ✅ Estado Actual
- ✅ Base de datos configurada (tabla `password_resets` creada)
- ✅ API backend implementada (`api/password-reset.php`)
- ✅ Frontend integrado con EmailJS
- ✅ Página de nueva contraseña funcional (`nueva_clave.php`)
- ⚠️ **PENDIENTE**: Configurar credenciales de EmailJS

## 🔧 Pasos para Completar la Configuración

### 1. Obtener tu Public Key de EmailJS

1. Ve a tu dashboard de EmailJS: https://dashboard.emailjs.com/
2. En el menú izquierdo, haz clic en **"Account"**
3. En la sección **"API Keys"**, copia tu **Public Key**

### 2. Crear Template de Email

1. En tu dashboard de EmailJS, ve a **"Email Templates"**
2. Haz clic en **"Create New Template"**
3. Usa el **Template ID**: `template_recovery`
4. Configura el template con este contenido:

**Subject:**
```
🔐 Recuperación de Contraseña - {{company_name}}
```

**Content:**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .button { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Recuperación de Contraseña</h1>
        </div>
        
        <div class="content">
            <h2>Hola {{to_name}},</h2>
            
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>{{company_name}}</strong>.</p>
            
            <p>Si fuiste tú quien solicitó este cambio, haz clic en el siguiente botón:</p>
            
            <div style="text-align: center;">
                <a href="{{reset_link}}" class="button">🔓 Restablecer Contraseña</a>
            </div>
            
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este enlace expira en <strong>1 hora</strong></li>
                <li>Solo puede ser usado una vez</li>
                <li>Si no solicitaste este cambio, ignora este email</li>
            </ul>
            
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
            <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px;">
                {{reset_link}}
            </p>
        </div>
        
        <div class="footer">
            <p>Este email fue enviado automáticamente por {{company_name}}</p>
            <p>Si necesitas ayuda, contacta a: {{support_email}}</p>
            <p>&copy; {{current_year}} {{company_name}}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
```

### 3. Actualizar Configuración

Edita el archivo `api/email-config.js` y reemplaza:

```javascript
PUBLIC_KEY: 'TU_PUBLIC_KEY_AQUI'
```

Por tu Public Key real de EmailJS.

### 4. Verificar Service ID

Confirma que tu Service ID en EmailJS sea exactamente: `service_n364nyr`

Si es diferente, actualiza el archivo `api/email-config.js`:

```javascript
SERVICE_ID: 'tu_service_id_real'
```

## 🧪 Probar el Sistema

### Prueba Completa:

1. **Ir a la página de recuperación:**
   ```
   http://localhost/La_Esquinita/recuperar.php
   ```

2. **Ingresar un email válido** que exista en tu tabla `usuarios`

3. **Verificar que se envía el email** (revisar bandeja de entrada y spam)

4. **Hacer clic en el enlace del email** para ir a `nueva_clave.php`

5. **Cambiar la contraseña** y verificar que funciona el login

### Prueba de API (Opcional):

Puedes probar la API directamente con herramientas como Postman:

```bash
POST http://localhost/La_Esquinita/api/password-reset.php
Content-Type: application/json

{
    "action": "request_reset",
    "email": "usuario@ejemplo.com"
}
```

## 🔍 Solución de Problemas

### Error: "TU_PUBLIC_KEY_AQUI"
- **Causa**: No has actualizado la configuración
- **Solución**: Reemplaza `TU_PUBLIC_KEY_AQUI` con tu Public Key real

### Error: "Template not found"
- **Causa**: El template no existe o tiene ID diferente
- **Solución**: Crear template con ID exacto `template_recovery`

### Error: "Service not found"
- **Causa**: Service ID incorrecto
- **Solución**: Verificar que sea `service_n364nyr` o actualizar

### Email no llega
- **Causa**: Configuración incorrecta o email en spam
- **Solución**: 
  1. Revisar carpeta de spam
  2. Verificar configuración de EmailJS
  3. Comprobar que el email existe en la base de datos

### Token inválido o expirado
- **Causa**: Token usado o más de 1 hora transcurrida
- **Solución**: Solicitar nuevo token de recuperación

## 📋 Checklist Final

- [ ] Public Key configurado en `api/email-config.js`
- [ ] Template `template_recovery` creado en EmailJS
- [ ] Service ID verificado (`service_n364nyr`)
- [ ] Tabla `password_resets` creada en la base de datos
- [ ] Prueba completa realizada exitosamente

## 🎉 ¡Listo!

Una vez completados estos pasos, tu sistema de recuperación de contraseñas estará completamente funcional con:

- ✅ Seguridad con tokens que expiran
- ✅ Emails profesionales con HTML
- ✅ Validación completa de formularios
- ✅ Interfaz de usuario moderna
- ✅ API robusta y segura

¡Tu sistema POS ahora tiene recuperación de contraseñas profesional! 🚀