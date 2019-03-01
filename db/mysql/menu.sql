-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-02-2019 a las 15:56:17
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
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nombre` varchar(25) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A',
  `id_grupo_menu` int(11) DEFAULT NULL,
  `id_icono` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id_menu`, `nombre`, `fecha_registro`, `estado`, `id_grupo_menu`, `id_icono`) VALUES
(1, 'Seguridad', '2018-10-02 09:48:59', 'A', 1, 307),
(3, 'Parámetros cultivos', '2018-10-24 09:29:25', 'A', 1, 171),
(4, 'Contable', '2018-10-24 09:32:29', 'I', 1, 433),
(5, 'Procesos', '2018-10-24 09:36:00', 'A', 2, 92),
(6, 'Configuración empresa', '2018-11-05 11:54:52', 'I', 1, 256),
(7, 'Ventas', '2018-11-06 08:30:09', 'A', 2, 433),
(8, 'Parámetros de facturación', '2019-01-18 08:58:39', 'A', 1, 77),
(9, 'Dashboard', '2019-02-20 09:13:08', 'A', 3, 177),
(10, 'Clientes', '2019-02-27 13:26:35', 'A', 2, 64);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_Menu_GrupoMenu` (`id_grupo_menu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `FK_Menu_GrupoMenu` FOREIGN KEY (`id_grupo_menu`) REFERENCES `grupo_menu` (`id_grupo_menu`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
