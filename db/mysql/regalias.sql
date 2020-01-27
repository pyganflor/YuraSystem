-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-01-2020 a las 14:55:55
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
-- Estructura de tabla para la tabla `regalias`
--

CREATE TABLE `regalias` (
  `id_regalias` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `codigo_semana` int(4) NOT NULL,
  `valor` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `regalias`
--
ALTER TABLE `regalias`
  ADD PRIMARY KEY (`id_regalias`),
  ADD KEY `FK_Regalias_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `regalias`
--
ALTER TABLE `regalias`
  MODIFY `id_regalias` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `regalias`
--
ALTER TABLE `regalias`
  ADD CONSTRAINT `FK_Regalias_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
