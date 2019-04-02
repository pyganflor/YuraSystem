-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-04-2019 a las 22:12:10
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
-- Estructura de tabla para la tabla `marcacion_coloracion`
--

CREATE TABLE `marcacion_coloracion` (
  `id_marcacion_coloracion` int(11) NOT NULL,
  `id_marcacion` int(11) NOT NULL,
  `id_coloracion` int(11) NOT NULL,
  `id_detalle_especificacionempaque` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `marcacion_coloracion`
--
ALTER TABLE `marcacion_coloracion`
  ADD PRIMARY KEY (`id_marcacion_coloracion`),
  ADD KEY `FK_MarcacionColoracion_Marcacion` (`id_marcacion`),
  ADD KEY `FK_MarcacionColoracion_Coloracion` (`id_coloracion`),
  ADD KEY `FK_MarcacionColoracion_DetalleEspecificacionEmpaque` (`id_detalle_especificacionempaque`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `marcacion_coloracion`
--
ALTER TABLE `marcacion_coloracion`
  MODIFY `id_marcacion_coloracion` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `marcacion_coloracion`
--
ALTER TABLE `marcacion_coloracion`
  ADD CONSTRAINT `FK_MarcacionColoracion_Coloracion` FOREIGN KEY (`id_coloracion`) REFERENCES `coloracion` (`id_coloracion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MarcacionColoracion_DetalleEspecificacionEmpaque` FOREIGN KEY (`id_detalle_especificacionempaque`) REFERENCES `detalle_especificacionempaque` (`id_detalle_especificacionempaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MarcacionColoracion_Marcacion` FOREIGN KEY (`id_marcacion`) REFERENCES `marcacion` (`id_marcacion`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
