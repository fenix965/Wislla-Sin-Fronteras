-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2024 a las 00:34:02
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
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `usuario_id`, `nombre`, `apellidos`, `telefono`, `email`) VALUES
(1, 1, 'Carlos', 'Gomez', '1234567890', 'carlos@mail.com'),
(2, 5, 'Ana', 'Lopez', '1234567891', 'ana@mail.com'),
(3, 6, 'Luis', 'Martinez', '1234567892', 'luis@mail.com'),
(4, 10, 'Maria', 'Fernandez', '1234567893', 'maria@mail.com'),
(5, 5, 'Jorge', 'Perez', '1234567894', 'jorge@mail.com'),
(6, 6, 'Laura', 'Garcia', '1234567895', 'laura@mail.com'),
(7, 1, 'Miguel', 'Sanchez', '1234567896', 'miguel@mail.com'),
(8, 10, 'Julia', 'Diaz', '1234567897', 'julia@mail.com'),
(9, 5, 'Manuel', 'Torres', '1234567898', 'manuel@mail.com'),
(10, 6, 'Andrea', 'Ramirez', '1234567899', 'andrea@mail.com'),
(11, 19, 'Maciel', 'Balderrama', '75896985', 'horacioshirpas@gmail.com'),
(12, 20, 'mateo', 'torrez', '78569321', 'mateofeo@gmail.com'),
(13, 21, 'daniel', 'mendiola', '65432111', 'daniManoso@gmail.com'),
(14, 22, 'Andres', 'Baldiviezo', '78523691', 'baldimanoso@gmail.com');

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
  `email` varchar(255) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `nombre`, `apellidos`, `puesto`, `salario`, `telefono`, `email`, `fecha_ingreso`) VALUES
(6, 2, 'Victor', 'Nuñez', 'Administrador', 1500.00, '3214567895', 'victor@mail.com', '2021-04-20'),
(7, 3, 'Marcos', 'Meneces', 'Cocinero', 2000.00, '3214567896', 'marcos@mail.com', '2021-05-12'),
(9, 7, 'Laura', 'Castillo', 'Limpiador', 1200.00, '3214567898', 'laura.castillo@mail.com', '2020-12-04'),
(10, 8, 'Diana', 'Ortega', 'Cocinera', 1120.00, '3214567899', 'diana@mail.com', '2019-11-18'),
(11, 11, 'mauri', 'Marces', 'Cocinero', 2000.00, '75585965', 'leandroManoso@gmail.com', '0000-00-00'),
(12, 12, 'Ayana', 'Orosco', 'Cocinero Vegano', 2200.00, '6852314', 'nataliaLafea@gmail.com', '0000-00-00'),
(14, 15, 'Leandro', 'Bolaños', 'Limpiador', 1200.00, '6852314', 'leandroManoso1@gmail.com', '2024-11-14'),
(15, 16, 'Alexandra', 'Mamani', 'Cocinero', 2000.00, '67015543', 'alexandra@gmail.com', '2024-11-14'),
(16, 17, 'mateo', 'torrez', 'Cocinero Vegano', 2200.00, '74858555', 'silvia@gmailk.com', '2024-11-14');

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
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_empleados_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
