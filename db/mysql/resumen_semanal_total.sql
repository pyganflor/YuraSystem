-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2020 a las 23:11:55
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
-- Estructura de tabla para la tabla `resumen_semanal_total`
--

CREATE TABLE `resumen_semanal_total` (
  `id_resumen_semanal_total` int(11) NOT NULL,
  `codigo_semana` varchar(5) COLLATE utf8_bin NOT NULL,
  `valor` float DEFAULT '0',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `campo` float DEFAULT '0',
  `campo_mp` float DEFAULT '0',
  `campo_mo` float DEFAULT '0',
  `campo_gip` float DEFAULT '0',
  `campo_ga` float DEFAULT '0',
  `propagacion` float DEFAULT '0',
  `propagacion_mp` float DEFAULT '0',
  `propagacion_mo` float DEFAULT '0',
  `propagacion_gip` float DEFAULT '0',
  `propagacion_ga` float DEFAULT '0',
  `cosecha` float DEFAULT '0',
  `cosecha_mp` float DEFAULT '0',
  `cosecha_mo` float DEFAULT '0',
  `cosecha_gip` float DEFAULT '0',
  `cosecha_ga` float DEFAULT '0',
  `postcosecha` float DEFAULT '0',
  `postcosecha_mp` float DEFAULT '0',
  `postcosecha_mo` float DEFAULT '0',
  `postcosecha_gip` float DEFAULT '0',
  `postcosecha_ga` float DEFAULT '0',
  `servicios_generales` float DEFAULT '0',
  `servicios_generales_mp` float DEFAULT '0',
  `servicios_generales_mo` float DEFAULT '0',
  `servicios_generales_gip` float DEFAULT '0',
  `servicios_generales_ga` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `resumen_semanal_total`
--
ALTER TABLE `resumen_semanal_total`
  ADD PRIMARY KEY (`id_resumen_semanal_total`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `resumen_semanal_total`
--
ALTER TABLE `resumen_semanal_total`
  MODIFY `id_resumen_semanal_total` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
