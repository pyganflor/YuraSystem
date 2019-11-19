-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2019 a las 15:47:21
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
-- Estructura de tabla para la tabla `indicador`
--

CREATE TABLE `indicador` (
  `id_indicador` int(11) NOT NULL,
  `nombre` char(4) COLLATE utf8_bin NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_bin NOT NULL,
  `valor` float NOT NULL DEFAULT '0',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `indicador`
--

INSERT INTO `indicador` (`id_indicador`, `nombre`, `descripcion`, `valor`, `estado`, `fecha_registro`) VALUES
(1, 'D1', 'Calibre en los últimos 7 días', 0, 1, '2019-11-19 09:02:24'),
(2, 'D2', 'Tallos clasificados en los últimos 7 días', 0, 1, '2019-11-19 09:03:38'),
(3, 'D3', 'Precio promedio por ramo de los últimos 7 días', 0, 1, '2019-11-19 09:36:52'),
(4, 'D4', 'Dinero ingresado en los útlimos 7 días', 0, 1, '2019-11-19 09:37:45'),
(5, 'D5', 'Rendimiento en los útlimos 7 días', 0, 1, '2019-11-19 09:38:23'),
(6, 'D6', 'Desecho en los útlimos 7 días', 0, 1, '2019-11-19 09:38:39'),
(7, 'D7', 'Área en producción en los últimos 4 meses', 0, 1, '2019-11-19 09:39:46'),
(8, 'D8', 'Ramos/m2/año en los últimos 4 meses', 0, 1, '2019-11-19 09:40:13'),
(9, 'D9', 'Venta $/m2/año (4 meses)', 0, 1, '2019-11-19 09:43:11'),
(10, 'D10', 'Venta $/m2/año (1 año)', 0, 1, '2019-11-19 09:46:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `indicador`
--
ALTER TABLE `indicador`
  ADD PRIMARY KEY (`id_indicador`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `indicador`
--
ALTER TABLE `indicador`
  MODIFY `id_indicador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
