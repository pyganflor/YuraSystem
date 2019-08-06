-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-08-2019 a las 19:01:28
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
-- Estructura de tabla para la tabla `proyeccion_modulo`
--

CREATE TABLE `proyeccion_modulo` (
  `id_proyeccion_modulo` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_semana` int(11) NOT NULL,
  `tipo` char(1) COLLATE utf8_bin NOT NULL COMMENT 'Poda; Siembra; Cerrado',
  `curva` varchar(25) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `semana_poda_siembra` int(11) DEFAULT NULL,
  `poda_siembra` int(11) NOT NULL,
  `plantas_iniciales` int(11) NOT NULL,
  `desecho` float NOT NULL,
  `tallos_planta` float NOT NULL,
  `tallos_ramo` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `proyeccion_modulo`
--
ALTER TABLE `proyeccion_modulo`
  ADD PRIMARY KEY (`id_proyeccion_modulo`),
  ADD KEY `FK_ProyeccionModulo_Modulo` (`id_modulo`),
  ADD KEY `FK_ProyeccionModulo_Variedad` (`id_variedad`),
  ADD KEY `FK_ProyeccionModulo_Semana` (`id_semana`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `proyeccion_modulo`
--
ALTER TABLE `proyeccion_modulo`
  MODIFY `id_proyeccion_modulo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `proyeccion_modulo`
--
ALTER TABLE `proyeccion_modulo`
  ADD CONSTRAINT `FK_ProyeccionModulo_Modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ProyeccionModulo_Semana` FOREIGN KEY (`id_semana`) REFERENCES `semana` (`id_semana`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ProyeccionModulo_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
