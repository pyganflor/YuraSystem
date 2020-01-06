-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-01-2020 a las 22:43:42
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
-- Estructura de tabla para la tabla `costos_semana_mano_obra`
--

CREATE TABLE `costos_semana_mano_obra` (
  `id_costos_semana_mano_obra` int(11) NOT NULL,
  `codigo_semana` int(4) NOT NULL,
  `id_actividad_mano_obra` int(11) NOT NULL,
  `valor` float NOT NULL,
  `cantidad` float NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `costos_semana_mano_obra`
--
ALTER TABLE `costos_semana_mano_obra`
  ADD PRIMARY KEY (`id_costos_semana_mano_obra`),
  ADD KEY `FK_CostosSemanaManoObra` (`id_actividad_mano_obra`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `costos_semana_mano_obra`
--
ALTER TABLE `costos_semana_mano_obra`
  MODIFY `id_costos_semana_mano_obra` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `costos_semana_mano_obra`
--
ALTER TABLE `costos_semana_mano_obra`
  ADD CONSTRAINT `FK_CostosSemanaManoObra` FOREIGN KEY (`id_actividad_mano_obra`) REFERENCES `actividad_mano_obra` (`id_actividad_mano_obra`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
