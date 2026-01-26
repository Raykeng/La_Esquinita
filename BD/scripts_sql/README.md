# 📁 Scripts SQL - La Esquinita

Esta carpeta contiene todos los scripts SQL necesarios para configurar y mantener la base de datos del sistema.

## 📋 **Orden de Ejecución:**

### 1. **Base de Datos Principal**
```sql
-- Ejecutar primero el script principal de la base de datos
source ../la_esquinita_optimizada.sql
```

### 2. **Tabla de Recuperación de Contraseñas**
```sql
-- Crear tabla para tokens de recuperación
source crear_tabla_password_resets.sql
```

### 3. **Usuario de Prueba (Opcional)**
```sql
-- Crear usuario para pruebas de recuperación
source crear_usuario_prueba.sql
```

## 📄 **Descripción de Archivos:**

### `crear_tabla_password_resets.sql`
- **Propósito:** Crear tabla para tokens de recuperación de contraseñas
- **Dependencias:** Tabla `usuarios` debe existir
- **Características:**
  - Tokens únicos de 64 caracteres
  - Expiración automática (1 hora)
  - Relación con tabla usuarios
  - Índices optimizados

### `crear_usuario_prueba.sql`
- **Propósito:** Crear usuario de prueba para testing
- **Email:** `mj3u7000@hotmail.com`
- **Rol:** Cajero (rol_id = 2)
- **Contraseña:** `password` (hash incluido)

## 🔧 **Comandos de Ejecución:**

### Desde MySQL Command Line:
```bash
# Navegar a la carpeta
cd /path/to/La_Esquinita/BD/scripts_sql/

# Ejecutar scripts
mysql -u root -p < crear_tabla_password_resets.sql
mysql -u root -p < crear_usuario_prueba.sql
```

### Desde XAMPP:
```bash
# Usar la ruta completa de XAMPP
C:\xampp\mysql\bin\mysql.exe -u root -e "source crear_tabla_password_resets.sql"
C:\xampp\mysql\bin\mysql.exe -u root -e "source crear_usuario_prueba.sql"
```

## 📤 **Para tu Compañero:**

1. **Clonar el repositorio**
2. **Ejecutar scripts en orden:**
   - Base de datos principal
   - Scripts de esta carpeta
3. **Verificar que todo funcione**

## 🧪 **Testing:**

Después de ejecutar los scripts, puedes probar:
- **Recuperación de contraseñas:** `http://localhost/La_Esquinita/test_recovery.php`
- **Login normal:** `http://localhost/La_Esquinita/login.php`

## 📝 **Notas:**

- Todos los scripts son **idempotentes** (se pueden ejecutar múltiples veces)
- Incluyen verificaciones de existencia
- Compatibles con MySQL 5.7+ y MariaDB 10.3+