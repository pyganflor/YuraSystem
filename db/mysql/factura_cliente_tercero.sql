-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-04-2019 a las 17:27:44
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
-- Estructura de tabla para la tabla `factura_cliente_tercero`
--

CREATE TABLE `factura_cliente_tercero` (
  `id_factura_cliente_tercero` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL,
  `nombre_cliente_tercero` varchar(100) NOT NULL,
  `codigo_identificacion` int(11) DEFAULT NULL,
  `identificacion` varchar(100) DEFAULT NULL,
  `codigo_impuesto` int(2) NOT NULL,
  `codigo_impuesto_porcentaje` int(11) NOT NULL,
  `codigo_pais` varchar(5) NOT NULL,
  `provincia` varchar(200) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `almacen` varchar(100) DEFAULT NULL,
  `direccion` varchar(500) NOT NULL,
  `dae` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `factura_cliente_tercero`
--
ALTER TABLE `factura_cliente_tercero`
  ADD PRIMARY KEY (`id_factura_cliente_tercero`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `factura_cliente_tercero`
--
ALTER TABLE `factura_cliente_tercero`
  MODIFY `id_factura_cliente_tercero` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
