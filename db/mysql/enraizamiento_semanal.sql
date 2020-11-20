-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2020 a las 19:51:09
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
-- Estructura de tabla para la tabla `enraizamiento_semanal`
--

CREATE TABLE `enraizamiento_semanal` (
  `id_enraizamiento_semanal` int(11) NOT NULL,
  `semana_ini` int(4) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `cantidad_siembra` int(11) NOT NULL,
  `semana_fin` int(4) NOT NULL,
  `cantidad_semanas` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `enraizamiento_semanal`
--
ALTER TABLE `enraizamiento_semanal`
  ADD PRIMARY KEY (`id_enraizamiento_semanal`),
  ADD KEY `FK_EnraizamientoSemanal_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `enraizamiento_semanal`
--
ALTER TABLE `enraizamiento_semanal`
  MODIFY `id_enraizamiento_semanal` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `enraizamiento_semanal`
--
ALTER TABLE `enraizamiento_semanal`
  ADD CONSTRAINT `FK_EnraizamientoSemanal_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
