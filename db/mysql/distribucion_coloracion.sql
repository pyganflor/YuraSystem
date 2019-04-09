-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-04-2019 a las 19:10:19
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
-- Estructura de tabla para la tabla `distribucion_coloracion`
--

CREATE TABLE `distribucion_coloracion` (
  `id_distribucion_coloracion` int(11) NOT NULL,
  `id_distribucion` int(11) NOT NULL,
  `id_marcacion_coloracion` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `distribucion_coloracion`
--
ALTER TABLE `distribucion_coloracion`
  ADD PRIMARY KEY (`id_distribucion_coloracion`),
  ADD KEY `FK_DistribucionColoracion_Distribucion` (`id_distribucion`),
  ADD KEY `FK_DistribucionColoracion_MarcacionColoracion` (`id_marcacion_coloracion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `distribucion_coloracion`
--
ALTER TABLE `distribucion_coloracion`
  MODIFY `id_distribucion_coloracion` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `distribucion_coloracion`
--
ALTER TABLE `distribucion_coloracion`
  ADD CONSTRAINT `FK_DistribucionColoracion_Distribucion` FOREIGN KEY (`id_distribucion`) REFERENCES `distribucion` (`id_distribucion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DistribucionColoracion_MarcacionColoracion` FOREIGN KEY (`id_marcacion_coloracion`) REFERENCES `marcacion_coloracion` (`id_marcacion_coloracion`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
