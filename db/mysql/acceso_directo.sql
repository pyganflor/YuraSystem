-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2020 a las 22:45:12
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
-- Estructura de tabla para la tabla `acceso_directo`
--

CREATE TABLE `acceso_directo` (
  `id_acceso_directo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_submenu` int(11) NOT NULL,
  `id_icono` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso_directo`
--
ALTER TABLE `acceso_directo`
  ADD PRIMARY KEY (`id_acceso_directo`),
  ADD KEY `FK_AccessoDirecto_Usuario` (`id_usuario`),
  ADD KEY `FK_AccessoDirecto_Submenu` (`id_submenu`),
  ADD KEY `FK_AccessoDirecto_Icono` (`id_icono`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso_directo`
--
ALTER TABLE `acceso_directo`
  MODIFY `id_acceso_directo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso_directo`
--
ALTER TABLE `acceso_directo`
  ADD CONSTRAINT `FK_AccessoDirecto_Icono` FOREIGN KEY (`id_icono`) REFERENCES `icono` (`id_icono`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_AccessoDirecto_Submenu` FOREIGN KEY (`id_submenu`) REFERENCES `submenu` (`id_submenu`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_AccessoDirecto_Usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
