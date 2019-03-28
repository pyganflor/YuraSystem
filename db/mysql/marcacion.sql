-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2019 a las 18:36:07
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
-- Estructura de tabla para la tabla `marcacion`
--

CREATE TABLE `marcacion` (
  `id_marcacion` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `ramos` int(11) NOT NULL,
  `id_detalle_pedido` int(11) NOT NULL,
  `id_especificacion_empaque` int(11) NOT NULL,
  `piezas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `marcacion`
--
ALTER TABLE `marcacion`
  ADD PRIMARY KEY (`id_marcacion`),
  ADD KEY `FK_Marcacion_DetallePedido` (`id_detalle_pedido`),
  ADD KEY `FK_Marcacion_EspecificacionEmpaque` (`id_especificacion_empaque`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `marcacion`
--
ALTER TABLE `marcacion`
  MODIFY `id_marcacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `marcacion`
--
ALTER TABLE `marcacion`
  ADD CONSTRAINT `FK_Marcacion_DetallePedido` FOREIGN KEY (`id_detalle_pedido`) REFERENCES `detalle_pedido` (`id_detalle_pedido`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Marcacion_EspecificacionEmpaque` FOREIGN KEY (`id_especificacion_empaque`) REFERENCES `especificacion_empaque` (`id_especificacion_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
