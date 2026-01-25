# 🗄️ Guía de Acceso a la Base de Datos - La Esquinita

## 📍 Ubicaciones de la Base de Datos

### 1️⃣ **phpMyAdmin (Interfaz Web)**

La forma más fácil de administrar la base de datos visualmente.

**URL de Acceso:**
```
http://localhost/phpmyadmin
```

**Credenciales:**
- **Usuario:** `root`
- **Contraseña:** *(dejar vacío)*

**Pasos:**
1. Abre tu navegador favorito (Chrome, Edge, Firefox)
2. Escribe: `http://localhost/phpmyadmin`
3. Inicia sesión con usuario `root` (sin contraseña)
4. En el panel izquierdo, busca y haz clic en `la_esquinita`
5. Verás todas las tablas de la base de datos

**Captura de pantalla esperada:**
```
┌─────────────────────────────────────────┐
│ phpMyAdmin                              │
├─────────────────────────────────────────┤
│ Bases de datos:                         │
│  ├─ information_schema                  │
│  ├─ la_esquinita ◄── AQUÍ              │
│  ├─ mysql                               │
│  └─ performance_schema                  │
│                                         │
│ Tablas en la_esquinita:                │
│  ├─ auditoria                          │
│  ├─ cajas                              │
│  ├─ categorias                         │
│  ├─ clientes                           │
│  ├─ compras                            │
│  └─ ... (19 tablas en total)          │
└─────────────────────────────────────────┘
```

---

### 2️⃣ **HeidiSQL (Herramienta de Laragon)**

HeidiSQL es una herramienta más avanzada incluida en Laragon.

**Cómo Acceder:**
1. Abre **Laragon** (la ventana principal)
2. Haz clic en el botón **"Database"** o **"MySQL"**
3. Se abrirá **HeidiSQL** automáticamente
4. La conexión a MySQL ya estará configurada
5. Busca `la_esquinita` en el árbol de bases de datos

**Ventajas de HeidiSQL:**
- ✅ Más rápido que phpMyAdmin
- ✅ Mejor para consultas SQL complejas
- ✅ Exportación/importación más eficiente
- ✅ Editor SQL con autocompletado

---

### 3️⃣ **Línea de Comandos (MySQL CLI)**

Para usuarios avanzados que prefieren la terminal.

**Ruta del ejecutable:**
```
C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe
```

**Conectar a la base de datos:**
```bash
# Opción 1: Desde PowerShell
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root la_esquinita

# Opción 2: Si MySQL está en el PATH
mysql -u root la_esquinita
```

**Comandos útiles:**
```sql
-- Ver todas las tablas
SHOW TABLES;

-- Ver estructura de una tabla
DESCRIBE usuarios;

-- Consultar datos
SELECT * FROM roles;

-- Ver todos los usuarios
SELECT id, nombre_completo, email FROM usuarios;
```

---

### 4️⃣ **Archivos Físicos de la Base de Datos**

Los archivos reales de MySQL están almacenados aquí:

**Ruta:**
```
C:\laragon\bin\mysql\mysql-8.4.3-winx64\data\la_esquinita\
```

**Contenido:**
```
la_esquinita/
├── auditoria.ibd
├── cajas.ibd
├── categorias.ibd
├── clientes.ibd
├── compras.ibd
├── configuracion.ibd
├── detalle_compras.ibd
├── detalle_ventas.ibd
├── movimientos_caja.ibd
├── movimientos_inventario.ibd
├── pagos_venta.ibd
├── permisos.ibd
├── productos.ibd
├── proveedores.ibd
├── rol_permisos.ibd
├── roles.ibd
├── turnos_caja.ibd
├── usuarios.ibd
└── ventas.ibd
```

> ⚠️ **ADVERTENCIA:** No modifiques estos archivos directamente. Usa phpMyAdmin, HeidiSQL o MySQL CLI.

---

## 🔧 Verificar que la Base de Datos Existe

