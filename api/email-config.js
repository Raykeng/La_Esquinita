/**
 * Configuración de EmailJS para La Esquinita
 * API gratuita para envío de emails
 */

// Configuración de EmailJS (reemplaza con tus datos reales)
const EMAIL_CONFIG = {
    SERVICE_ID: 'service_n364nyr', // Tu Service ID de Gmail
    TEMPLATE_ID: 'template_mx7ryeq', // Tu template de recuperación
    PUBLIC_KEY: 'cFDVZ8Smd9_ZJEk1q', // Tu Public Key de EmailJS
    
    // Configuración del remitente
    FROM_NAME: 'La Esquinita - Sistema POS',
    FROM_EMAIL: 'noreply@laesquinita.com'
};

/**
 * Inicializar EmailJS
 */
function initEmailJS() {
    // Cargar la librería de EmailJS si no está cargada
    if (typeof emailjs === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js';
        script.onload = () => {
            emailjs.init(EMAIL_CONFIG.PUBLIC_KEY);
            console.log('EmailJS inicializado correctamente');
        };
        document.head.appendChild(script);
    } else {
        emailjs.init(EMAIL_CONFIG.PUBLIC_KEY);
    }
}

/**
 * Enviar email de recuperación de contraseña
 * @param {string} userEmail - Email del usuario
 * @param {string} userName - Nombre del usuario
 * @param {string} resetToken - Token de recuperación
 * @param {string} resetLink - Link completo de recuperación
 */
async function sendPasswordResetEmail(userEmail, userName, resetToken, resetLink) {
    try {
        // Parámetros del template
        const templateParams = {
            to_email: userEmail,
            to_name: userName,
            from_name: EMAIL_CONFIG.FROM_NAME,
            reset_link: resetLink,
            reset_token: resetToken,
            company_name: 'La Esquinita',
            support_email: 'soporte@laesquinita.com',
            current_year: new Date().getFullYear()
        };

        // Enviar email usando EmailJS
        const response = await emailjs.send(
            EMAIL_CONFIG.SERVICE_ID,
            EMAIL_CONFIG.TEMPLATE_ID,
            templateParams
        );

        console.log('Email enviado exitosamente:', response);
        return {
            success: true,
            message: 'Email de recuperación enviado correctamente',
            messageId: response.text
        };

    } catch (error) {
        console.error('Error enviando email:', error);
        return {
            success: false,
            message: 'Error al enviar el email: ' + error.text || error.message,
            error: error
        };
    }
}

/**
 * Validar email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', initEmailJS);