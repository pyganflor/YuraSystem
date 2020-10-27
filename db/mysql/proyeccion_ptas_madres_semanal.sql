-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-10-2020 a las 19:13:29
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
-- Estructura de tabla para la tabla `proyeccion_ptas_madres_semanal`
--

CREATE TABLE `proyeccion_ptas_madres_semanal` (
  `id_proyeccion_ptas_madres_semanal` int(11) NOT NULL,
  `id_cama` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `semana` int(4) NOT NULL,
  `tipo` char(1) COLLATE utf8_bin NOT NULL COMMENT 'S-siembra; I-info; C-cosecha; N-vacia',
  `tallos_cosechados` int(11) DEFAULT NULL,
  `tallos_proyectados` float DEFAULT NULL,
  `proyeccion` tinyint(1) NOT NULL DEFAULT '0',
  `semana_cosecha` int(11) NOT NULL,
  `total_semanas_cosecha` int(11) NOT NULL,
  `esq_x_planta` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `proyeccion_ptas_madres_semanal`
--
ALTER TABLE `proyeccion_ptas_madres_semanal`
  ADD PRIMARY KEY (`id_proyeccion_ptas_madres_semanal`),
  ADD KEY `FK_ProyeccionPtasMadresSemanal_Cama` (`id_cama`),
  ADD KEY `FK_ProyeccionPtasMadresSemanal_Variedad` (`id_variedad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `proyeccion_ptas_madres_semanal`
--
ALTER TABLE `proyeccion_ptas_madres_semanal`
  MODIFY `id_proyeccion_ptas_madres_semanal` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `proyeccion_ptas_madres_semanal`
--
ALTER TABLE `proyeccion_ptas_madres_semanal`
  ADD CONSTRAINT `FK_ProyeccionPtasMadresSemanal_Cama` FOREIGN KEY (`id_cama`) REFERENCES `cama` (`id_cama`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ProyeccionPtasMadresSemanal_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
