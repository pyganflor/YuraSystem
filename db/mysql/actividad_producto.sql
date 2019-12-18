-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-12-2019 a las 15:58:46
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
-- Estructura de tabla para la tabla `actividad_producto`
--

CREATE TABLE `actividad_producto` (
  `id_actividad_producto` int(11) NOT NULL,
  `id_actividad` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad_producto`
--
ALTER TABLE `actividad_producto`
  ADD PRIMARY KEY (`id_actividad_producto`),
  ADD KEY `FK_ActividadProducto_Actividad` (`id_actividad`),
  ADD KEY `FK_ActividadProducto_Prodcuto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad_producto`
--
ALTER TABLE `actividad_producto`
  MODIFY `id_actividad_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad_producto`
--
ALTER TABLE `actividad_producto`
  ADD CONSTRAINT `FK_ActividadProducto_Actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividad` (`id_actividad`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ActividadProducto_Prodcuto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
