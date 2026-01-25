# 🏪 Abarrotería "La Esquinita" - Sistema POS

Sistema de Punto de Venta para Abarrotería

---

## 📁 Estructura del Proyecto

```
La_Esquinita/
├── BD/
│   └── la_esquinita_database.sql    # Script de base de datos
├── (otros archivos del proyecto)
```

---

## 🗄️ Base de Datos

### Instalación

La base de datos ya está instalada y configurada en MySQL.

**Nombre:** `la_esquinita`

### Credenciales de Acceso

```
Email:      admin@laesquinita.com
Contraseña: admin123
Rol:        Administrador
```

> ⚠️ **IMPORTANTE:** Cambiar esta contraseña antes de usar en producción.

### Reinstalar Base de Datos

Si necesitas reinstalar la base de datos:

```bash
# Desde la carpeta BD
cd c:\laragon\www\La_Esquinita\BD

# Ejecutar script
C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root < la_esquinita_database.sql
```

---

## 📊 Características de la Base de Datos

- ✅ **19 Tablas** - Gestión completa del negocio
- ✅ **5 Roles** - Sistema de permisos robusto
- ✅ **30 Permisos** - Control granular de acceso
- ✅ **3 Vistas** - Consultas optimizadas
- ✅ **Procedimientos Almacenados** - Lógica de negocio
- ✅ **Triggers** - Automatización de alertas

---

## 🎯 Módulos Incluidos

1. **Usuarios y Roles** - Control de acceso
2. **Productos e Inventario** - Gestión de stock
3. **Ventas** - Registro de transacciones
4. **Caja** - Control de efectivo
5. **Compras** - Gestión de proveedores
6. **Clientes** - Base de datos de clientes
7. **Reportes** - Análisis de negocio
8. **Configuración** - Ajustes del sistema

---

## 🚀 Próximos Pasos

1. Desarrollar el frontend del sistema POS
2. Crear APIs para conectar con la base de datos
3. Implementar sistema de autenticación
4. Diseñar interfaz de usuario
5. Configurar impresora de tickets

---

## 📚 Documentación

Para documentación completa, consultar los archivos en la carpeta de artifacts.

---

**Versión:** 1.0  
**Fecha:** 2026-01-23
