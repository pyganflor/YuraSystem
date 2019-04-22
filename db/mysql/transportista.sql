-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-04-2019 a las 23:05:17
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
-- Estructura de tabla para la tabla `transportista`
--

CREATE TABLE `transportista` (
  `nombre_empresa` varchar(100) NOT NULL,
  `ruc` varchar(20) NOT NULL,
  `encargado` varchar(100) NOT NULL,
  `ruc_encargado` varchar(20) NOT NULL,
  `telefono_encargado` varchar(20) NOT NULL,
  `direccion_empresa` varchar(300) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_transportista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `transportista`
--

INSERT INTO `transportista` (`nombre_empresa`, `ruc`, `encargado`, `ruc_encargado`, `telefono_encargado`, `direccion_empresa`, `estado`, `fecha_registro`, `id_transportista`) VALUES
('sdthrthrt', 'rtrrtrtr', 'trtrttrtrth', 'rttrrtrtdgfdf', 'dfesrdtfyghj', 'sretjfdgsrteyrhg', 1, '2019-04-05 15:04:09', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `transportista`
--
ALTER TABLE `transportista`
  ADD PRIMARY KEY (`id_transportista`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `transportista`
--
ALTER TABLE `transportista`
  MODIFY `id_transportista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
