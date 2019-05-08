-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-05-2019 a las 20:05:17
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
-- Estructura de tabla para la tabla `inventario_frio`
--

CREATE TABLE `inventario_frio` (
  `id_inventario_frio` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_ramo` int(11) NOT NULL,
  `id_empaque_e` int(11) DEFAULT NULL,
  `id_empaque_p` int(11) NOT NULL,
  `tallos_x_ramo` int(11) DEFAULT NULL,
  `longitud_ramo` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `disponibles` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_unidad_medida` int(11) DEFAULT NULL,
  `disponibilidad` tinyint(1) NOT NULL DEFAULT '1',
  `descripcion` varchar(500) COLLATE utf8_bin NOT NULL,
  `basura` tinyint(1) NOT NULL DEFAULT '0',
  `id_clasificacion_blanco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `inventario_frio`
--
ALTER TABLE `inventario_frio`
  ADD PRIMARY KEY (`id_inventario_frio`),
  ADD KEY `FK_InventarioFrio_Variedad` (`id_variedad`),
  ADD KEY `FK_InventarioFrio_ClasificacionRamo` (`id_clasificacion_ramo`),
  ADD KEY `FK_InventarioFrio_EmpaqueE` (`id_empaque_e`),
  ADD KEY `FK_InventarioFrio_EmpaqueP` (`id_empaque_p`),
  ADD KEY `FK_InventarioFrio_UnidadMedida` (`id_unidad_medida`),
  ADD KEY `FK_InventarioFrio_ClasificacionBlanco` (`id_clasificacion_blanco`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `inventario_frio`
--
ALTER TABLE `inventario_frio`
  MODIFY `id_inventario_frio` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `inventario_frio`
--
ALTER TABLE `inventario_frio`
  ADD CONSTRAINT `FK_InventarioFrio_ClasificacionBlanco` FOREIGN KEY (`id_clasificacion_blanco`) REFERENCES `clasificacion_blanco` (`id_clasificacion_blanco`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_ClasificacionRamo` FOREIGN KEY (`id_clasificacion_ramo`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_EmpaqueE` FOREIGN KEY (`id_empaque_e`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_EmpaqueP` FOREIGN KEY (`id_empaque_p`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
