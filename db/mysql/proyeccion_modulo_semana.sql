-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-09-2019 a las 00:07:58
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
-- Estructura de tabla para la tabla `proyeccion_modulo_semana`
--

CREATE TABLE `proyeccion_modulo_semana` (
  `id_proyeccion_modulo_semana` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `semana` int(4) NOT NULL,
  `tipo` char(1) COLLATE utf8_bin NOT NULL COMMENT 'Siembra; Poda; Info; T(semana de cosecha); Y(proyeccion); Vacio',
  `info` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `cosechados` float DEFAULT NULL,
  `proyectados` float DEFAULT NULL,
  `plantas_iniciales` float DEFAULT NULL,
  `plantas_actuales` float DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `id_variedad` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '0',
  `area` float DEFAULT NULL,
  `tallos_planta` float DEFAULT NULL,
  `tallos_ramo` float DEFAULT NULL,
  `curva` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `poda_siembra` char(1) COLLATE utf8_bin DEFAULT NULL,
  `semana_poda_siembra` int(11) DEFAULT NULL,
  `desecho` float DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tabla` char(1) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `proyeccion_modulo_semana`
--
ALTER TABLE `proyeccion_modulo_semana`
  ADD PRIMARY KEY (`id_proyeccion_modulo_semana`),
  ADD KEY `FK_ProyeccionModuloSemana_Modulo` (`id_modulo`),
  ADD KEY `FK_ProyeccionModuloSemana_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `proyeccion_modulo_semana`
--
ALTER TABLE `proyeccion_modulo_semana`
  MODIFY `id_proyeccion_modulo_semana` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `proyeccion_modulo_semana`
--
ALTER TABLE `proyeccion_modulo_semana`
  ADD CONSTRAINT `FK_ProyeccionModuloSemana_Modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ProyeccionModuloSemana_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
