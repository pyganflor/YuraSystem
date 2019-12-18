-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-12-2019 a las 15:00:49
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
-- Estructura de tabla para la tabla `indicador_variedad`
--

CREATE TABLE `indicador_variedad` (
  `id_indicador_variedad` int(11) NOT NULL,
  `id_indicador` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `valor` float NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `indicador_variedad`
--
ALTER TABLE `indicador_variedad`
  ADD PRIMARY KEY (`id_indicador_variedad`),
  ADD KEY `FK_IndicadorVariedad_Indicador` (`id_indicador`),
  ADD KEY `FK_IndicadorVariedad_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `indicador_variedad`
--
ALTER TABLE `indicador_variedad`
  MODIFY `id_indicador_variedad` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `indicador_variedad`
--
ALTER TABLE `indicador_variedad`
  ADD CONSTRAINT `FK_IndicadorVariedad_Indicador` FOREIGN KEY (`id_indicador`) REFERENCES `indicador` (`id_indicador`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_IndicadorVariedad_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