### Desde PowerShell:

```powershell
# Ver todas las bases de datos
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "SHOW DATABASES;"

# Ver tablas de la_esquinita
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE la_esquinita; SHOW TABLES;"

# Ver roles creados
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE la_esquinita; SELECT * FROM roles;"
```

---

## 📊 Consultas Rápidas desde phpMyAdmin

Una vez dentro de phpMyAdmin, puedes ejecutar estas consultas en la pestaña **SQL**:

### Ver todos los roles:
```sql
SELECT * FROM roles;
```

### Ver todos los usuarios:
```sql
SELECT id, nombre_completo, email, rol_id, estado FROM usuarios;
```

### Ver permisos de un rol:
```sql
SELECT r.nombre as rol, p.modulo, p.accion, p.descripcion
FROM roles r
INNER JOIN rol_permisos rp ON r.id = rp.rol_id
INNER JOIN permisos p ON rp.permiso_id = p.id
WHERE r.id = 1  -- Cambiar ID según el rol
ORDER BY p.modulo, p.accion;
```

### Ver productos con stock bajo:
```sql
SELECT * FROM v_productos_stock_bajo;
```

### Ver ventas de hoy:
```sql
SELECT * FROM v_ventas_hoy;
```

---

## 🔐 Cambiar Contraseña del Administrador

Desde phpMyAdmin o HeidiSQL:

```sql
-- Generar hash de nueva contraseña en PHP
-- Usa este código PHP para generar el hash:
<?php
echo password_hash('tu_nueva_contraseña', PASSWORD_BCRYPT);
?>

-- Luego actualiza en la base de datos:
UPDATE usuarios 
SET password_hash = '$2y$10$TU_HASH_GENERADO_AQUI'
WHERE email = 'admin@laesquinita.com';
```

---

## 🚀 Respaldo de la Base de Datos

### Desde phpMyAdmin:
1. Selecciona la base de datos `la_esquinita`
2. Haz clic en la pestaña **"Exportar"**
3. Selecciona **"Método rápido"** o **"Personalizado"**
4. Haz clic en **"Continuar"**
5. Se descargará un archivo `.sql`

### Desde Línea de Comandos:
```bash
# Crear respaldo
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe" -u root la_esquinita > backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql

# Restaurar respaldo
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root la_esquinita < backup_20260123_223000.sql
```

---

## 🆘 Solución de Problemas

### No puedo acceder a phpMyAdmin

**Problema:** `http://localhost/phpmyadmin` no carga

**Soluciones:**
1. Verifica que Apache esté corriendo en Laragon
2. Verifica que MySQL esté corriendo en Laragon
3. Intenta: `http://127.0.0.1/phpmyadmin`
4. Reinicia Laragon (Stop All → Start All)

### Error: "Access denied for user 'root'"

**Solución:**
```sql
-- Desde MySQL CLI como administrador
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;
```

### La base de datos no aparece

**Verificar:**
```bash
# Ver si existe
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "SHOW DATABASES LIKE 'la_esquinita';"

# Si no existe, reinstalar
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root < "c:\laragon\www\La_Esquinita\BD\la_esquinita_database.sql"
```

---

## 📱 Acceso Remoto (Opcional)

Si quieres acceder desde otro dispositivo en tu red local:

1. Encuentra tu IP local: `ipconfig` en PowerShell
2. Configura MySQL para aceptar conexiones remotas
3. Accede desde otro dispositivo: `http://TU_IP/phpmyadmin`

---

## 📚 Recursos Adicionales

- **Documentación MySQL:** https://dev.mysql.com/doc/
- **Documentación phpMyAdmin:** https://www.phpmyadmin.net/docs/
- **Documentación HeidiSQL:** https://www.heidisql.com/help.php

---

**Última actualización:** 2026-01-23  
**Base de Datos:** la_esquinita  
**Versión MySQL:** 8.4.3
