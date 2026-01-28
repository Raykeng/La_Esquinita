-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-01-2026 a las 21:43:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `la_esquinita`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 'Bebidas', 'Refrescos, jugos, agua', 'activo', '2026-01-26 05:31:13'),
(2, 'Enlatados', 'Conservas, frijoles, verduras', 'activo', '2026-01-26 05:31:13'),
(3, 'Frescos', 'Frutas, verduras, perecederos', 'activo', '2026-01-26 05:31:13'),
(4, 'Abarrotes', 'Arroz, frijol, az??car, aceite', 'activo', '2026-01-26 05:31:13'),
(5, 'L??cteos', 'Leche, queso, crema', 'activo', '2026-01-26 05:31:13'),
(6, 'Panader??a', 'Pan dulce, franc??s', 'activo', '2026-01-26 05:31:13'),
(7, 'Limpieza', 'Jab??n, cloro, detergente', 'activo', '2026-01-26 05:31:13'),
(8, 'Botanas', 'Frituras, galletas', 'activo', '2026-01-26 05:31:13'),
(9, 'Higiene', 'Papel, pasta dental, champ??', 'activo', '2026-01-26 05:31:13'),
(10, 'Congelados', 'Helados, hielos, carnes', 'activo', '2026-01-26 05:31:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierres_caja`
--

CREATE TABLE `cierres_caja` (
  `id` int(11) NOT NULL,
  `fecha_cierre` date NOT NULL,
  `turno` enum('matutino','vespertino','nocturno','Diurno') DEFAULT 'Diurno',
  `total_ventas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_efectivo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_tarjeta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_vales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ingresos_adicionales` decimal(10,2) DEFAULT 0.00,
  `egresos` decimal(10,2) DEFAULT 0.00,
  `total_final` decimal(10,2) NOT NULL DEFAULT 0.00,
  `diferencia` decimal(10,2) DEFAULT 0.00,
  `usuario` varchar(50) NOT NULL,
  `detalles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalles`)),
  `notas` text DEFAULT NULL,
  `estado` enum('abierto','cerrado') DEFAULT 'cerrado',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `nit`, `direccion`, `estado`, `fecha_creacion`) VALUES
