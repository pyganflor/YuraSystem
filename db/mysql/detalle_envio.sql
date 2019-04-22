-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-04-2019 a las 16:57:51
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
-- Base de datos: `yura_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_envio`
--

CREATE TABLE `detalle_envio` (
  `id_detalle_envio` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL,
  `id_especificacion` int(11) NOT NULL,
  `id_aerolinea` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `envio` int(11) DEFAULT NULL,
  `form` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_envio`
--
ALTER TABLE `detalle_envio`
  ADD PRIMARY KEY (`id_detalle_envio`),
  ADD KEY `FK_DetalleEnvio_AgenciaTransporte` (`id_aerolinea`),
  ADD KEY `FK_DetalleEnvio_Envio` (`id_envio`),
  ADD KEY `FK_DetalleEnvio_Especificacion` (`id_especificacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_envio`
--
ALTER TABLE `detalle_envio`
  MODIFY `id_detalle_envio` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_envio`
--
ALTER TABLE `detalle_envio`
  ADD CONSTRAINT `FK_DetalleEnvio_AgenciaTransporte` FOREIGN KEY (`id_aerolinea`) REFERENCES `aerolinea` (`id_aerolinea`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEnvio_Envio` FOREIGN KEY (`id_envio`) REFERENCES `envio` (`id_envio`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEnvio_Especificacion` FOREIGN KEY (`id_especificacion`) REFERENCES `especificacion` (`id_especificacion`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
