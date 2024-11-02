-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2024 a las 23:32:12
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
-- Base de datos: `test_1_wislla_sin_fronteras`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `usuario_id`, `nombre`, `apellidos`, `telefono`, `email`, `direccion`) VALUES
(1, 1, 'Carlos', 'Gomez', '1234567890', 'carlos@mail.com', 'Calle 1'),
(2, 5, 'Ana', 'Lopez', '1234567891', 'ana@mail.com', 'Calle 2'),
(3, 6, 'Luis', 'Martinez', '1234567892', 'luis@mail.com', 'Calle 3'),
(4, 10, 'Maria', 'Fernandez', '1234567893', 'maria@mail.com', 'Calle 4'),
(5, 5, 'Jorge', 'Perez', '1234567894', 'jorge@mail.com', 'Calle 5'),
(6, 6, 'Laura', 'Garcia', '1234567895', 'laura@mail.com', 'Calle 6'),
(7, 1, 'Miguel', 'Sanchez', '1234567896', 'miguel@mail.com', 'Calle 7'),
(8, 10, 'Julia', 'Diaz', '1234567897', 'julia@mail.com', 'Calle 8'),
(9, 5, 'Manuel', 'Torres', '1234567898', 'manuel@mail.com', 'Calle 9'),
(10, 6, 'Andrea', 'Ramirez', '1234567899', 'andrea@mail.com', 'Calle 10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_orden`
--

CREATE TABLE `detalle_orden` (
  `id` bigint(20) NOT NULL,
  `orden_id` bigint(20) DEFAULT NULL,
  `platillo_id` bigint(20) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL CHECK (`subtotal` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_orden`
--

INSERT INTO `detalle_orden` (`id`, `orden_id`, `platillo_id`, `subtotal`) VALUES
(1, 1, 1, 10.00),
(2, 1, 5, 2.50),
(3, 2, 2, 8.50),
(4, 2, 3, 12.00),
(5, 3, 4, 4.50),
(6, 3, 8, 10.00),
(7, 4, 7, 3.00),
(8, 5, 10, 8.00),
(9, 6, 9, 2.00),
(10, 7, 6, 10.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `puesto` varchar(50) NOT NULL,
  `salario` decimal(10,2) NOT NULL CHECK (`salario` >= 0),
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `nombre`, `apellidos`, `puesto`, `salario`, `telefono`, `email`) VALUES
(1, 2, 'Pedro', 'Morales', 'Administrador', 1500.00, '3214567890', 'pedro@mail.com'),
(2, 3, 'Carlos', 'Salinas', 'Cocinero', 1000.00, '3214567891', 'carlos.salinas@mail.com'),
(3, 4, 'Sandra', 'Villalba', 'Mesera', 900.00, '3214567892', 'sandra@mail.com'),
(4, 7, 'Luis', 'Romero', 'Mesero', 950.00, '3214567893', 'luis.romero@mail.com'),
(5, 8, 'Elena', 'Campos', 'Cocinera', 1100.00, '3214567894', 'elena@mail.com'),
(6, 2, 'Victor', 'Nuñez', 'Administrador', 1500.00, '3214567895', 'victor@mail.com'),
(7, 3, 'Marcos', 'Mendez', 'Cocinero', 1050.00, '3214567896', 'marcos@mail.com'),
(8, 4, 'Paola', 'Rojas', 'Mesera', 920.00, '3214567897', 'paola@mail.com'),
(9, 7, 'Laura', 'Castillo', 'Mesera', 930.00, '3214567898', 'laura.castillo@mail.com'),
(10, 8, 'Diana', 'Ortega', 'Cocinera', 1120.00, '3214567899', 'diana@mail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cantidad_disponible` int(11) NOT NULL CHECK (`cantidad_disponible` >= 0),
  `unidad_medida` varchar(50) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL CHECK (`precio_unitario` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nombre`, `cantidad_disponible`, `unidad_medida`, `precio_unitario`) VALUES
(1, 'Harina', 100, 'kg', 1.50),
(2, 'Leche', 200, 'litros', 0.80),
(3, 'Huevos', 500, 'unidades', 0.15),
(4, 'Carne de Res', 50, 'kg', 8.00),
(5, 'Papas', 100, 'kg', 0.50),
(6, 'Queso', 80, 'kg', 5.00),
(7, 'Aceite', 60, 'litros', 3.00),
(8, 'Pollo', 70, 'kg', 6.50),
(9, 'Tomates', 120, 'kg', 0.60),
(10, 'Lechuga', 50, 'kg', 0.40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` bigint(20) NOT NULL,
  `producto_id` bigint(20) DEFAULT NULL,
  `cantidad` int(11) NOT NULL CHECK (`cantidad` >= 0),
  `fecha_ingreso` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `producto_id`, `cantidad`, `fecha_ingreso`) VALUES
(1, 1, 50, '2024-11-01'),
(2, 2, 100, '2024-11-01'),
(3, 3, 20, '2024-11-01'),
(4, 4, 25, '2024-11-01'),
(5, 5, 30, '2024-11-01'),
(6, 6, 35, '2024-11-01'),
(7, 7, 40, '2024-11-01'),
(8, 8, 15, '2024-11-01'),
(9, 9, 60, '2024-11-01'),
(10, 10, 80, '2024-11-01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` bigint(20) NOT NULL,
  `numero` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL CHECK (`capacidad` > 0),
  `estado` enum('disponible','ocupada','reservada') NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `numero`, `capacidad`, `estado`, `ubicacion`) VALUES
(1, 1, 4, 'disponible', 'Planta Baja'),
(2, 2, 2, 'ocupada', 'Planta Baja'),
(3, 3, 6, 'reservada', 'Planta Alta'),
(4, 4, 4, 'disponible', 'Planta Alta'),
(5, 5, 2, 'disponible', 'Terraza'),
(6, 6, 8, 'ocupada', 'Salón Privado'),
(7, 7, 4, 'reservada', 'Salón Principal'),
(8, 8, 6, 'disponible', 'Terraza'),
(9, 9, 4, 'ocupada', 'Planta Baja'),
(10, 10, 4, 'reservada', 'Planta Alta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id` bigint(20) NOT NULL,
  `pedido_id` bigint(20) DEFAULT NULL,
  `platillo_id` bigint(20) DEFAULT NULL,
  `cantidad` int(11) NOT NULL CHECK (`cantidad` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden`
--

INSERT INTO `orden` (`id`, `pedido_id`, `platillo_id`, `cantidad`) VALUES
(1, 1, 1, 2),
(2, 1, 5, 1),
(3, 2, 2, 1),
(4, 2, 3, 2),
(5, 3, 4, 1),
(6, 3, 8, 1),
(7, 4, 7, 1),
(8, 5, 10, 2),
(9, 6, 9, 1),
(10, 7, 6, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) DEFAULT NULL,
  `mesa_id` bigint(20) DEFAULT NULL,
  `empleado_id` bigint(20) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT curdate(),
  `estado` enum('pendiente','completado','cancelado') NOT NULL,
  `total` decimal(10,2) NOT NULL CHECK (`total` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `mesa_id`, `empleado_id`, `fecha`, `estado`, `total`) VALUES
(1, 1, 1, 1, '2024-11-10', 'pendiente', 50.00),
(2, 2, 3, 2, '2024-11-11', 'completado', 75.00),
(3, 3, 5, 3, '2024-11-12', 'pendiente', 30.00),
(4, 4, 7, 4, '2024-11-13', 'cancelado', 0.00),
(5, 5, 9, 5, '2024-11-14', 'pendiente', 60.00),
(6, 6, 10, 6, '2024-11-15', 'completado', 100.00),
(7, 7, 2, 7, '2024-11-16', 'pendiente', 40.00),
(8, 8, 4, 8, '2024-11-17', 'completado', 80.00),
(9, 9, 6, 9, '2024-11-18', 'pendiente', 90.00),
(10, 10, 8, 10, '2024-11-19', 'completado', 120.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platillos`
--

CREATE TABLE `platillos` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` >= 0),
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `platillos`
--

INSERT INTO `platillos` (`id`, `nombre`, `precio`, `descripcion`) VALUES
(1, 'Hamburguesa Clásica', 5.00, 'Hamburguesa con carne de res, lechuga, tomate y queso'),
(2, 'Pizza Margherita', 8.50, 'Pizza con queso, tomate y albahaca'),
(3, 'Tacos de Pollo', 6.00, 'Tacos con pollo, queso y salsa'),
(4, 'Ensalada César', 4.50, 'Ensalada con lechuga, queso y aderezo César'),
(5, 'Papas Fritas', 2.50, 'Papas fritas crujientes'),
(6, 'Sandwich de Jamón y Queso', 3.50, 'Sandwich con jamón y queso'),
(7, 'Sopa de Tomate', 3.00, 'Sopa cremosa de tomate'),
(8, 'Asado de Res', 10.00, 'Carne de res asada con guarnición'),
(9, 'Tortilla de Huevo', 2.00, 'Tortilla de huevo con vegetales'),
(10, 'Pastel de Chocolate', 4.00, 'Pastel de chocolate con cobertura de chocolate');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platillo_ingredientes`
--

CREATE TABLE `platillo_ingredientes` (
  `id` bigint(20) NOT NULL,
  `platillo_id` bigint(20) DEFAULT NULL,
  `ingrediente_id` bigint(20) DEFAULT NULL,
  `cantidad` int(11) NOT NULL CHECK (`cantidad` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `platillo_ingredientes`
--

INSERT INTO `platillo_ingredientes` (`id`, `platillo_id`, `ingrediente_id`, `cantidad`) VALUES
(1, 1, 4, 1),
(2, 1, 9, 1),
(3, 1, 6, 1),
(4, 2, 6, 1),
(5, 2, 9, 1),
(6, 3, 8, 1),
(7, 3, 6, 1),
(8, 4, 10, 1),
(9, 4, 6, 1),
(10, 5, 5, 1),
(11, 6, 6, 1),
(12, 7, 9, 1),
(13, 8, 4, 1),
(14, 9, 3, 2),
(15, 10, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` >= 0),
  `categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `categoria`) VALUES
(1, 'Cerveza', 3.50, 'Bebida'),
(2, 'Refresco', 1.50, 'Bebida'),
(3, 'Pizza', 12.00, 'Comida'),
(4, 'Hamburguesa', 10.00, 'Comida'),
(5, 'Pasta', 8.00, 'Comida'),
(6, 'Ensalada', 5.00, 'Comida'),
(7, 'Helado', 3.00, 'Postre'),
(8, 'Pastel', 4.00, 'Postre'),
(9, 'Cafe', 2.00, 'Bebida Caliente'),
(10, 'Te', 2.00, 'Bebida Caliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `contacto`, `telefono`, `email`, `direccion`) VALUES
(1, 'Proveedor 1', 'Carlos Sanchez', '1111111111', 'proveedor1@mail.com', 'Zona Industrial 1'),
(2, 'Proveedor 2', 'Ana Perez', '1111111112', 'proveedor2@mail.com', 'Zona Industrial 2'),
(3, 'Proveedor 3', 'Luis Garcia', '1111111113', 'proveedor3@mail.com', 'Zona Industrial 3'),
(4, 'Proveedor 4', 'Sofia Torres', '1111111114', 'proveedor4@mail.com', 'Zona Industrial 4'),
(5, 'Proveedor 5', 'Jose Diaz', '1111111115', 'proveedor5@mail.com', 'Zona Industrial 5'),
(6, 'Proveedor 6', 'Laura Rojas', '1111111116', 'proveedor6@mail.com', 'Zona Industrial 6'),
(7, 'Proveedor 7', 'Juan Martinez', '1111111117', 'proveedor7@mail.com', 'Zona Industrial 7'),
(8, 'Proveedor 8', 'Claudia Romero', '1111111118', 'proveedor8@mail.com', 'Zona Industrial 8'),
(9, 'Proveedor 9', 'Miguel Hernandez', '1111111119', 'proveedor9@mail.com', 'Zona Industrial 9'),
(10, 'Proveedor 10', 'Raul Gonzalez', '1111111120', 'proveedor10@mail.com', 'Zona Industrial 10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) DEFAULT NULL,
  `mesa_id` bigint(20) DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `numero_personas` int(11) NOT NULL CHECK (`numero_personas` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `cliente_id`, `mesa_id`, `fecha`, `hora`, `numero_personas`) VALUES
(1, 1, 1, '2024-11-10', '18:00:00', 4),
(2, 2, 3, '2024-11-11', '19:00:00', 6),
(3, 3, 5, '2024-11-12', '20:00:00', 2),
(4, 4, 7, '2024-11-13', '21:00:00', 4),
(5, 5, 9, '2024-11-14', '18:30:00', 4),
(6, 6, 10, '2024-11-15', '19:30:00', 6),
(7, 7, 2, '2024-11-16', '20:30:00', 2),
(8, 8, 4, '2024-11-17', '21:30:00', 4),
(9, 9, 6, '2024-11-18', '18:00:00', 8),
(10, 10, 8, '2024-11-19', '19:00:00', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `nombre_usuario` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('cliente','administrador','cocinero','mesero') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `contrasena`, `rol`) VALUES
(1, 'usuario1', 'password1', 'cliente'),
(2, 'usuario2', 'password2', 'administrador'),
(3, 'usuario3', 'password3', 'cocinero'),
(4, 'usuario4', 'password4', 'mesero'),
(5, 'usuario5', 'password5', 'cliente'),
(6, 'usuario6', 'password6', 'cliente'),
(7, 'usuario7', 'password7', 'mesero'),
(8, 'usuario8', 'password8', 'cocinero'),
(9, 'usuario9', 'password9', 'administrador'),
(10, 'usuario10', 'password10', 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_clientes_email` (`email`);

--
-- Indices de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_id` (`orden_id`),
  ADD KEY `platillo_id` (`platillo_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_empleados_email` (`email`);

--
-- Indices de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `idx_mesas_numero` (`numero`);

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `platillo_id` (`platillo_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `mesa_id` (`mesa_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `platillos`
--
ALTER TABLE `platillos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `platillo_ingredientes`
--
ALTER TABLE `platillo_ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `platillo_id` (`platillo_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `mesa_id` (`mesa_id`),
  ADD KEY `idx_reservas_fecha_hora` (`fecha`,`hora`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD KEY `idx_usuarios_nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `platillos`
--
ALTER TABLE `platillos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `platillo_ingredientes`
--
ALTER TABLE `platillo_ingredientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD CONSTRAINT `detalle_orden_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `orden` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_orden_ibfk_2` FOREIGN KEY (`platillo_id`) REFERENCES `platillos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `orden`
--
ALTER TABLE `orden`
  ADD CONSTRAINT `orden_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_ibfk_2` FOREIGN KEY (`platillo_id`) REFERENCES `platillos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `platillo_ingredientes`
--
ALTER TABLE `platillo_ingredientes`
  ADD CONSTRAINT `platillo_ingredientes_ibfk_1` FOREIGN KEY (`platillo_id`) REFERENCES `platillos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `platillo_ingredientes_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
