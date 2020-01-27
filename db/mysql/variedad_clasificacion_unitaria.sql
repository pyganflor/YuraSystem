-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-01-2020 a las 14:56:19
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
-- Estructura de tabla para la tabla `variedad_clasificacion_unitaria`
--

CREATE TABLE `variedad_clasificacion_unitaria` (
  `id_variedad_clasificacion_unitaria` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `variedad_clasificacion_unitaria`
--
ALTER TABLE `variedad_clasificacion_unitaria`
  ADD PRIMARY KEY (`id_variedad_clasificacion_unitaria`),
  ADD KEY `FK_VariedadClasificacionUnitaria_Variedad` (`id_variedad`),
  ADD KEY `FK_VariedadClasificacionUnitaria_ClasificacionUnitaria` (`id_clasificacion_unitaria`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `variedad_clasificacion_unitaria`
--
ALTER TABLE `variedad_clasificacion_unitaria`
  MODIFY `id_variedad_clasificacion_unitaria` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `variedad_clasificacion_unitaria`
--
ALTER TABLE `variedad_clasificacion_unitaria`
  ADD CONSTRAINT `FK_VariedadClasificacionUnitaria_ClasificacionUnitaria` FOREIGN KEY (`id_clasificacion_unitaria`) REFERENCES `clasificacion_unitaria` (`id_clasificacion_unitaria`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_VariedadClasificacionUnitaria_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
