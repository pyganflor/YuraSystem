-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2020 a las 23:29:41
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
-- Estructura de tabla para la tabla `ciclo_temperatura`
--

CREATE TABLE `ciclo_temperatura` (
  `id_ciclo_temperatura` int(11) NOT NULL,
  `id_ciclo` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `num_semana` int(11) NOT NULL,
  `acumulado` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ciclo_temperatura`
--
ALTER TABLE `ciclo_temperatura`
  ADD PRIMARY KEY (`id_ciclo_temperatura`),
  ADD KEY `FK_CicloTemperaturaCiclo` (`id_ciclo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ciclo_temperatura`
--
ALTER TABLE `ciclo_temperatura`
  MODIFY `id_ciclo_temperatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciclo_temperatura`
--
ALTER TABLE `ciclo_temperatura`
  ADD CONSTRAINT `FK_CicloTemperaturaCiclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclo` (`id_ciclo`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
