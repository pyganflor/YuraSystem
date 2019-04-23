-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2019 a las 23:35:14
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
-- Estructura de tabla para la tabla `impuesto_desglose_envio_factura`
--

CREATE TABLE `impuesto_desglose_envio_factura` (
  `id_impuesto_desglose_envio_factura` int(11) NOT NULL,
  `id_desglose_envio_factura` int(11) NOT NULL,
  `codigo_impuesto` int(11) NOT NULL,
  `codigo_porcentaje` varchar(6) NOT NULL,
  `base_imponible` float(10,2) NOT NULL,
  `valor` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `impuesto_desglose_envio_factura`
--
ALTER TABLE `impuesto_desglose_envio_factura`
  ADD PRIMARY KEY (`id_impuesto_desglose_envio_factura`),
  ADD KEY `codigo_impuesto` (`codigo_impuesto`),
  ADD KEY `id_desglose_envio_factura` (`id_desglose_envio_factura`),
  ADD KEY `codigo_porcentaje` (`codigo_porcentaje`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `impuesto_desglose_envio_factura`
--
ALTER TABLE `impuesto_desglose_envio_factura`
  MODIFY `id_impuesto_desglose_envio_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `impuesto_desglose_envio_factura`
--
ALTER TABLE `impuesto_desglose_envio_factura`
  ADD CONSTRAINT `FK_impuesto_desglose_envio_factura_desglose_envio_factura` FOREIGN KEY (`id_desglose_envio_factura`) REFERENCES `desglose_envio_factura` (`id_desglose_envio_factura`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