(1, 'P??blico General', NULL, NULL, NULL, 'activo', '2026-01-26 05:31:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_movimiento` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','merma') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT 0.00,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `fecha_movimiento` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`id`, `producto_id`, `tipo_movimiento`, `cantidad`, `precio_unitario`, `stock_anterior`, `stock_nuevo`, `motivo`, `proveedor_id`, `usuario_id`, `referencia`, `fecha_movimiento`) VALUES
(1, 18, 'entrada', 150, 0.00, 0, 0, 'Stock inicial', NULL, NULL, NULL, '2026-01-27 01:01:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(3, 2, 'f99b4ee29aff092b7cf83fc98c2c6210daebf8e74781896ed55b92a339d74974', '2026-01-26 19:45:39', 1, '2026-01-26 17:45:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `stock_maximo` int(11) DEFAULT 100,
  `unidad_medida` enum('pieza','kg','litro','paquete','caja','otro') DEFAULT 'pieza',
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo_barras`, `nombre`, `descripcion`, `categoria_id`, `proveedor_id`, `precio_compra`, `precio_venta`, `stock_actual`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `fecha_vencimiento`, `estado`, `fecha_creacion`, `fecha_modificacion`) VALUES
(1, '750105530', 'Coca Cola 3L Original', NULL, 1, 1, 15.00, 20.00, 50, 5, 100, 'pieza', '2026-03-15', 'activo', '2026-01-26 05:31:13', '2026-01-26 21:31:16'),
(2, '12345678', 'Arroz Blanco 1lb', NULL, 4, 2, 3.50, 5.00, 96, 5, 100, 'pieza', '2027-04-15', 'activo', '2026-01-26 05:31:13', '2026-01-27 00:01:54'),
(3, 'LECHE001', 'Leche Entera 1L', NULL, 5, 2, 10.00, 14.50, 15, 5, 100, 'pieza', '2026-02-10', 'activo', '2026-01-26 05:31:13', '2026-01-26 21:31:16'),
(4, 'YOG002', 'Yogurt Fresa Pack 4u', NULL, 5, 2, 12.00, 18.00, 8, 5, 100, 'pieza', '2026-01-30', 'activo', '2026-01-26 05:31:13', '2026-01-26 21:31:16'),
(5, 'JABON55', 'Jab??n Lavaplast', NULL, 7, 3, 4.00, 6.50, 200, 5, 100, 'pieza', NULL, 'activo', '2026-01-26 05:31:13', '2026-01-26 05:31:13'),
(6, '7501234567895', 'Arroz Blanco 1lb', NULL, 2, NULL, 3.00, 4.50, 38, 10, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-27 00:01:54'),
(7, '7501234567896', 'Frijol Negro 1lb', NULL, 2, NULL, 4.50, 6.00, 30, 8, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(8, '7501234567897', 'Azúcar Blanca 1lb', NULL, 2, NULL, 2.50, 3.50, 23, 5, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-27 00:01:54'),
(9, '7501234567898', 'Aceite Vegetal 1L', NULL, 2, NULL, 12.00, 15.00, 11, 3, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-27 00:01:54'),
(10, '7501234567899', 'Sal de Mesa 1lb', NULL, 2, NULL, 1.50, 2.00, 50, 10, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(11, '7501234567900', 'Tomate Rojo 1lb', NULL, 3, NULL, 3.00, 5.00, 20, 5, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(12, '7501234567901', 'Cebolla Blanca 1lb', NULL, 3, NULL, 2.50, 4.00, 15, 3, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(13, '7501234567902', 'Plátano Maduro 1lb', NULL, 3, NULL, 2.00, 3.50, 25, 5, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(14, '7501234567903', 'Leche Entera 1L', NULL, 4, NULL, 8.00, 12.00, 30, 5, 100, 'pieza', '2026-02-05', 'activo', '2026-01-26 20:53:07', '2026-01-26 21:31:16'),
(15, '7501234567904', 'Queso Fresco 1lb', NULL, 4, NULL, 15.00, 20.00, 10, 2, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(16, '7501234567905', 'Jabón en Polvo 1kg', NULL, 5, NULL, 8.00, 12.00, 20, 3, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(17, '7501234567906', 'Cloro 1L', NULL, 5, NULL, 6.00, 9.00, 15, 3, 100, 'pieza', NULL, 'activo', '2026-01-26 20:53:07', '2026-01-26 20:53:07'),
(18, '0222', 'pavo', 'pavo', 10, NULL, 123.00, 150.00, 150, 10, 30, '', '2026-02-20', 'activo', '2026-01-27 01:01:39', '2026-01-27 01:01:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `contacto`, `telefono`, `email`, `direccion`, `estado`, `fecha_creacion`) VALUES
(1, 'Coca Cola Company', 'Juan Distribuidor', NULL, NULL, NULL, 'activo', '2026-01-26 05:31:13'),
(2, 'Distribuidora El Grano', 'Maria Ventas', NULL, NULL, NULL, 'activo', '2026-01-26 05:31:13'),
(3, 'Procter & Gamble', 'Atenci??n Clientes', NULL, NULL, NULL, 'activo', '2026-01-26 05:31:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 'Administrador', 'Due??o o encargado. Puede hacer todo.', 'activo', '2026-01-26 05:31:13'),
(2, 'Cajero', 'Atiende al p??blico y cobra.', 'activo', '2026-01-26 05:31:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos_caja`
--

CREATE TABLE `turnos_caja` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_final` decimal(10,2) DEFAULT NULL,
  `total_ventas` decimal(10,2) DEFAULT 0.00,
  `total_efectivo` decimal(10,2) DEFAULT 0.00,
  `total_tarjeta` decimal(10,2) DEFAULT 0.00,
  `total_vales` decimal(10,2) DEFAULT 0.00,
  `retiros` decimal(10,2) DEFAULT 0.00,
  `ingresos_extra` decimal(10,2) DEFAULT 0.00,
  `diferencia` decimal(10,2) DEFAULT 0.00,
  `estado` enum('abierto','cerrado') DEFAULT 'abierto',
  `observaciones` text DEFAULT NULL,
  `fecha_apertura` timestamp NULL DEFAULT current_timestamp(),
  `fecha_cierre` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `email`, `password_hash`, `telefono`, `rol_id`, `estado`, `fecha_creacion`, `ultimo_acceso`) VALUES
(1, 'Administrador', 'admin@laesquinita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 1, 'activo', '2026-01-26 05:31:13', '2026-01-27 03:11:25'),
(2, 'Usuario Prueba', 'mj3u7000@hotmail.com', '$2y$10$ojt3IXTY8jM76h3/pk.3XOml8aybz12vbdSIM6ED4GeC9EKEnJYJW', '3001234567', 2, 'activo', '2026-01-26 07:09:17', '2026-01-27 03:11:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `folio` varchar(20) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','vales') NOT NULL DEFAULT 'efectivo',
  `monto_recibido` decimal(10,2) DEFAULT 0.00,
  `cambio` decimal(10,2) DEFAULT 0.00,
  `estado` enum('completada','cancelada') DEFAULT 'completada',
  `observaciones` text DEFAULT NULL,
  `fecha_venta` timestamp NULL DEFAULT current_timestamp(),
  `fecha_cancelacion` timestamp NULL DEFAULT NULL,
  `motivo_cancelacion` text DEFAULT NULL,
  `descuento_total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `folio`, `cliente_id`, `usuario_id`, `subtotal`, `descuento`, `total`, `metodo_pago`, `monto_recibido`, `cambio`, `estado`, `observaciones`, `fecha_venta`, `fecha_cancelacion`, `motivo_cancelacion`, `descuento_total`) VALUES
