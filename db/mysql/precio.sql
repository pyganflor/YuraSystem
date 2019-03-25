-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-03-2019 a las 21:13:34
-- Versión del servidor: 10.1.36-MariaDB
-- Versión de PHP: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `yura_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precio`
--

CREATE TABLE `precio` (
  `id_precio` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_detalle_especificacionempaque` int(11) NOT NULL,
  `cantidad` varchar(50) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `precio`
--

INSERT INTO `precio` (`id_precio`, `id_cliente`, `id_detalle_especificacionempaque`, `cantidad`, `estado`, `fecha_registro`) VALUES
(1, 1, 2, '2', 1, '2019-03-14 13:18:37'),
(2, 1, 3, '2', 1, '2019-03-14 13:18:37'),
(3, 1, 4, '3', 1, '2019-03-14 13:18:37'),
(4, 1, 7, '2', 1, '2019-03-14 13:18:37'),
(5, 1, 8, '2', 1, '2019-03-14 13:18:38'),
(6, 1, 9, '2', 1, '2019-03-14 13:18:38'),
(7, 1, 10, '2', 1, '2019-03-14 13:18:38'),
(8, 1, 11, '2', 1, '2019-03-14 13:18:38'),
(9, 1, 12, '2', 1, '2019-03-14 13:18:38'),
(10, 1, 13, '2.25|3', 1, '2019-03-14 13:18:38'),
(11, 1, 14, '1.5', 1, '2019-03-14 13:18:38'),
(12, 1, 15, '1.25|2|1.75|2.5', 1, '2019-03-14 13:18:38');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `precio`
--
ALTER TABLE `precio`
  ADD PRIMARY KEY (`id_precio`),
  ADD KEY `FK_Precio_Variedad` (`id_cliente`),
  ADD KEY `FK_Precio_ClasificacionRamo` (`id_detalle_especificacionempaque`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `precio`
--
ALTER TABLE `precio`
  MODIFY `id_precio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `precio`
--
ALTER TABLE `precio`
  ADD CONSTRAINT `FK_Precio_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Precio_DetalleEspecificacionEmpaque` FOREIGN KEY (`id_detalle_especificacionempaque`) REFERENCES `detalle_especificacionempaque` (`id_detalle_especificacionempaque`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
