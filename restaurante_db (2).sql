-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-11-2024 a las 11:59:08
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
-- Base de datos: `restaurante_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `nombre` text NOT NULL,
  `apellidos` text NOT NULL,
  `telefono` text DEFAULT NULL,
  `email` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `usuario_id`, `nombre`, `apellidos`, `telefono`, `email`) VALUES
(1, 2, 'Juan', 'Pérez', '987654321', 'juan.perez@example.com'),
(2, 3, 'María', 'González', '965432178', 'maria.gonzalez@example.com'),
(4, 16, 'Katia', 'Mansilla', '67688987', 'bdm2027252@est.univalle.edu'),
(5, 17, 'niebla', 'Balderrama', '75869541', 'baldelgadom@gmail.com'),
(6, 18, 'lupita', 'weqweqweqwe', '65236985', 'Patatamexicana028@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `nombre` text NOT NULL,
  `apellidos` text NOT NULL,
  `puesto` text NOT NULL,
  `salario` decimal(10,2) NOT NULL,
  `telefono` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `nombre`, `apellidos`, `puesto`, `salario`, `telefono`, `email`, `fecha_ingreso`) VALUES
(16, 11, 'Joaquin', 'alcozer', 'Cocinero Vegano', 2200.00, '67015543', 'Patatamexicana029@gmail.com', '2024-11-15 00:00:00'),
(17, 12, 'fwfsdfs', 'sfsdfsdf', 'Limpiador', 1200.00, '75869541', 'bdm2027252@est.univalle.edu', '2024-11-06 00:00:00'),
(23, NULL, 'asdadasd', 'asdasdasda', 'Cocinero Vegano', 2200.00, '67015543', 'Patatamexicana029@gmail.com', '2024-11-06 00:00:00'),
(24, 20, 'mauri', 'marces', 'Cocinero Vegano', 2200.00, '76665443', 'mauri@gmail.com', '2024-11-06 06:46:44'),
(27, 24, 'horacio', 'alfaro', 'Cocinero', 2000.00, '67015547', 'horacio@gmail.com', '0000-00-00 00:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
