-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-11-2020 a las 16:41:09
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
-- Estructura de tabla para la tabla `resumen_total_semanal_exportcalas`
--

CREATE TABLE `resumen_total_semanal_exportcalas` (
  `id_resumen_total_semanal_exportcalas` int(11) NOT NULL,
  `semana` int(4) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `tallos_cosechados` int(11) NOT NULL,
  `tallos_proyectados` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `resumen_total_semanal_exportcalas`
--
ALTER TABLE `resumen_total_semanal_exportcalas`
  ADD PRIMARY KEY (`id_resumen_total_semanal_exportcalas`),
  ADD KEY `ResumenTotalSemanalExportcalasVariedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `resumen_total_semanal_exportcalas`
--
ALTER TABLE `resumen_total_semanal_exportcalas`
  MODIFY `id_resumen_total_semanal_exportcalas` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `resumen_total_semanal_exportcalas`
--
ALTER TABLE `resumen_total_semanal_exportcalas`
  ADD CONSTRAINT `ResumenTotalSemanalExportcalasVariedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
