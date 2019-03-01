-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-02-2019 a las 15:56:43
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
-- Estructura de tabla para la tabla `rol_submenu`
--

CREATE TABLE `rol_submenu` (
  `id_rol_submenu` int(11) NOT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_submenu` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `rol_submenu`
--

INSERT INTO `rol_submenu` (`id_rol_submenu`, `id_rol`, `id_submenu`, `fecha_registro`, `estado`) VALUES
(1, 1, 1, '2018-10-02 09:50:30', 'A'),
(2, 1, 2, '2018-10-03 08:39:35', 'A'),
(3, 1, 3, '2018-10-04 12:37:08', 'A'),
(4, 1, 4, '2018-10-24 09:30:32', 'A'),
(5, 1, 7, '2018-10-24 09:37:07', 'A'),
(6, 1, 5, '2018-10-24 09:37:30', 'A'),
(7, 1, 6, '2018-10-24 09:37:43', 'A'),
(8, 1, 8, '2018-10-24 09:39:42', 'A'),
(9, 1, 9, '2018-11-05 11:55:50', 'A'),
(10, 1, 10, '2018-11-05 16:50:01', 'A'),
(11, 1, 11, '2018-11-06 08:30:51', 'A'),
(12, 1, 12, '2018-11-08 14:51:56', 'A'),
(13, 1, 13, '2018-11-14 09:38:25', 'A'),
(14, 1, 14, '2018-12-03 09:35:05', 'A'),
(15, 1, 15, '2018-12-03 11:18:06', 'A'),
(16, 1, 16, '2018-12-05 09:55:51', 'A'),
(17, 1, 17, '2018-12-17 15:54:50', 'A'),
(18, 1, 18, '2018-12-20 09:46:25', 'A'),
(19, 1, 19, '2019-01-08 15:12:40', 'A'),
(20, 1, 22, '2019-01-18 09:01:13', 'A'),
(21, 1, 20, '2019-01-18 09:01:26', 'A'),
(22, 1, 21, '2019-01-18 09:01:39', 'A'),
(23, 1, 23, '2019-02-04 08:35:43', 'A'),
(24, 1, 24, '2019-02-04 08:37:10', 'A'),
(25, 1, 25, '2019-02-08 15:37:36', 'A'),
(26, 1, 26, '2019-02-14 13:21:39', 'A'),
(27, 1, 27, '2019-02-20 09:17:42', 'A'),
(28, 1, 28, '2019-02-22 16:55:57', 'A');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `rol_submenu`
--
ALTER TABLE `rol_submenu`
  ADD PRIMARY KEY (`id_rol_submenu`),
  ADD KEY `FK_RolSubmenu_Rol` (`id_rol`),
  ADD KEY `FK_RolSubmenu_Submenu` (`id_submenu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `rol_submenu`
--
ALTER TABLE `rol_submenu`
  MODIFY `id_rol_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rol_submenu`
--
ALTER TABLE `rol_submenu`
  ADD CONSTRAINT `FK_RolSubmenu_Rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_RolSubmenu_Submenu` FOREIGN KEY (`id_submenu`) REFERENCES `submenu` (`id_submenu`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
