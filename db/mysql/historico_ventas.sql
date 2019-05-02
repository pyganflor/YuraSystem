-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-05-2019 a las 18:40:28
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
-- Estructura de tabla para la tabla `historico_ventas`
--

CREATE TABLE `historico_ventas` (
  `id_historico_ventas` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `mes` varchar(2) COLLATE utf8_bin NOT NULL,
  `valor` float DEFAULT NULL,
  `cajas_fisicas` float DEFAULT NULL,
  `cajas_equivalentes` float DEFAULT NULL,
  `precio_x_ramo` float DEFAULT '0',
  `anno` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historico_ventas`
--
ALTER TABLE `historico_ventas`
  ADD PRIMARY KEY (`id_historico_ventas`),
  ADD KEY `FK_HistoricoVentas_Cliente` (`id_cliente`),
  ADD KEY `FK_HistoricoVentas_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historico_ventas`
--
ALTER TABLE `historico_ventas`
  MODIFY `id_historico_ventas` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historico_ventas`
--
ALTER TABLE `historico_ventas`
  ADD CONSTRAINT `FK_HistoricoVentas_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_HistoricoVentas_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
