-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2020 a las 15:58:55
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
-- Estructura de tabla para la tabla `resumen_fenograma_ejecucion`
--

CREATE TABLE `resumen_fenograma_ejecucion` (
  `id_resumen_fenograma_ejecucion` int(11) NOT NULL,
  `id_ciclo` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `nombre_modulo` varchar(250) COLLATE utf8_bin NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `semana` int(4) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `siglas_variedad` varchar(6) COLLATE utf8_bin NOT NULL,
  `nombre_variedad` varchar(250) COLLATE utf8_bin NOT NULL,
  `poda_siembra` char(1) COLLATE utf8_bin NOT NULL,
  `dias` int(11) NOT NULL,
  `area_m2` float NOT NULL,
  `total_x_semana_m2` float NOT NULL,
  `primera_flor` int(11) DEFAULT NULL,
  `porciento_mortalidad` float NOT NULL,
  `tallos_cosechados` int(11) DEFAULT NULL,
  `real_tallos_m2` float NOT NULL,
  `porciento_cosechado` float NOT NULL,
  `proy_tallos_m2` float NOT NULL,
  `plantas_iniciales` int(11) NOT NULL,
  `plantas_actuales` int(11) NOT NULL,
  `plantas_muertas` int(11) NOT NULL,
  `densidad_plantas_ini_m2` float NOT NULL,
  `conteo` float NOT NULL,
  `id_planta` int(11) NOT NULL,
  `siglas_planta` varchar(6) COLLATE utf8_bin NOT NULL,
  `nombre_planta` varchar(250) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `resumen_fenograma_ejecucion`
--
ALTER TABLE `resumen_fenograma_ejecucion`
  ADD PRIMARY KEY (`id_resumen_fenograma_ejecucion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `resumen_fenograma_ejecucion`
--
ALTER TABLE `resumen_fenograma_ejecucion`
  MODIFY `id_resumen_fenograma_ejecucion` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
