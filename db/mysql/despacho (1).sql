-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2019 a las 23:37:24
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
-- Estructura de tabla para la tabla `despacho`
--

CREATE TABLE `despacho` (
  `id_despacho` int(11) NOT NULL,
  `id_transportista` int(11) NOT NULL,
  `id_camion` int(11) NOT NULL,
  `id_conductor` int(11) NOT NULL,
  `fecha_despacho` date NOT NULL,
  `sello_salida` varchar(30) NOT NULL,
  `semana` varchar(10) NOT NULL,
  `rango_temp` varchar(20) DEFAULT NULL,
  `n_viaje` int(11) NOT NULL,
  `hora_salida` varchar(10) DEFAULT NULL,
  `temp` varchar(20) DEFAULT NULL,
  `kilometraje` varchar(20) DEFAULT NULL,
  `sellos` varchar(300) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `horario` varchar(50) DEFAULT NULL,
  `resp_ofi_despacho` varchar(100) NOT NULL,
  `id_resp_ofi_despacho` varchar(20) NOT NULL,
  `aux_cuarto_fri` varchar(100) NOT NULL,
  `id_aux_cuarto_fri` varchar(20) DEFAULT NULL,
  `guardia_turno` varchar(100) NOT NULL,
  `id_guardia_turno` varchar(20) NOT NULL,
  `asist_comercial_ext` varchar(100) NOT NULL,
  `id_asist_comrecial_ext` varchar(20) NOT NULL,
  `resp_transporte` varchar(100) NOT NULL,
  `id_resp_transporte` varchar(20) NOT NULL,
  `n_despacho` varchar(20) NOT NULL,
  `sello_adicional` varchar(20) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `despacho`
--
ALTER TABLE `despacho`
  ADD PRIMARY KEY (`id_despacho`),
  ADD KEY `FK_transportista_despacho` (`id_transportista`),
  ADD KEY `FK_camion_despacho` (`id_camion`),
  ADD KEY `FK_conductor_despacho` (`id_conductor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `despacho`
--
ALTER TABLE `despacho`
  MODIFY `id_despacho` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
