# Tests - Sistema POS La Esquinita

Esta carpeta contiene todos los tests del sistema.

## Estructura

```
tests/
├── unit/           # Tests unitarios
├── integration/    # Tests de integración
└── fixtures/       # Datos de prueba
```

## Tipos de Tests

### Tests Unitarios
- Modelos (Producto, Cliente, Venta)
- Controladores
- Funciones utilitarias

### Tests de Integración
- APIs completas
- Flujos de trabajo completos
- Interacciones entre módulos

## Ejecutar Tests

```bash
# Todos los tests
php tests/run_all_tests.php

# Tests específicos
php tests/unit/ProductoTest.php
```

## Convenciones

- Archivos terminan en `Test.php`
- Clases terminan en `Test`
- Métodos empiezan con `test`
- Usar datos de fixtures para consistencia