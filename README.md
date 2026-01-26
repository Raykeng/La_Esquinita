# Abarrotería "La Esquinita" - Sistema POS

Sistema de punto de venta desarrollado para la gestión diaria de una abarrotería. Incluye control de inventario, ventas, caja y reportes.

---

## Estructura del Proyecto

```
La_Esquinita/
├── BD/
│   ├── la_esquinita_optimizada.sql
│   └── GUIA_ACCESO_BD.md
└── README.md
```

---

## Base de Datos

### Instalación

El sistema utiliza MySQL 8.4.3 a través de Laragon.

**Nombre de la base de datos:** `la_esquinita`

### Usuario por defecto

```
Email:      admin@laesquinita.com
Contraseña: admin123
Rol:        Administrador
```

**Importante:** Recuerda cambiar esta contraseña antes de poner el sistema en producción.

### Cómo instalar la base de datos

Abre PowerShell y ejecuta:

```bash
cd c:\laragon\www\La_Esquinita\BD
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root < la_esquinita_optimizada.sql
```

### Acceso a la base de datos

Hay varias formas de trabajar con la base de datos:

- **phpMyAdmin:** Abre tu navegador y ve a `http://localhost/phpmyadmin`
- **HeidiSQL:** Viene incluido con Laragon, haz clic en el botón "Database"
- **Línea de comandos:** Usa el cliente MySQL desde la terminal
- **Archivos:** Se guardan en `C:\laragon\bin\mysql\mysql-8.4.3-winx64\data\la_esquinita\`

Para más detalles sobre cómo usar cada método, revisa el archivo [GUIA_ACCESO_BD.md](BD/GUIA_ACCESO_BD.md).

---

## Características

La base de datos incluye:

- **19 tablas** para gestionar todo el negocio
- **5 roles** con diferentes niveles de acceso
- **30 permisos** para control detallado
- **3 vistas** para consultas frecuentes
- **Procedimientos almacenados** para operaciones comunes
- **Triggers** para alertas automáticas de stock

---

## Módulos

El sistema está organizado en los siguientes módulos:

1. **Usuarios y Roles** - Gestión de accesos y permisos
2. **Productos e Inventario** - Control de stock y alertas
3. **Ventas** - Registro de transacciones
4. **Caja** - Manejo de efectivo y turnos
5. **Compras** - Gestión de proveedores y pedidos
6. **Clientes** - Registro de clientes frecuentes
7. **Reportes** - Estadísticas y análisis
8. **Configuración** - Ajustes generales del sistema

---

## Próximos pasos

- Desarrollar la interfaz web del sistema
- Crear las APIs para conectar con la base de datos
- Implementar el sistema de login
- Diseñar las pantallas de usuario
- Configurar la impresora de tickets

---

## Documentación

- [Guía de Acceso a la Base de Datos](BD/GUIA_ACCESO_BD.md) - Instrucciones detalladas
- Script SQL: `BD/la_esquinita_optimizada.sql`

---

**Versión:** 1.0  
**Última actualización:** Enero 2026
