# REQUISITOS DEL EXAMEN - SISTEMA POS LA ESQUINITA

## INFORMACIÓN GENERAL
- **Examen**: Desarrollador Full Stack
- **Instrumento**: Certificación CQH-2026
- **Desarrollador**: Marlon
- **País**: Guatemala
- **Moneda**: Quetzales (Q)
- **Proyecto**: Sistema POS para Abarrotería La Esquinita

## DESCRIPCIÓN DEL NEGOCIO
Abarrotería de barrio que atiende al público desde temprano. Los propietarios necesitan:
- Controlar mejor el inventario
- Detectar faltantes de productos esenciales
- Registrar ventas de forma rápida y confiable
- Implementar promociones por vencimiento cercano
- Conocer productos más vendidos por día de la semana
- Hacer pedidos con más inteligencia

## REQUISITOS FUNCIONALES

### MÓDULO: PRODUCTOS
- ✅ Registro con vencimiento, unidad de medida, proveedor
- ✅ Categorías: bebidas, enlatados, frescos, etc.
- ✅ **Promociones automáticas por vencimiento cercano**
- ✅ **Sistema de descuentos por proximidad de vencimiento**
- ✅ Promociones por combo o vencimiento

### MÓDULO: PUNTO DE VENTA
- ✅ Buscador de producto por código o nombre
- ✅ Entrada rápida por teclado
- ✅ Cálculo automático del cambio

### MÓDULO: INVENTARIO
- ✅ Control de stock en tiempo real
- ✅ Alertas por vencimiento o baja existencia
- ✅ Entradas y salidas por proveedor

### MÓDULO: CIERRE DE CAJA
- ✅ Cálculo automático de ventas al final del turno
- ✅ Reporte por método de pago (efectivo, tarjeta, vales)
- ✅ Registro de ingresos y egresos

### MÓDULO: REPORTES
- ✅ Productos más vendidos
- ✅ Horas pico de venta
- ✅ Sugerencias de pedido automático

## VENTANAS DE INTERACCIÓN SUGERIDAS
- ✅ Vista rápida de stock con filtros
- ✅ Módulo de promociones
- ✅ Facturación con escaneo simulado (input rápido)
- ✅ Gestión de clientes

## TABLAS SUGERIDAS
- ✅ productos(id, nombre, precio, cantidad, fecha_vencimiento, categoria)
- ✅ clientes(id, nombre, telefono, puntos_acumulados)
- ✅ ventas(id, id_cliente, fecha, total)
- ✅ detalle_venta(id, id_venta, id_producto, cantidad, subtotal)

## REGLAS DE DESARROLLO (MARLON)
1. **Sin emojis** en código ni commits
2. **Commits en español** con nombre "Marlon"
3. **Tecnologías**: PHP con HTML, CSS, JavaScript embebido
4. **Estructura**: Usar llaves PHP para todo
5. **Organización**:
   - Scripts SQL → `BD/scripts_sql/`
   - Tests → `tests/` (nueva carpeta)
   - APIs → `api/`
   - Controladores → `controllers/`
   - Modelos → `models/`
   - Vistas → `modulos/`

## FUNCIONALIDADES ESPECÍFICAS DE PROMOCIONES

### SISTEMA DE DESCUENTOS POR VENCIMIENTO
- ✅ **Detección automática** de productos próximos a vencer
- ✅ **Aplicación automática de descuentos** según días restantes:
  - 30-60 días: 10% descuento
  - 15-29 días: 20% descuento  
  - 7-14 días: 30% descuento
  - 1-6 días: 50% descuento
- ✅ **Alertas visuales** en POS para productos con descuento
- ✅ **Cálculo automático** del precio con descuento en el ticket
- ✅ **Reporte de productos** con descuento aplicado

### GESTIÓN DE PROMOCIONES (SOLO ADMINISTRADOR)
- ✅ **Crear promociones** por vencimiento
- ✅ **Modificar porcentajes** de descuento
- ✅ **Activar/desactivar** promociones
- ✅ **Promociones por combo** (2x1, 3x2, etc.)
- ✅ **Historial de promociones** aplicadas

