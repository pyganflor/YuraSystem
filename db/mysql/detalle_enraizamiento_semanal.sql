-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2020 a las 20:33:16
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
-- Estructura de tabla para la tabla `detalle_enraizamiento_semanal`
--

CREATE TABLE `detalle_enraizamiento_semanal` (
  `id_detalle_enraizamiento_semanal` int(11) NOT NULL,
  `id_enraizamiento_semanal` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad_siembra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_enraizamiento_semanal`
--
ALTER TABLE `detalle_enraizamiento_semanal`
  ADD PRIMARY KEY (`id_detalle_enraizamiento_semanal`),
  ADD KEY `FK_DetalleEnraizamientoSemanal_EnraizamientoSemanal` (`id_enraizamiento_semanal`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_enraizamiento_semanal`
--
ALTER TABLE `detalle_enraizamiento_semanal`
  MODIFY `id_detalle_enraizamiento_semanal` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_enraizamiento_semanal`
--
ALTER TABLE `detalle_enraizamiento_semanal`
  ADD CONSTRAINT `FK_DetalleEnraizamientoSemanal_EnraizamientoSemanal` FOREIGN KEY (`id_enraizamiento_semanal`) REFERENCES `enraizamiento_semanal` (`id_enraizamiento_semanal`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
