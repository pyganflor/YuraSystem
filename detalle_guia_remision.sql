-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-04-2019 a las 16:32:36
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `yura_db_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_guia_remision`
--

CREATE TABLE `detalle_guia_remision` (
  `id_detalle_guia_remision` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_guia_remision`
--
ALTER TABLE `detalle_guia_remision`
  ADD PRIMARY KEY (`id_detalle_guia_remision`),
  ADD KEY `detalle_guia_remision_id_comprobante_index` (`id_comprobante`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_guia_remision`
--
ALTER TABLE `detalle_guia_remision`
  MODIFY `id_detalle_guia_remision` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_guia_remision`
--
ALTER TABLE `detalle_guia_remision`
  ADD CONSTRAINT `FK_detalle_guia_remision_comprobante` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobante` (`id_comprobante`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
