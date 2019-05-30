-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-05-2019 a las 21:59:09
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
-- Estructura de tabla para la tabla `ciclo`
--

CREATE TABLE `ciclo` (
  `id_ciclo` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `poda_siembra` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'P',
  `fecha_inicio` date NOT NULL,
  `fecha_cosecha` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_variedad` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `area` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ciclo`
--
ALTER TABLE `ciclo`
  ADD PRIMARY KEY (`id_ciclo`),
  ADD KEY `FK_Ciclo_Modulo` (`id_modulo`),
  ADD KEY `FK_Ciclo_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ciclo`
--
ALTER TABLE `ciclo`
  MODIFY `id_ciclo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciclo`
--
ALTER TABLE `ciclo`
  ADD CONSTRAINT `FK_Ciclo_Modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Ciclo_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