(4, '', 1, 0, 20.00, 0.00, 20.00, 'efectivo', 20.00, 0.00, 'completada', NULL, '2026-01-26 22:10:46', NULL, NULL, 0.00),
(7, 'V20260126-1636', 1, 0, 20.00, 0.00, 20.00, 'efectivo', 20.00, 0.00, 'completada', NULL, '2026-01-26 22:55:48', NULL, NULL, 0.00),
(8, 'V20260127-1925', 1, 0, 28.00, 0.00, 28.00, 'efectivo', 30.00, 2.00, 'completada', NULL, '2026-01-26 23:00:08', NULL, NULL, 0.00),
(9, 'V20260127-4663', 1, 0, 28.00, 0.00, 28.00, 'efectivo', 40.00, 12.00, 'completada', NULL, '2026-01-27 00:01:54', NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_detalles`
--

INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`) VALUES
(1, 4, 9, 1, 15.00, 15.00, '2026-01-26 22:10:46'),
(2, 4, 2, 1, 5.00, 5.00, '2026-01-26 22:10:46'),
(3, 7, 9, 1, 15.00, 15.00, '2026-01-26 22:55:48'),
(4, 7, 2, 1, 5.00, 5.00, '2026-01-26 22:55:48'),
(5, 8, 9, 1, 15.00, 15.00, '2026-01-26 23:00:08'),
(6, 8, 2, 1, 5.00, 5.00, '2026-01-26 23:00:08'),
(7, 8, 6, 1, 4.50, 4.50, '2026-01-26 23:00:08'),
(8, 8, 8, 1, 3.50, 3.50, '2026-01-26 23:00:08'),
(9, 9, 9, 1, 15.00, 15.00, '2026-01-27 00:01:54'),
(10, 9, 2, 1, 5.00, 5.00, '2026-01-27 00:01:54'),
(11, 9, 6, 1, 4.50, 4.50, '2026-01-27 00:01:54'),
(12, 9, 8, 1, 3.50, 3.50, '2026-01-27 00:01:54');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_productos_por_vencer`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_productos_por_vencer` (
`id` int(11)
,`nombre` varchar(200)
,`fecha_vencimiento` date
,`dias_restantes` int(7)
,`stock_actual` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_productos_stock_bajo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_productos_stock_bajo` (
`id` int(11)
,`codigo_barras` varchar(50)
,`nombre` varchar(200)
,`categoria` varchar(100)
,`stock_actual` int(11)
,`stock_minimo` int(11)
,`proveedor` varchar(150)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_sugerencias_pedido`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_sugerencias_pedido` (
`nombre` varchar(200)
,`proveedor` varchar(150)
,`stock_actual` int(11)
,`stock_minimo` int(11)
,`cantidad_sugerida` bigint(13)
,`prioridad` varchar(7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_ventas_hoy`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_ventas_hoy` (
`id` int(11)
,`folio` varchar(20)
,`cliente` varchar(150)
,`vendedor` varchar(100)
,`total` decimal(10,2)
,`metodo_pago` enum('efectivo','tarjeta','vales')
,`estado` enum('completada','cancelada')
,`hora` time
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos_por_vencer`
--
DROP TABLE IF EXISTS `v_productos_por_vencer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos_por_vencer`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`fecha_vencimiento` AS `fecha_vencimiento`, to_days(`p`.`fecha_vencimiento`) - to_days(curdate()) AS `dias_restantes`, `p`.`stock_actual` AS `stock_actual` FROM `productos` AS `p` WHERE `p`.`fecha_vencimiento` is not null AND `p`.`fecha_vencimiento` <= curdate() + interval 7 day AND `p`.`estado` = 'activo' ORDER BY `p`.`fecha_vencimiento` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos_stock_bajo`
--
DROP TABLE IF EXISTS `v_productos_stock_bajo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos_stock_bajo`  AS SELECT `p`.`id` AS `id`, `p`.`codigo_barras` AS `codigo_barras`, `p`.`nombre` AS `nombre`, `c`.`nombre` AS `categoria`, `p`.`stock_actual` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, `pr`.`nombre` AS `proveedor` FROM ((`productos` `p` left join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) left join `proveedores` `pr` on(`p`.`proveedor_id` = `pr`.`id`)) WHERE `p`.`stock_actual` <= `p`.`stock_minimo` AND `p`.`estado` = 'activo' ORDER BY `p`.`stock_actual` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_sugerencias_pedido`
--
DROP TABLE IF EXISTS `v_sugerencias_pedido`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sugerencias_pedido`  AS SELECT `p`.`nombre` AS `nombre`, `pr`.`nombre` AS `proveedor`, `p`.`stock_actual` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, ceiling(`p`.`stock_minimo` * 2 - `p`.`stock_actual`) AS `cantidad_sugerida`, CASE WHEN `p`.`stock_actual` = 0 THEN 'URGENTE' ELSE 'Normal' END AS `prioridad` FROM (`productos` `p` left join `proveedores` `pr` on(`p`.`proveedor_id` = `pr`.`id`)) WHERE `p`.`stock_actual` <= `p`.`stock_minimo` ORDER BY `p`.`stock_actual` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_ventas_hoy`
--
DROP TABLE IF EXISTS `v_ventas_hoy`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ventas_hoy`  AS SELECT `v`.`id` AS `id`, `v`.`folio` AS `folio`, `c`.`nombre` AS `cliente`, `u`.`nombre_completo` AS `vendedor`, `v`.`total` AS `total`, `v`.`metodo_pago` AS `metodo_pago`, `v`.`estado` AS `estado`, cast(`v`.`fecha_venta` as time) AS `hora` FROM ((`ventas` `v` left join `clientes` `c` on(`v`.`cliente_id` = `c`.`id`)) join `usuarios` `u` on(`v`.`usuario_id` = `u`.`id`)) WHERE cast(`v`.`fecha_venta` as date) = curdate() ORDER BY `v`.`fecha_venta` DESC ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `cierres_caja`
--
ALTER TABLE `cierres_caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_turno` (`turno_id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_fecha` (`fecha_movimiento`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `idx_codigo_barras` (`codigo_barras`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_vencimiento` (`fecha_vencimiento`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_apertura` (`fecha_apertura`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_folio` (`folio`),
  ADD KEY `idx_fecha` (`fecha_venta`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_ventas_fecha` (`fecha_venta`),
  ADD KEY `idx_ventas_cliente` (`cliente_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cierres_caja`
--
ALTER TABLE `cierres_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`turno_id`) REFERENCES `turnos_caja` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `movimientos_inventario_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD CONSTRAINT `turnos_caja_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
