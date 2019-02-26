-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-02-2019 a las 16:56:06
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
-- Estructura de tabla para la tabla `clasificacion_ramo`
--

CREATE TABLE `clasificacion_ramo` (
  `id_clasificacion_ramo` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_configuracion_empresa` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL DEFAULT '2',
  `nombre` int(11) NOT NULL,
  `estandar` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `clasificacion_ramo`
--

INSERT INTO `clasificacion_ramo` (`id_clasificacion_ramo`, `estado`, `fecha_registro`, `id_configuracion_empresa`, `id_unidad_medida`, `nombre`, `estandar`) VALUES
(1, 1, '2019-02-08 16:29:31', 1, 2, 250, 1),
(2, 1, '2019-02-22 16:56:34', 1, 2, 1000, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clasificacion_ramo`
--
ALTER TABLE `clasificacion_ramo`
  ADD PRIMARY KEY (`id_clasificacion_ramo`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_ClasificacionRamo_ConfiguracionEmpresa` (`id_configuracion_empresa`),
  ADD KEY `FK_ClasificacionRamo_UnidadMedida` (`id_unidad_medida`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clasificacion_ramo`
--
ALTER TABLE `clasificacion_ramo`
  MODIFY `id_clasificacion_ramo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clasificacion_ramo`
--
ALTER TABLE `clasificacion_ramo`
  ADD CONSTRAINT `FK_ClasificacionRamo_ConfiguracionEmpresa` FOREIGN KEY (`id_configuracion_empresa`) REFERENCES `configuracion_empresa` (`id_configuracion_empresa`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClasificacionRamo_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
