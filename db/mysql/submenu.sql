-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-02-2019 a las 15:56:40
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
-- Estructura de tabla para la tabla `submenu`
--

CREATE TABLE `submenu` (
  `id_submenu` int(11) NOT NULL,
  `nombre` varchar(50) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A',
  `id_menu` int(11) DEFAULT NULL,
  `url` varchar(25) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `submenu`
--

INSERT INTO `submenu` (`id_submenu`, `nombre`, `fecha_registro`, `estado`, `id_menu`, `url`) VALUES
(1, 'Menú del sistema', '2018-10-02 09:50:14', 'A', 1, 'menu_sistema'),
(2, 'Usuarios', '2018-10-03 08:39:02', 'A', 1, 'usuarios'),
(3, 'Permisos', '2018-10-04 09:43:16', 'A', 1, 'permisos'),
(4, 'Plantas variedades', '2018-10-24 09:30:13', 'A', 3, 'plantas_variedades'),
(5, 'Sectores y módulos', '2018-10-24 09:31:35', 'A', 3, 'sectores_modulos'),
(6, 'Semanas', '2018-10-24 09:31:57', 'A', 3, 'semanas'),
(7, 'Marcas', '2018-10-24 09:32:47', 'A', 7, 'marcas'),
(8, '1- Cosecha', '2018-10-24 09:36:44', 'A', 5, 'recepcion'),
(9, '2- Clasificación en verde', '2018-11-05 11:55:17', 'A', 5, 'clasificacion_verde'),
(10, 'Configuración empresa', '2018-11-05 16:48:42', 'A', 1, 'configuracion'),
(11, '2- Cliente', '2018-11-06 08:30:27', 'A', 10, 'clientes'),
(12, '1- Agencias de Carga', '2018-11-08 14:50:55', 'A', 10, 'agrencias_carga'),
(13, '3- Apertura', '2018-11-14 09:37:28', 'A', 5, 'apertura'),
(14, 'Lotes', '2018-12-03 09:32:27', 'A', 5, 'lotes'),
(15, 'Pedidos', '2018-12-03 11:15:47', 'A', 7, 'pedidos'),
(16, 'Medio de Transporte', '2018-12-05 09:54:47', 'A', 7, 'agencias_transporte'),
(17, 'Envíos', '2018-12-17 15:54:22', 'A', 7, 'envio'),
(18, '4- Clasificación en blanco', '2018-12-20 09:45:33', 'A', 5, 'clasificacion_blanco'),
(19, 'Despachos', '2019-01-08 15:09:51', 'A', 5, 'despachos'),
(20, 'Tipos comprobantes', '2019-01-18 08:59:05', 'A', 8, 'tipo_comprobante'),
(21, 'Tipos de identificación', '2019-01-18 08:59:36', 'A', 8, 'tipo_identificacion'),
(22, 'Tipos impuestos', '2019-01-18 08:59:57', 'A', 8, 'tipo_impuesto'),
(23, 'Emisión comprobantes', '2019-02-04 08:35:21', 'A', 8, 'emision_comprobantes'),
(24, 'Códigos DAE', '2019-02-04 08:36:45', 'A', 8, 'codigo_dae'),
(25, 'Comprobantes', '2019-02-08 15:37:18', 'A', 7, 'comprobante'),
(26, '4- Especificaciones', '2019-02-14 13:17:23', 'A', 10, 'especificacion'),
(27, 'Postcosecha', '2019-02-20 09:16:34', 'A', 9, 'crm_postcosecha'),
(28, '3- Cajas y presentaciones', '2019-02-22 16:55:36', 'A', 10, 'caja_presentacion');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `submenu`
--
ALTER TABLE `submenu`
  ADD PRIMARY KEY (`id_submenu`),
  ADD UNIQUE KEY `url` (`url`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_Submenu_Menu` (`id_menu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `submenu`
--
ALTER TABLE `submenu`
  MODIFY `id_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `submenu`
--
ALTER TABLE `submenu`
  ADD CONSTRAINT `FK_Submenu_Menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