### ADMINISTRADOR
- ✅ Acceso completo a todos los módulos
- ✅ Gestión de usuarios (crear cuentas para cajeros)
- ✅ Cierre de caja
- ✅ Reportes avanzados
- ✅ **Configuración de promociones por vencimiento**
- ✅ **Aplicar descuentos automáticos a productos próximos a vencer**
- ✅ **Gestión de promociones por combo**
- ✅ Gestión de proveedores
- ✅ Control total del inventario

### CAJERO
- ✅ Punto de venta (POS)
- ✅ Consulta de inventario
- ✅ Registro de productos básico
- ✅ Atención al cliente
- ❌ No puede crear usuarios
- ❌ No puede cerrar caja
- ❌ No puede ver reportes administrativos

## PROBLEMAS IDENTIFICADOS A RESOLVER

### ESTRUCTURALES
1. **Modal duplicado** `#modalCobrar` en pos.php y footer.php
2. **Archivos faltantes**: clientes.php, caja.php
3. **Referencias rotas**: layout/header.php, layout/footer.php
4. **Variable $vista** no definida en header.php
5. **Funciones JS** sin definir en pos.php y footer.php

### BASE DE DATOS
1. **Tablas faltantes** según requisitos del examen
2. **Estructura actual** no coincide con tablas sugeridas
3. **Relaciones** entre tablas no definidas

### FUNCIONALIDADES
1. **Sistema de autenticación** con roles
2. **Módulos completos** faltantes (70% del sistema)
3. **APIs backend** para operaciones CRUD
4. **Validaciones** frontend y backend

## PLAN DE IMPLEMENTACIÓN

### FASE 1: LIMPIEZA Y ESTRUCTURA
1. Arreglar duplicados y problemas estructurales
2. Crear carpeta tests/
3. Organizar scripts SQL
4. Definir base de datos completa
5. Sistema de roles y autenticación

### FASE 2: FUNCIONALIDADES CORE
1. Módulo productos completo
2. POS funcional con todas las características
3. Inventario con alertas y control
4. APIs backend necesarias

### FASE 3: MÓDULOS AVANZADOS
1. Cierre de caja completo
2. Sistema de reportes
3. Módulo de promociones
4. Gestión de clientes y usuarios

### FASE 4: TESTING Y OPTIMIZACIÓN
1. Tests unitarios
2. Tests de integración
3. Optimización de rendimiento
4. Documentación final

## CONTEXTO GUATEMALTECO
- **Moneda**: Quetzales (Q)
- **Horarios**: Abarrotería de barrio (temprano en la mañana)
- **Productos típicos**: Bebidas, enlatados, frescos, abarrotes básicos
- **Métodos de pago**: Efectivo (principal), tarjeta, vales
- **Cultura de compra**: Compras diarias, productos de primera necesidad

## TECNOLOGÍAS CONFIRMADAS
- **Backend**: PHP 8+ con PDO
- **Frontend**: HTML5, CSS3, JavaScript (embebido en PHP)
- **Base de datos**: MySQL
- **Framework CSS**: Bootstrap 5
- **Iconos**: Font Awesome
- **Gráficos**: ApexCharts
- **Alertas**: SweetAlert2

## ESTRUCTURA DE ARCHIVOS OBJETIVO
```
La_Esquinita/
├── BD/
│   ├── scripts_sql/
│   │   ├── 01_crear_tablas.sql
│   │   ├── 02_insertar_datos.sql
│   │   └── 03_procedimientos.sql
├── tests/
│   ├── unit/
│   └── integration/
├── api/
│   ├── productos.php
│   ├── ventas.php
│   ├── inventario.php
│   └── usuarios.php
├── controllers/
├── models/
├── modulos/
├── assets/
├── config/
└── layout/
```

## NOTAS IMPORTANTES
- Mantener consistencia en el estilo de código PHP embebido
- Usar nomenclatura en español para variables y funciones
- Implementar validaciones tanto en frontend como backend
- Seguir patrones de seguridad para prevenir SQL injection
- Optimizar consultas para rendimiento en tiempo real