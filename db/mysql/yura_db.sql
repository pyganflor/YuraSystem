-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-02-2019 a las 22:32:16
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
-- Estructura de tabla para la tabla `agencia_carga`
--

CREATE TABLE `agencia_carga` (
  `id_agencia_carga` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `codigo` varchar(255) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aerolineas`
--

CREATE TABLE `agencia_transporte` (
  `id_agencia_transporte` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_bin NOT NULL,
  `tipo_agencia` varchar(1) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `tabla` varchar(250) COLLATE utf8_bin NOT NULL,
  `codigo` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accion` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'I',
  `id_usuario` int(11) NOT NULL,
  `ip` varchar(250) COLLATE utf8_bin NOT NULL,
  `observacion` varchar(180) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `tabla`, `codigo`, `fecha_registro`, `accion`, `id_usuario`, `ip`, `observacion`) VALUES
(1, 'CONFIGURACION_EMPRESA', 3, '2019-02-08 16:31:10', 'I', 1, '127.0.0.1', 'INSERCIÓN SATISFACTORIA DE UNA NUEVA CLASIFICACIÓN UNITARIA'),
(2, 'CLASIFICACION_RAMO', 1, '2019-02-08 16:31:10', 'I', 1, '127.0.0.1', 'INSERCIÓN SATISFACTORIA DE UNA NUEVA CLASIFICACIÓN POR RAMOS'),
(3, 'CONFIGURACION_EMPRESA', 3, '2019-02-08 16:31:31', 'I', 1, '127.0.0.1', 'INSERCIÓN SATISFACTORIA DE UNA NUEVA CLASIFICACIÓN UNITARIA'),
(4, 'CLASIFICACION_RAMO', 1, '2019-02-08 16:31:31', 'I', 1, '127.0.0.1', 'INSERCIÓN SATISFACTORIA DE UNA NUEVA CLASIFICACIÓN POR RAMOS'),
(5, 'CLASIFICACION_RAMO', 1, '2019-02-08 16:31:31', 'I', 1, '127.0.0.1', 'INSERCIÓN SATISFACTORIA DE UN NUEVO EMPAQUE');

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
  `nombre` varchar(25) COLLATE utf8_bin NOT NULL,
  `estandar` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `clasificacion_ramo`
--

INSERT INTO `clasificacion_ramo` (`id_clasificacion_ramo`, `estado`, `fecha_registro`, `id_configuracion_empresa`, `id_unidad_medida`, `nombre`, `estandar`) VALUES
(1, 1, '2019-02-08 16:29:31', 1, 2, '250', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion_unitaria`
--

CREATE TABLE `clasificacion_unitaria` (
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `nombre` varchar(25) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_configuracion_empresa` int(11) NOT NULL,
  `id_unidad_medida` int(11) DEFAULT '1',
  `id_clasificacion_ramo_estandar` int(11) DEFAULT NULL,
  `id_clasificacion_ramo_real` int(11) DEFAULT NULL,
  `tallos_x_ramo` int(11) NOT NULL DEFAULT '5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `clasificacion_unitaria`
--

INSERT INTO `clasificacion_unitaria` (`id_clasificacion_unitaria`, `nombre`, `fecha_registro`, `estado`, `id_configuracion_empresa`, `id_unidad_medida`, `id_clasificacion_ramo_estandar`, `id_clasificacion_ramo_real`, `tallos_x_ramo`) VALUES
(3, '10|25', '2019-02-08 16:27:26', 1, 1, 2, 1, 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion_verde`
--

CREATE TABLE `clasificacion_verde` (
  `id_clasificacion_verde` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_ingreso` date NOT NULL,
  `id_semana` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `personal` int(11) DEFAULT NULL,
  `hora_inicio` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '08:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_agenciacarga`
--

CREATE TABLE `cliente_agenciacarga` (
  `id_cliente_agencia_carga` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_agencia_carga` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_pedido_especificacion`
--

CREATE TABLE `cliente_pedido_especificacion` (
  `id_cliente_pedido_especificacion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_especificacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigo_dae`
--

CREATE TABLE `codigo_dae` (
  `id_codigo_dae` int(11) NOT NULL,
  `codigo_pais` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `dae` varchar(5) NOT NULL,
  `codigo_dae` varchar(20) NOT NULL,
  `mes` char(2) NOT NULL,
  `anno` char(4) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coloracion`
--

CREATE TABLE `coloracion` (
  `id_coloracion` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `fondo` varchar(25) COLLATE utf8_bin NOT NULL,
  `texto` varchar(25) COLLATE utf8_bin NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_marcacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id_comprobante` int(11) NOT NULL,
  `id_envio` int(11) DEFAULT NULL,
  `clave_acceso` varchar(49) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0',
  `tipo_comprobante` char(2) NOT NULL,
  `monto_total` float(10,2) DEFAULT NULL,
  `fecha_emision` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `causa` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_empresa`
--

CREATE TABLE `configuracion_empresa` (
  `id_configuracion_empresa` int(11) NOT NULL,
  `nombre` varchar(300) COLLATE utf8_bin NOT NULL,
  `cantidad_usuarios` int(10) NOT NULL,
  `cantidad_hectareas` decimal(10,2) NOT NULL,
  `propagacion` varchar(1000) COLLATE utf8_bin DEFAULT NULL COMMENT 'nombre de procesos',
  `campo` varchar(1000) COLLATE utf8_bin DEFAULT NULL COMMENT 'nombre de procesos',
  `postcocecha` varchar(1000) COLLATE utf8_bin DEFAULT NULL COMMENT 'nombre de procesos',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `moneda` varchar(10) COLLATE utf8_bin NOT NULL,
  `ramos_x_caja` int(11) NOT NULL,
  `razon_social` varchar(300) COLLATE utf8_bin NOT NULL,
  `direccion_matriz` varchar(500) COLLATE utf8_bin NOT NULL,
  `codigo_pais` char(2) COLLATE utf8_bin NOT NULL,
  `direccion_establecimiento` varchar(300) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `configuracion_empresa`
--

INSERT INTO `configuracion_empresa` (`id_configuracion_empresa`, `nombre`, `cantidad_usuarios`, `cantidad_hectareas`, `propagacion`, `campo`, `postcocecha`, `fecha_registro`, `estado`, `moneda`, `ramos_x_caja`, `razon_social`, `direccion_matriz`, `codigo_pais`, `direccion_establecimiento`) VALUES
(1, 'Pyganflor', 5, '15000.00', '||', '|', 'Cosecha|Clasificación en verde|Apertura|Clasificación en blanco|Frío', '2018-11-07 08:46:29', 1, 'usd', 40, 'Pyganflor S.A.', 'Pasaje La Paz E-901 y Ave. 6 de Diciembre', 'EC', 'Vía San José de Minas, Vía al Pisque');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_user`
--

CREATE TABLE `configuracion_user` (
  `id_configuracion_user` int(11) NOT NULL,
  `fixed_layout` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'S',
  `boxed_layout` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'N',
  `toggle_color_config` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'N',
  `skin` varchar(25) COLLATE utf8_bin DEFAULT 'skin-blue',
  `config_online` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'S',
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `configuracion_user`
--

INSERT INTO `configuracion_user` (`id_configuracion_user`, `fixed_layout`, `boxed_layout`, `toggle_color_config`, `skin`, `config_online`, `id_usuario`) VALUES
(1, 'S', 'N', 'N', 'skin-blue', 'S', 1),
(2, 'S', 'N', 'N', 'skin-green', 'S', 2),
(3, 'S', 'N', 'N', 'skin-purple', 'S', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumo`
--

CREATE TABLE `consumo` (
  `id_consumo` int(11) NOT NULL,
  `fecha_pedidos` date NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

CREATE TABLE `contacto` (
  `id_contacto` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `correo` varchar(250) COLLATE utf8_bin NOT NULL,
  `telefono` varchar(250) COLLATE utf8_bin NOT NULL,
  `direccion` varchar(1000) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cosecha`
--

CREATE TABLE `cosecha` (
  `id_cosecha` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `personal` int(11) NOT NULL,
  `hora_inicio` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '08:00',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desglose_envio_factura`
--

CREATE TABLE `desglose_envio_factura` (
  `id_desglose_envio_factura` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL,
  `codigo_principal` varchar(12) NOT NULL,
  `descripcion` varchar(300) NOT NULL,
  `cantidad` float(10,2) NOT NULL,
  `precio_unitario` float(10,2) NOT NULL,
  `descuento` float(10,2) NOT NULL,
  `precio_total_sin_impuesto` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desglose_recepcion`
--

CREATE TABLE `desglose_recepcion` (
  `id_desglose_recepcion` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_recepcion` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `cantidad_mallas` int(3) NOT NULL,
  `tallos_x_malla` int(2) NOT NULL,
  `id_modulo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_clasificacion_verde`
--

CREATE TABLE `detalle_clasificacion_verde` (
  `id_detalle_clasificacion_verde` int(11) NOT NULL,
  `id_clasificacion_verde` int(11) NOT NULL,
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `cantidad_ramos` int(11) NOT NULL,
  `tallos_x_ramos` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_ingreso` varchar(16) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cliente`
--

CREATE TABLE `detalle_cliente` (
  `id_detalle_cliente` int(11) NOT NULL,
  `nombre` varchar(1000) COLLATE utf8_bin NOT NULL,
  `direccion` varchar(1000) COLLATE utf8_bin NOT NULL,
  `provincia` varchar(1000) COLLATE utf8_bin NOT NULL,
  `codigo_pais` varchar(2) COLLATE utf8_bin NOT NULL,
  `telefono` varchar(250) COLLATE utf8_bin NOT NULL,
  `ruc` varchar(250) COLLATE utf8_bin NOT NULL,
  `correo` varchar(100) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_cliente` int(11) NOT NULL,
  `codigo_identificacion` char(2) COLLATE utf8_bin NOT NULL,
  `codigo_impuesto` int(1) NOT NULL,
  `codigo_porcentaje_impuesto` varchar(6) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cliente_contacto`
--

CREATE TABLE `detalle_cliente_contacto` (
  `id_detalle_cliente_contacto` int(11) NOT NULL,
  `id_detalle_cliente` int(11) NOT NULL,
  `id_contacto` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_empaque`
--

CREATE TABLE `detalle_empaque` (
  `id_detalle_empaque` int(11) NOT NULL,
  `id_empaque` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_ramo` int(11) NOT NULL,
  `cantidad` int(10) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_envio`
--

CREATE TABLE `detalle_envio` (
  `id_detalle_envio` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL,
  `id_especificacion` int(11) NOT NULL,
  `id_agencia_transporte` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `envio` int(11) NOT NULL,
  `form` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_especificacionempaque`
--

CREATE TABLE `detalle_especificacionempaque` (
  `id_detalle_especificacionempaque` int(11) NOT NULL,
  `id_especificacion_empaque` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_ramo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `id_empaque_e` int(11) NOT NULL,
  `id_empaque_p` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tallos_x_ramos` int(11) DEFAULT NULL,
  `longitud_ramo` int(11) DEFAULT NULL,
  `id_unidad_medida` int(11) DEFAULT NULL,
  `id_grosor_ramo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id_detalle_factura` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL,
  `razon_social_emisor` varchar(300) NOT NULL,
  `nombre_comercial_emisor` varchar(300) NOT NULL,
  `direccion_matriz_emisor` varchar(300) NOT NULL,
  `direccion_establecimiento_emisor` varchar(300) NOT NULL,
  `obligado_contabilidad` tinyint(1) NOT NULL,
  `tipo_identificacion_comprador` char(2) NOT NULL,
  `razon_social_comprador` varchar(300) NOT NULL,
  `identificacion_comprador` bigint(20) NOT NULL,
  `total_sin_impuestos` float(10,2) NOT NULL,
  `total_descuento` float(10,0) NOT NULL,
  `propina` float(10,2) NOT NULL,
  `importe_total` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle_pedido` int(11) NOT NULL,
  `id_cliente_especificacion` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_agencia_carga` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distribucion`
--

CREATE TABLE `distribucion` (
  `id_distribucion` int(11) NOT NULL,
  `id_marcacion` int(11) NOT NULL,
  `id_coloracion` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id_documento` int(11) NOT NULL,
  `entidad` varchar(250) COLLATE utf8_bin NOT NULL,
  `codigo` int(11) NOT NULL,
  `nombre_campo` varchar(250) COLLATE utf8_bin NOT NULL,
  `tipo_dato` varchar(25) COLLATE utf8_bin NOT NULL COMMENT 'int, char, varchar, boolean, date, datetime',
  `int` int(11) DEFAULT NULL,
  `float` float DEFAULT NULL,
  `char` char(1) COLLATE utf8_bin DEFAULT NULL,
  `varchar` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `boolean` tinyint(1) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `descripcion` varchar(4000) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empaque`
--

CREATE TABLE `empaque` (
  `id_empaque` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `id_configuracion_empresa` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'C' COMMENT 'C => Caja E => Envoltura P => Presentacion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `empaque`
--

INSERT INTO `empaque` (`id_empaque`, `nombre`, `id_configuracion_empresa`, `estado`, `fecha_registro`, `tipo`) VALUES
(1, 'Half|0.5', 1, 1, '2019-02-08 16:31:31', 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envio`
--

CREATE TABLE `envio` (
  `id_envio` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_envio` datetime NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especificacion`
--

CREATE TABLE `especificacion` (
  `id_especificacion` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `descripcion` varchar(4000) COLLATE utf8_bin DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `tipo` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especificacion_empaque`
--

CREATE TABLE `especificacion_empaque` (
  `id_especificacion_empaque` int(11) NOT NULL,
  `id_especificacion` int(11) NOT NULL,
  `id_empaque` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `imagen` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grosor_ramo`
--

CREATE TABLE `grosor_ramo` (
  `id_grosor_ramo` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `grosor_ramo`
--

INSERT INTO `grosor_ramo` (`id_grosor_ramo`, `nombre`, `estado`, `fecha_registro`) VALUES
(1, 'HOJAS', 1, '2018-12-20 16:03:07'),
(2, 'LARGA', 1, '2018-12-20 16:03:07'),
(3, 'CORTA', 1, '2018-12-20 16:03:07'),
(4, 'OTRO', 0, '2018-12-20 16:03:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_menu`
--

CREATE TABLE `grupo_menu` (
  `id_grupo_menu` int(11) NOT NULL,
  `nombre` varchar(25) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `grupo_menu`
--

INSERT INTO `grupo_menu` (`id_grupo_menu`, `nombre`, `fecha_registro`, `estado`) VALUES
(1, 'ADMINISTRACIÓN', '2018-10-02 09:11:52', 'A'),
(2, '3 - POSTCOSECHA', '2018-10-24 09:33:03', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `icono`
--

CREATE TABLE `icono` (
  `id_icono` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `icono`
--

INSERT INTO `icono` (`id_icono`, `nombre`, `estado`) VALUES
(1, 'adjust', 'A'),
(2, 'anchor', 'A'),
(3, 'archive', 'A'),
(4, 'area-chart', 'A'),
(5, 'arrows', 'A'),
(6, 'arrows-h', 'A'),
(7, 'arrows-v', 'A'),
(8, 'asterisk', 'A'),
(9, 'at', 'A'),
(10, 'balance-scale', 'A'),
(11, 'ban', 'A'),
(12, 'bar-chart', 'A'),
(13, 'barcode', 'A'),
(14, 'bars', 'A'),
(15, 'battery-empty', 'A'),
(16, 'battery-full', 'A'),
(17, 'battery-half', 'A'),
(18, 'battery-quarter', 'A'),
(19, 'battery-three-quarters', 'A'),
(20, 'bed', 'A'),
(21, 'beer', 'A'),
(22, 'bell', 'A'),
(23, 'bell-o', 'A'),
(24, 'bell-slash', 'A'),
(25, 'bell-slash-o', 'A'),
(26, 'bicycle', 'A'),
(27, 'binoculars', 'A'),
(28, 'birthday-cake', 'A'),
(29, 'bolt', 'A'),
(30, 'bomb', 'A'),
(31, 'book', 'A'),
(32, 'bookmark', 'A'),
(33, 'bookmark-o', 'A'),
(34, 'briefcase', 'A'),
(35, 'bug', 'A'),
(36, 'building', 'A'),
(37, 'building-o', 'A'),
(38, 'bullhorn', 'A'),
(39, 'bullseye', 'A'),
(40, 'bus', 'A'),
(41, 'calculator', 'A'),
(42, 'calendar', 'A'),
(43, 'calendar-check-o', 'A'),
(44, 'calendar-minus-o', 'A'),
(45, 'calendar-o', 'A'),
(46, 'calendar-plus-o', 'A'),
(47, 'calendar-times-o', 'A'),
(48, 'camera', 'A'),
(49, 'camera-retro', 'A'),
(50, 'car', 'A'),
(51, 'caret-square-o-down', 'A'),
(52, 'caret-square-o-left', 'A'),
(53, 'caret-square-o-right', 'A'),
(54, 'caret-square-o-up', 'A'),
(55, 'cart-arrow-down', 'A'),
(56, 'cart-plus', 'A'),
(57, 'cc', 'A'),
(58, 'certificate', 'A'),
(59, 'check', 'A'),
(60, 'check-circle', 'A'),
(61, 'check-circle-o', 'A'),
(62, 'check-square', 'A'),
(63, 'check-square-o', 'A'),
(64, 'child', 'A'),
(65, 'circle', 'A'),
(66, 'circle-o', 'A'),
(67, 'circle-o-notch', 'A'),
(68, 'circle-thin', 'A'),
(69, 'clock-o', 'A'),
(70, 'clone', 'A'),
(71, 'cloud', 'A'),
(72, 'cloud-download', 'A'),
(73, 'cloud-upload', 'A'),
(74, 'code', 'A'),
(75, 'code-fork', 'A'),
(76, 'coffee', 'A'),
(77, 'cog', 'A'),
(78, 'cogs', 'A'),
(79, 'comment', 'A'),
(80, 'comment-o', 'A'),
(81, 'commenting', 'A'),
(82, 'commenting-o', 'A'),
(83, 'comments', 'A'),
(84, 'comments-o', 'A'),
(85, 'compass', 'A'),
(86, 'copyright', 'A'),
(87, 'creative-commons', 'A'),
(88, 'credit-card', 'A'),
(89, 'crop', 'A'),
(90, 'crosshairs', 'A'),
(91, 'cube', 'A'),
(92, 'cubes', 'A'),
(93, 'cutlery', 'A'),
(94, 'database', 'A'),
(95, 'desktop', 'A'),
(96, 'diamond', 'A'),
(97, 'dot-circle-o', 'A'),
(98, 'download', 'A'),
(99, 'ellipsis-h', 'A'),
(100, 'ellipsis-v', 'A'),
(101, 'envelope', 'A'),
(102, 'envelope-o', 'A'),
(103, 'envelope-square', 'A'),
(104, 'eraser', 'A'),
(105, 'exchange', 'A'),
(106, 'exclamation', 'A'),
(107, 'exclamation-circle', 'A'),
(108, 'exclamation-triangle', 'A'),
(109, 'external-link', 'A'),
(110, 'external-link-square', 'A'),
(111, 'eye', 'A'),
(112, 'eye-slash', 'A'),
(113, 'eyedropper', 'A'),
(114, 'fax', 'A'),
(115, 'female', 'A'),
(116, 'fighter-jet', 'A'),
(117, 'file-archive-o', 'A'),
(118, 'file-audio-o', 'A'),
(119, 'file-code-o', 'A'),
(120, 'file-excel-o', 'A'),
(121, 'file-image-o', 'A'),
(122, 'file-pdf-o', 'A'),
(123, 'file-powerpoint-o', 'A'),
(124, 'file-video-o', 'A'),
(125, 'file-word-o', 'A'),
(126, 'film', 'A'),
(127, 'filter', 'A'),
(128, 'fire', 'A'),
(129, 'fire-extinguisher', 'A'),
(130, 'flag', 'A'),
(131, 'flag-checkered', 'A'),
(132, 'flag-o', 'A'),
(133, 'flask', 'A'),
(134, 'folder', 'A'),
(135, 'folder-o', 'A'),
(136, 'folder-open', 'A'),
(137, 'folder-open-o', 'A'),
(138, 'frown-o', 'A'),
(139, 'futbol-o', 'A'),
(140, 'gamepad', 'A'),
(141, 'gavel', 'A'),
(142, 'gift', 'A'),
(143, 'glass', 'A'),
(144, 'globe', 'A'),
(145, 'graduation-cap', 'A'),
(146, 'hand-lizard-o', 'A'),
(147, 'hand-paper-o', 'A'),
(148, 'hand-peace-o', 'A'),
(149, 'hand-pointer-o', 'A'),
(150, 'hand-rock-o', 'A'),
(151, 'hand-scissors-o', 'A'),
(152, 'hand-spock-o', 'A'),
(153, 'hdd-o', 'A'),
(154, 'headphones', 'A'),
(155, 'heart', 'A'),
(156, 'heart-o', 'A'),
(157, 'heartbeat', 'A'),
(158, 'history', 'A'),
(159, 'home', 'A'),
(160, 'hourglass', 'A'),
(161, 'hourglass-end', 'A'),
(162, 'hourglass-half', 'A'),
(163, 'hourglass-o', 'A'),
(164, 'hourglass-start', 'A'),
(165, 'i-cursor', 'A'),
(166, 'inbox', 'A'),
(167, 'industry', 'A'),
(168, 'keyboard-o', 'A'),
(169, 'language', 'A'),
(170, 'laptop', 'A'),
(171, 'leaf', 'A'),
(172, 'lemon-o', 'A'),
(173, 'level-down', 'A'),
(174, 'level-up', 'A'),
(175, 'life-ring', 'A'),
(176, 'lightbulb-o', 'A'),
(177, 'line-chart', 'A'),
(178, 'location-arrow', 'A'),
(179, 'lock', 'A'),
(180, 'magic', 'A'),
(181, 'magnet', 'A'),
(182, 'male', 'A'),
(183, 'map', 'A'),
(184, 'map-marker', 'A'),
(185, 'map-o', 'A'),
(186, 'map-pin', 'A'),
(187, 'map-signs', 'A'),
(188, 'meh-o', 'A'),
(189, 'microphone', 'A'),
(190, 'microphone-slash', 'A'),
(191, 'minus', 'A'),
(192, 'minus-circle', 'A'),
(193, 'minus-square', 'A'),
(194, 'minus-square-o', 'A'),
(195, 'mobile', 'A'),
(196, 'money', 'A'),
(197, 'moon-o', 'A'),
(198, 'motorcycle', 'A'),
(199, 'mouse-pointer', 'A'),
(200, 'music', 'A'),
(201, 'newspaper-o', 'A'),
(202, 'object-group', 'A'),
(203, 'object-ungroup', 'A'),
(204, 'paint-brush', 'A'),
(205, 'paper-plane', 'A'),
(206, 'paper-plane-o', 'A'),
(207, 'paw', 'A'),
(208, 'pencil', 'A'),
(209, 'pencil-square', 'A'),
(210, 'pencil-square-o', 'A'),
(211, 'phone', 'A'),
(212, 'phone-square', 'A'),
(213, 'picture-o', 'A'),
(214, 'pie-chart', 'A'),
(215, 'plane', 'A'),
(216, 'plug', 'A'),
(217, 'plus', 'A'),
(218, 'plus-circle', 'A'),
(219, 'plus-square', 'A'),
(220, 'plus-square-o', 'A'),
(221, 'power-off', 'A'),
(222, 'print', 'A'),
(223, 'puzzle-piece', 'A'),
(224, 'qrcode', 'A'),
(225, 'question', 'A'),
(226, 'question-circle', 'A'),
(227, 'quote-left', 'A'),
(228, 'quote-right', 'A'),
(229, 'random', 'A'),
(230, 'recycle', 'A'),
(231, 'refresh', 'A'),
(232, 'registered', 'A'),
(233, 'reply', 'A'),
(234, 'reply-all', 'A'),
(235, 'retweet', 'A'),
(236, 'road', 'A'),
(237, 'rocket', 'A'),
(238, 'rss', 'A'),
(239, 'rss-square', 'A'),
(240, 'search', 'A'),
(241, 'search-minus', 'A'),
(242, 'search-plus', 'A'),
(243, 'server', 'A'),
(244, 'share', 'A'),
(245, 'share-alt', 'A'),
(246, 'share-alt-square', 'A'),
(247, 'share-square', 'A'),
(248, 'share-square-o', 'A'),
(249, 'shield', 'A'),
(250, 'ship', 'A'),
(251, 'shopping-cart', 'A'),
(252, 'sign-in', 'A'),
(253, 'sign-out', 'A'),
(254, 'signal', 'A'),
(255, 'sitemap', 'A'),
(256, 'sliders', 'A'),
(257, 'smile-o', 'A'),
(258, 'sort', 'A'),
(259, 'sort-alpha-asc', 'A'),
(260, 'sort-alpha-desc', 'A'),
(261, 'sort-amount-asc', 'A'),
(262, 'sort-amount-desc', 'A'),
(263, 'sort-asc', 'A'),
(264, 'sort-desc', 'A'),
(265, 'sort-numeric-asc', 'A'),
(266, 'sort-numeric-desc', 'A'),
(267, 'space-shuttle', 'A'),
(268, 'spinner', 'A'),
(269, 'spoon', 'A'),
(270, 'square', 'A'),
(271, 'square-o', 'A'),
(272, 'star', 'A'),
(273, 'star-half', 'A'),
(274, 'star-half-o', 'A'),
(275, 'star-o', 'A'),
(276, 'sun-o', 'A'),
(277, 'tablet', 'A'),
(278, 'tachometer', 'A'),
(279, 'tag', 'A'),
(280, 'tags', 'A'),
(281, 'tasks', 'A'),
(282, 'taxi', 'A'),
(283, 'television', 'A'),
(284, 'terminal', 'A'),
(285, 'thumb-tack', 'A'),
(286, 'thumbs-down', 'A'),
(287, 'thumbs-o-down', 'A'),
(288, 'thumbs-o-up', 'A'),
(289, 'thumbs-up', 'A'),
(290, 'ticket', 'A'),
(291, 'times', 'A'),
(292, 'times-circle', 'A'),
(293, 'times-circle-o', 'A'),
(294, 'tint', 'A'),
(295, 'toggle-off', 'A'),
(296, 'toggle-on', 'A'),
(297, 'trademark', 'A'),
(298, 'trash', 'A'),
(299, 'trash-o', 'A'),
(300, 'tree', 'A'),
(301, 'trophy', 'A'),
(302, 'truck', 'A'),
(303, 'tty', 'A'),
(304, 'umbrella', 'A'),
(305, 'university', 'A'),
(306, 'unlock', 'A'),
(307, 'unlock-alt', 'A'),
(308, 'upload', 'A'),
(309, 'user', 'A'),
(310, 'user-plus', 'A'),
(311, 'user-secret', 'A'),
(312, 'user-times', 'A'),
(313, 'users', 'A'),
(314, 'video-camera', 'A'),
(315, 'volume-down', 'A'),
(316, 'volume-off', 'A'),
(317, 'volume-up', 'A'),
(318, 'wheelchair', 'A'),
(319, 'wifi', 'A'),
(320, 'wrench', 'A'),
(321, 'hand-o-right', 'A'),
(322, 'hand-o-down', 'A'),
(323, 'hand-o-left', 'A'),
(324, 'hand-o-up', 'A'),
(325, 'ambulance', 'A'),
(326, 'subway', 'A'),
(327, 'train', 'A'),
(328, 'genderless', 'A'),
(329, 'mars', 'A'),
(330, 'mars-double', 'A'),
(331, 'mars-stroke', 'A'),
(332, 'mars-stroke-h', 'A'),
(333, 'mars-stroke-v', 'A'),
(334, 'mercury', 'A'),
(335, 'neuter', 'A'),
(336, 'transgender', 'A'),
(337, 'transgender-alt', 'A'),
(338, 'venus', 'A'),
(339, 'venus-double', 'A'),
(340, 'venus-mars', 'A'),
(343, 'file', 'A'),
(347, 'file-text', 'A'),
(364, 'file-text-o', 'A'),
(365, 'cc-amex', 'A'),
(366, 'cc-diners-club', 'A'),
(367, 'cc-discover', 'A'),
(403, 'cc-jcb', 'A'),
(404, 'cc-mastercard', 'A'),
(405, 'cc-paypal', 'A'),
(406, 'cc-stripe', 'A'),
(407, 'cc-visa', 'A'),
(408, 'google-wallet', 'A'),
(409, 'paypal', 'A'),
(416, 'btc', 'A'),
(417, 'eur', 'A'),
(418, 'gbp', 'A'),
(419, 'gg', 'A'),
(426, 'gg-circle', 'A'),
(427, 'ils', 'A'),
(428, 'inr', 'A'),
(429, 'jpy', 'A'),
(430, 'krw', 'A'),
(431, 'rub', 'A'),
(432, 'try', 'A'),
(433, 'usd', 'A'),
(434, 'align-center', 'A'),
(470, 'align-justify', 'A'),
(471, 'align-left', 'A'),
(472, 'align-right', 'A'),
(473, 'bold', 'A'),
(474, 'chain-broken', 'A'),
(475, 'clipboard', 'A'),
(476, 'columns', 'A'),
(477, 'files-o', 'A'),
(478, 'floppy-o', 'A'),
(479, 'font', 'A'),
(480, 'header', 'A'),
(481, 'indent', 'A'),
(482, 'italic', 'A'),
(483, 'link', 'A'),
(484, 'list', 'A'),
(485, 'list-alt', 'A'),
(486, 'list-ol', 'A'),
(487, 'list-ul', 'A'),
(488, 'outdent', 'A'),
(489, 'paperclip', 'A'),
(490, 'paragraph', 'A'),
(491, 'repeat', 'A'),
(492, 'scissors', 'A'),
(493, 'strikethrough', 'A'),
(494, 'subscript', 'A'),
(495, 'superscript', 'A'),
(496, 'table', 'A'),
(497, 'text-height', 'A'),
(498, 'text-width', 'A'),
(499, 'th', 'A'),
(500, 'th-large', 'A'),
(501, 'th-list', 'A'),
(502, 'underline', 'A'),
(503, 'undo', 'A'),
(504, 'angle-double-down', 'A'),
(505, 'angle-double-left', 'A'),
(506, 'angle-double-right', 'A'),
(507, 'angle-double-up', 'A'),
(508, 'angle-down', 'A'),
(509, 'angle-left', 'A'),
(510, 'angle-right', 'A'),
(511, 'angle-up', 'A'),
(512, 'arrow-circle-down', 'A'),
(513, 'arrow-circle-o-right', 'A'),
(522, 'arrow-circle-left', 'A'),
(523, 'arrow-circle-o-up', 'A'),
(524, 'arrow-circle-o-down', 'A'),
(525, 'arrow-circle-right', 'A'),
(526, 'arrow-circle-o-left', 'A'),
(527, 'arrow-circle-up', 'A'),
(528, 'arrow-down', 'A'),
(529, 'arrow-left', 'A'),
(530, 'arrow-right', 'A'),
(531, 'arrow-up', 'A'),
(532, 'arrows-alt', 'A'),
(544, 'chevron-left', 'A'),
(545, 'chevron-right', 'A'),
(546, 'chevron-up', 'A'),
(547, 'chevron-down', 'A'),
(549, 'long-arrow-down', 'A'),
(550, 'long-arrow-left', 'A'),
(551, 'long-arrow-right', 'A'),
(552, 'long-arrow-up', 'A'),
(553, 'backward', 'A'),
(569, 'compress', 'A'),
(570, 'eject', 'A'),
(571, 'expand', 'A'),
(572, 'fast-backward', 'A'),
(573, 'fast-forward', 'A'),
(574, 'forward', 'A'),
(575, 'pause', 'A'),
(576, 'play', 'A'),
(577, 'play-circle', 'A'),
(578, 'play-circle-o', 'A'),
(579, 'step-backward', 'A'),
(580, 'step-forward', 'A'),
(581, 'stop', 'A'),
(582, 'youtube-play', 'A'),
(591, 'h-square', 'A'),
(592, 'hospital-o', 'A'),
(593, 'medkit', 'A'),
(594, 'stethoscope', 'A'),
(595, 'user-md', 'A'),
(596, '500px', 'A'),
(597, 'adn', 'A'),
(598, 'amazon', 'A'),
(599, 'android', 'A'),
(600, 'angellist', 'A'),
(601, 'apple', 'A'),
(602, 'behance', 'A'),
(603, 'behance-square', 'A'),
(604, 'bitbucket', 'A'),
(605, 'bitbucket-square', 'A'),
(606, 'black-tie', 'A'),
(607, 'buysellads', 'A'),
(608, 'chrome', 'A'),
(609, 'codepen', 'A'),
(610, 'connectdevelop', 'A'),
(611, 'contao', 'A'),
(612, 'css3', 'A'),
(613, 'dashcube', 'A'),
(614, 'delicious', 'A'),
(615, 'deviantart', 'A'),
(616, 'digg', 'A'),
(617, 'dribbble', 'A'),
(618, 'dropbox', 'A'),
(619, 'drupal', 'A'),
(620, 'empire', 'A'),
(621, 'expeditedssl', 'A'),
(622, 'facebook', 'A'),
(623, 'facebook-official', 'A'),
(624, 'facebook-square', 'A'),
(625, 'firefox', 'A'),
(626, 'flickr', 'A'),
(627, 'fonticons', 'A'),
(628, 'forumbee', 'A'),
(629, 'foursquare', 'A'),
(630, 'get-pocket', 'A'),
(631, 'git', 'A'),
(632, 'git-square', 'A'),
(633, 'github', 'A'),
(634, 'github-alt', 'A'),
(635, 'github-square', 'A'),
(636, 'google', 'A'),
(637, 'google-plus', 'A'),
(638, 'google-plus-square', 'A'),
(639, 'gratipay', 'A'),
(640, 'hacker-news', 'A'),
(641, 'houzz', 'A'),
(642, 'html5', 'A'),
(643, 'instagram', 'A'),
(644, 'internet-explorer', 'A'),
(645, 'ioxhost', 'A'),
(646, 'joomla', 'A'),
(647, 'jsfiddle', 'A'),
(648, 'lastfm', 'A'),
(649, 'lastfm-square', 'A'),
(650, 'leanpub', 'A'),
(651, 'linkedin', 'A'),
(652, 'linkedin-square', 'A'),
(653, 'linux', 'A'),
(654, 'maxcdn', 'A'),
(655, 'meanpath', 'A'),
(656, 'medium', 'A'),
(657, 'odnoklassniki', 'A'),
(658, 'odnoklassniki-square', 'A'),
(659, 'opencart', 'A'),
(660, 'openid', 'A'),
(661, 'opera', 'A'),
(662, 'optin-monster', 'A'),
(663, 'pagelines', 'A'),
(664, 'pied-piper', 'A'),
(665, 'pied-piper-alt', 'A'),
(666, 'pinterest', 'A'),
(667, 'pinterest-p', 'A'),
(668, 'pinterest-square', 'A'),
(816, 'qq', 'A'),
(817, 'rebel', 'A'),
(818, 'reddit', 'A'),
(819, 'reddit-square', 'A'),
(820, 'renren', 'A'),
(821, 'safari', 'A'),
(822, 'sellsy', 'A'),
(823, 'shirtsinbulk', 'A'),
(824, 'simplybuilt', 'A'),
(825, 'skyatlas', 'A'),
(826, 'skype', 'A'),
(827, 'slack', 'A'),
(828, 'slideshare', 'A'),
(829, 'soundcloud', 'A'),
(830, 'spotify', 'A'),
(831, 'stack-exchange', 'A'),
(832, 'stack-overflow', 'A'),
(833, 'steam', 'A'),
(834, 'steam-square', 'A'),
(835, 'stumbleupon', 'A'),
(836, 'stumbleupon-circle', 'A'),
(837, 'tencent-weibo', 'A'),
(838, 'trello', 'A'),
(839, 'tripadvisor', 'A'),
(840, 'tumblr', 'A'),
(841, 'tumblr-square', 'A'),
(842, 'twitch', 'A'),
(843, 'twitter', 'A'),
(844, 'twitter-square', 'A'),
(845, 'viacoin', 'A'),
(846, 'vimeo', 'A'),
(847, 'vimeo-square', 'A'),
(848, 'vine', 'A'),
(849, 'vk', 'A'),
(850, 'weibo', 'A'),
(851, 'weixin', 'A'),
(852, 'whatsapp', 'A'),
(853, 'wikipedia-w', 'A'),
(854, 'windows', 'A'),
(855, 'wordpress', 'A'),
(856, 'xing', 'A'),
(857, 'xing-square', 'A'),
(858, 'y-combinator', 'A'),
(859, 'yahoo', 'A'),
(860, 'yelp', 'A'),
(861, 'youtube', 'A'),
(862, 'youtube-square', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuesto`
--

CREATE TABLE `impuesto` (
  `id_impuesto` int(11) NOT NULL,
  `codigo` int(1) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `impuesto`
--

INSERT INTO `impuesto` (`id_impuesto`, `codigo`, `nombre`) VALUES
(1, 2, 'IVA'),
(2, 3, 'ICE'),
(3, 5, 'IRBPNR ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuesto_desglose_envio_factura`
--

CREATE TABLE `impuesto_desglose_envio_factura` (
  `id_impuesto_desglose_envio_factura` int(11) NOT NULL,
  `id_desglose_envio_factura` int(11) NOT NULL,
  `codigo_impuesto` int(11) NOT NULL,
  `codigo_porcentaje` varchar(6) NOT NULL,
  `base_imponible` float(10,2) NOT NULL,
  `valor` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuesto_detalle_factura`
--

CREATE TABLE `impuesto_detalle_factura` (
  `id_impuesto_detalle_factura` int(11) NOT NULL,
  `id_detalle_factura` int(11) NOT NULL,
  `codigo_impuesto` int(1) NOT NULL,
  `codigo_porcentaje` varchar(6) NOT NULL,
  `base_imponible` float(10,2) NOT NULL,
  `valor` float(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informacion_adicional_factura`
--

CREATE TABLE `informacion_adicional_factura` (
  `id_informacion_adicional_factura` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL,
  `direccion` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL,
  `telefono` bigint(11) NOT NULL,
  `dae` bigint(20) DEFAULT NULL,
  `guia_madre` bigint(20) DEFAULT NULL,
  `guia_hija` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_frio`
--

CREATE TABLE `inventario_frio` (
  `id_inventario_frio` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_ramo` int(11) NOT NULL,
  `id_empaque_e` int(11) NOT NULL,
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
  `basura` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lote`
--

CREATE TABLE `lote` (
  `id_lote` int(11) NOT NULL,
  `nombre` varchar(25) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `area` int(11) DEFAULT NULL,
  `descripcion` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `id_modulo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lote_re`
--

CREATE TABLE `lote_re` (
  `id_lote_re` int(111) NOT NULL,
  `cantidad_tallos` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `id_clasificacion_verde` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etapa` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A' COMMENT 'A => Apertura, C => Guarde Clasificacion, G => Guarde Apertura, F => StockFrio, E => Empaquetado',
  `guarde_clasificacion` date DEFAULT NULL,
  `apertura` date DEFAULT NULL,
  `guarde_apertura` date DEFAULT NULL,
  `empaquetado` date DEFAULT NULL,
  `dias_guarde_clasificacion` int(11) DEFAULT NULL,
  `dias_guarde_apertura` int(11) DEFAULT NULL,
  `stock_frio` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcacion`
--

CREATE TABLE `marcacion` (
  `id_marcacion` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `cantidad` int(11) NOT NULL,
  `id_especificacion_empaque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_bin NOT NULL,
  `descripcion` varchar(500) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
(8, 'Parámetros de facturación', '2019-01-18 08:58:39', 'A', 1, 77);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `area` int(11) DEFAULT NULL,
  `descripcion` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `id_sector` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id_modulo`, `nombre`, `estado`, `fecha_registro`, `area`, `descripcion`, `id_sector`) VALUES
(1, '1', 1, '2019-01-22 12:32:25', 2, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pais`
--

CREATE TABLE `pais` (
  `codigo` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `nombre` varchar(44) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pais`
--

INSERT INTO `pais` (`codigo`, `nombre`) VALUES
('AD', 'ANDORRA'),
('AE', 'EMIRATOS ARABES UNIDOS'),
('AF', 'AFGHANISTAN'),
('AG', 'ANTIGUA Y BARBUDA'),
('AI', 'ANGUILLA'),
('AL', 'ALBANIA'),
('AM', 'ARMENIA'),
('AO', 'ANGOLA'),
('AP', 'ASIA / REGION PACIFICO'),
('AQ', 'ANTARCTICA'),
('AR', 'ARGENTINA'),
('AS', 'AMERICAN SAMOA'),
('AT', 'AUSTRIA'),
('AU', 'AUSTRALIA'),
('AW', 'ARUBA'),
('AX', 'ALAND ISLANDS'),
('AZ', 'AZERBAIJAN'),
('BA', 'BOSNIA AND HERZEGOVINA'),
('BB', 'BARBADOS'),
('BD', 'BANGLADESH'),
('BE', 'BELGICA'),
('BF', 'BURKINA FASO'),
('BG', 'BULGARIA'),
('BH', 'BAHRAIN'),
('BI', 'BURUNDI'),
('BJ', 'BENIN'),
('BL', 'SAINT BARTHELEMY'),
('BM', 'BERMUDA'),
('BN', 'BRUNEI DARUSSALAM'),
('BO', 'BOLIVIA'),
('BQ', 'BONAIR'),
('BR', 'BRASIL'),
('BS', 'BAHAMAS'),
('BT', 'BHUTAN'),
('BW', 'BOTSWANA'),
('BY', 'BELARUS'),
('BZ', 'BELICE'),
('CA', 'CANADA'),
('CC', 'COCOS (KEELING) ISLANDS'),
('CD', 'CONG'),
('CF', 'REPUBLICA CENTRAL DE AFRICA'),
('CG', 'CONGO'),
('CH', 'SUIZA'),
('CI', 'COTE D\'IVOIRE'),
('CK', 'COOK ISLANDS'),
('CL', 'CHILE'),
('CM', 'CAMERUN'),
('CN', 'CHINA'),
('CO', 'COLOMBIA'),
('CR', 'COSTA RICA'),
('CU', 'CUBA'),
('CV', 'CABO VERDE'),
('CW', 'CURACAO'),
('CX', 'ISLAS DE PASCUA'),
('CY', 'CHIPRE'),
('CZ', 'REPUBLICA CHECA'),
('DE', 'ALEMANIA'),
('DJ', 'DJIBOUTI'),
('DK', 'DINAMARCA'),
('DM', 'DOMINICA'),
('DO', 'REPUBLICA DOMINICANA'),
('DZ', 'ALGERIA'),
('EC', 'ECUADOR'),
('EE', 'ESTONIA'),
('EG', 'EGIPTO'),
('EH', 'WESTERN SAHARA'),
('ER', 'ERITREA'),
('ES', 'ESPAÑA'),
('ET', 'ETIOPIA'),
('FI', 'FINLANDIA'),
('FJ', 'FIJI'),
('FK', 'ISLAS (MALVINAS)'),
('FM', 'MICRONESI'),
('FO', 'FAROE ISLANDS'),
('FR', 'FRANCIA'),
('GA', 'GABON'),
('GB', 'REINO UNIDO'),
('GD', 'GRANADA'),
('GE', 'GEORGIA'),
('GF', 'GUYANA FRANCESA'),
('GG', 'GUERNSEY'),
('GH', 'GANA'),
('GI', 'GIBRALTAR'),
('GL', 'GROENLANDIA'),
('GM', 'GAMBIA'),
('GN', 'GUINEA'),
('GP', 'GUADELOUPE'),
('GQ', 'GUINEA ECUATORIAL'),
('GR', 'GRECIA'),
('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS'),
('GT', 'GUATEMALA'),
('GU', 'GUAM'),
('GW', 'GUINEA-BISSAU'),
('GY', 'GUYANA'),
('HN', 'HONDURAS'),
('HR', 'CROACIA'),
('HT', 'HAITI'),
('HU', 'HUNGRIA'),
('ID', 'INDONESIA'),
('IE', 'IRLANDA'),
('IL', 'ISRAEL'),
('IM', 'ISLE OF MAN'),
('IN', 'INDIA'),
('IO', 'BRITISH INDIAN OCEAN TERRITORY'),
('IQ', 'IRAQ'),
('IR', 'IRA'),
('IS', 'ISLANDIA'),
('IT', 'ITALIA'),
('JE', 'JERSEY'),
('JM', 'JAMAICA'),
('JO', 'JORDAN'),
('JP', 'JAPAN'),
('KE', 'KENYA'),
('KG', 'KYRGYZSTAN'),
('KH', 'CAMBOYA'),
('KI', 'KIRIBATI'),
('KM', 'COMOROS'),
('KN', 'SAINT KITTS AND NEVIS'),
('KR', 'KORE'),
('KW', 'KUWAIT'),
('KY', 'ISLAS CAIMAN'),
('KZ', 'KAZAKHSTAN'),
('LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC'),
('LB', 'LEBANON'),
('LC', 'SANTA LUCIA'),
('LI', 'LIECHTENSTEIN'),
('LK', 'SRI LANKA'),
('LR', 'LIBERIA'),
('LS', 'LESOTHO'),
('LT', 'LITHUANIA'),
('LU', 'LUXEMBOURG'),
('LV', 'LATVIA'),
('LY', 'LIBYA'),
('MA', 'MARRUECOS'),
('MC', 'MONACO'),
('MD', 'MOLDOV'),
('ME', 'MONTENEGRO'),
('MF', 'SAN MARTIN'),
('MG', 'MADAGASCAR'),
('MH', 'MARSHALL ISLANDS'),
('MK', 'MACEDONIA'),
('ML', 'MALI'),
('MM', 'MYANMAR'),
('MN', 'MONGOLIA'),
('MO', 'MACAU'),
('MP', 'NORTHERN MARIANA ISLANDS'),
('MQ', 'MARTINIQUE'),
('MR', 'MAURITANIA'),
('MS', 'MONTSERRAT'),
('MT', 'MALTA'),
('MU', 'MAURITIUS'),
('MV', 'MALDIVES'),
('MW', 'MALAWI'),
('MX', 'MEXICO'),
('MY', 'MALASIA'),
('MZ', 'MOZAMBIQUE'),
('NA', 'NAMIBIA'),
('NC', 'NUEVA CALEDONIA'),
('NE', 'NIGER'),
('NF', 'NORFOLK ISLAND'),
('NG', 'NIGERIA'),
('NI', 'NICARAGUA'),
('NL', 'HOLANDA, PAISES BAJOS'),
('NO', 'NORUEGA'),
('NP', 'NEPAL'),
('NR', 'NAURU'),
('NU', 'NIUE'),
('NZ', 'NUEVA ZELANDIA'),
('OM', 'OMAN'),
('PA', 'PANAMA'),
('PE', 'PERU'),
('PF', 'FRENCH POLYNESIA'),
('PG', 'PAPUA NEW GUINEA'),
('PH', 'FILIPINAS'),
('PK', 'PAKISTAN'),
('PL', 'POLONIA'),
('PM', 'SAINT PIERRE AND MIQUELON'),
('PN', 'PITCAIRN ISLANDS'),
('PR', 'PUERTO RICO'),
('PS', 'PALESTINA'),
('PT', 'PORTUGAL'),
('PW', 'PALAU'),
('PY', 'PARAGUAY'),
('QA', 'QATAR'),
('RE', 'REUNION'),
('RO', 'ROMANIA'),
('RS', 'SERBIA'),
('RU', 'RUSIA'),
('RW', 'RUANDA'),
('SA', 'ARABIA SAUDITA'),
('SB', 'ISLAS SALOMON'),
('SC', 'SEYCHELLES'),
('SD', 'SUDAN'),
('SE', 'SUECIA'),
('SG', 'SINGAPUR'),
('SH', 'SAINT HELENA'),
('SI', 'ESLOVENIA'),
('SJ', 'SVALBARD AND JAN MAYEN'),
('SK', 'ESLOVAQUIA'),
('SL', 'SIERRA LEONE'),
('SM', 'SAN MARINO'),
('SN', 'SENEGAL'),
('SO', 'SOMALIA'),
('SR', 'SURINAM'),
('SS', 'SUDAN DEL SUR'),
('ST', 'SAO TOME AND PRINCIPE'),
('SV', 'EL SALVADOR'),
('SX', 'SINT MAARTEN (DUTCH PART)'),
('SY', 'SIRIA REPUBLICA ARABE'),
('SZ', 'SWAZILAND'),
('TC', 'TURKS AND CAICOS ISLANDS'),
('TD', 'CHAD'),
('TG', 'TOGO'),
('TH', 'TAILANDIA'),
('TJ', 'TAJIKISTAN'),
('TK', 'TOKELAU'),
('TL', 'TIMOR-LESTE'),
('TM', 'TURKMENISTAN'),
('TN', 'TUNISIA'),
('TO', 'TONGA'),
('TR', 'TURQUIA'),
('TT', 'TRINIDAD Y TOBAGO'),
('TV', 'TUVALU'),
('TW', 'TAIWAN'),
('TZ', 'TANZANIA'),
('UA', 'UCRANIA'),
('UG', 'UGANDA'),
('UM', 'UNITED STATES MINOR OUTLYING ISLANDS'),
('US', 'ESTADOS UNIDOS'),
('UY', 'URUGUAY'),
('UZ', 'UZBEKISTAN'),
('VA', 'HOLY SEE (VATICAN CITY STATE)'),
('VC', 'SAINT VINCENT AND THE GRENADINES'),
('VE', 'VENEZUELA'),
('VG', 'VIRGIN ISLAND'),
('VN', 'VIETNAM'),
('VU', 'VANUATU'),
('WF', 'WALLIS AND FUTUNA'),
('WS', 'SAMOA'),
('YE', 'YEMEN'),
('YT', 'MAYOTTE'),
('ZA', 'SUDAFRICA'),
('ZM', 'ZAMBIA'),
('ZW', 'ZIMBABWE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `descripcion` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `fecha_pedido` date NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empaquetado` tinyint(1) NOT NULL DEFAULT '0',
  `variedad` varchar(500) COLLATE utf8_bin NOT NULL,
  `tipo_especificacion` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'N' COMMENT 'N => Normal; O => Orden Semanal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planta`
--

CREATE TABLE `planta` (
  `id_planta` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `planta`
--

INSERT INTO `planta` (`id_planta`, `nombre`, `fecha_registro`, `estado`) VALUES
(1, 'GYPSOPHILA', '2018-10-25 09:01:26', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precio`
--

CREATE TABLE `precio` (
  `id_precio` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_ramo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion`
--

CREATE TABLE `recepcion` (
  `id_recepcion` int(11) NOT NULL,
  `id_semana` int(11) NOT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_cosecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion_clasificacion_verde`
--

CREATE TABLE `recepcion_clasificacion_verde` (
  `id_recepcion_clasificacion_verde` int(11) NOT NULL,
  `id_recepcion` int(11) NOT NULL,
  `id_clasificacion_verde` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(25) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A',
  `tipo` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'S' COMMENT 'P => Principal; S => Secundario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `fecha_registro`, `estado`, `tipo`) VALUES
(1, 'ADMINISTRADOR', '2018-10-02 09:09:40', 'A', 'P'),
(2, 'REPORTES', '2018-10-04 14:11:55', 'A', 'S');

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
(25, 1, 25, '2019-02-08 15:37:36', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id_sector` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `area` int(11) DEFAULT NULL,
  `descripcion` varchar(1000) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id_sector`, `nombre`, `fecha_registro`, `estado`, `area`, `descripcion`) VALUES
(1, 'LAS PALMAS', '2019-01-22 12:32:09', 1, 5, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semana`
--

CREATE TABLE `semana` (
  `id_semana` int(11) NOT NULL,
  `codigo` varchar(4) COLLATE utf8_bin NOT NULL,
  `fecha_inicial` date NOT NULL,
  `fecha_final` date NOT NULL,
  `curva` varchar(11) COLLATE utf8_bin DEFAULT NULL,
  `desecho` int(2) DEFAULT NULL,
  `semana_poda` int(2) DEFAULT NULL,
  `semana_siembra` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `anno` int(4) NOT NULL,
  `id_variedad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `semana`
--

INSERT INTO `semana` (`id_semana`, `codigo`, `fecha_inicial`, `fecha_final`, `curva`, `desecho`, `semana_poda`, `semana_siembra`, `fecha_registro`, `estado`, `anno`, `id_variedad`) VALUES
(1, '1901', '2018-12-31', '2019-01-06', NULL, NULL, NULL, NULL, '2018-11-05 12:50:12', 1, 2019, 1),
(2, '1902', '2019-01-07', '2019-01-13', NULL, NULL, NULL, NULL, '2018-11-05 12:50:12', 1, 2019, 1),
(3, '1903', '2019-01-14', '2019-01-20', NULL, NULL, NULL, NULL, '2018-11-05 12:50:13', 1, 2019, 1),
(4, '1904', '2019-01-21', '2019-01-27', NULL, NULL, NULL, NULL, '2018-11-05 12:50:13', 1, 2019, 1),
(5, '1905', '2019-01-28', '2019-02-03', NULL, NULL, NULL, NULL, '2018-11-05 12:50:13', 1, 2019, 1),
(6, '1906', '2019-02-04', '2019-02-10', NULL, NULL, NULL, NULL, '2018-11-05 12:50:13', 1, 2019, 1),
(7, '1907', '2019-02-11', '2019-02-17', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(8, '1908', '2019-02-18', '2019-02-24', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(9, '1909', '2019-02-25', '2019-03-03', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(10, '1910', '2019-03-04', '2019-03-10', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(11, '1911', '2019-03-11', '2019-03-17', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(12, '1912', '2019-03-18', '2019-03-24', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(13, '1913', '2019-03-25', '2019-03-31', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(14, '1914', '2019-04-01', '2019-04-07', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(15, '1915', '2019-04-08', '2019-04-14', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(16, '1916', '2019-04-15', '2019-04-21', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(17, '1917', '2019-04-22', '2019-04-28', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(18, '1918', '2019-04-29', '2019-05-05', NULL, NULL, NULL, NULL, '2018-11-05 12:50:14', 1, 2019, 1),
(19, '1919', '2019-05-06', '2019-05-12', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(20, '1920', '2019-05-13', '2019-05-19', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(21, '1921', '2019-05-20', '2019-05-26', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(22, '1922', '2019-05-27', '2019-06-02', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(23, '1923', '2019-06-03', '2019-06-09', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(24, '1924', '2019-06-10', '2019-06-16', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(25, '1925', '2019-06-17', '2019-06-23', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(26, '1926', '2019-06-24', '2019-06-30', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(27, '1927', '2019-07-01', '2019-07-07', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(28, '1928', '2019-07-08', '2019-07-14', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(29, '1929', '2019-07-15', '2019-07-21', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(30, '1930', '2019-07-22', '2019-07-28', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(31, '1931', '2019-07-29', '2019-08-04', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(32, '1932', '2019-08-05', '2019-08-11', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(33, '1933', '2019-08-12', '2019-08-18', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(34, '1934', '2019-08-19', '2019-08-25', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(35, '1935', '2019-08-26', '2019-09-01', NULL, NULL, NULL, NULL, '2018-11-05 12:50:15', 1, 2019, 1),
(36, '1936', '2019-09-02', '2019-09-08', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(37, '1937', '2019-09-09', '2019-09-15', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(38, '1938', '2019-09-16', '2019-09-22', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(39, '1939', '2019-09-23', '2019-09-29', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(40, '1940', '2019-09-30', '2019-10-06', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(41, '1941', '2019-10-07', '2019-10-13', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(42, '1942', '2019-10-14', '2019-10-20', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(43, '1943', '2019-10-21', '2019-10-27', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(44, '1944', '2019-10-28', '2019-11-03', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(45, '1945', '2019-11-04', '2019-11-10', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(46, '1946', '2019-11-11', '2019-11-17', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(47, '1947', '2019-11-18', '2019-11-24', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(48, '1948', '2019-11-25', '2019-12-01', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(49, '1949', '2019-12-02', '2019-12-08', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(50, '1950', '2019-12-09', '2019-12-15', NULL, NULL, NULL, NULL, '2018-11-05 12:50:16', 1, 2019, 1),
(51, '1951', '2019-12-16', '2019-12-22', NULL, NULL, NULL, NULL, '2018-11-05 12:50:17', 1, 2019, 1),
(52, '1952', '2019-12-23', '2019-12-29', NULL, NULL, NULL, NULL, '2018-11-05 12:50:17', 1, 2019, 1),
(53, '1901', '2018-12-31', '2019-01-06', NULL, NULL, NULL, NULL, '2018-11-05 12:51:47', 1, 2019, 2),
(54, '1902', '2019-01-07', '2019-01-13', NULL, NULL, NULL, NULL, '2018-11-05 12:51:47', 1, 2019, 2),
(55, '1903', '2019-01-14', '2019-01-20', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(56, '1904', '2019-01-21', '2019-01-27', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(57, '1905', '2019-01-28', '2019-02-03', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(58, '1906', '2019-02-04', '2019-02-10', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(59, '1907', '2019-02-11', '2019-02-17', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(60, '1908', '2019-02-18', '2019-02-24', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(61, '1909', '2019-02-25', '2019-03-03', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(62, '1910', '2019-03-04', '2019-03-10', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(63, '1911', '2019-03-11', '2019-03-17', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(64, '1912', '2019-03-18', '2019-03-24', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(65, '1913', '2019-03-25', '2019-03-31', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(66, '1914', '2019-04-01', '2019-04-07', NULL, NULL, NULL, NULL, '2018-11-05 12:51:48', 1, 2019, 2),
(67, '1915', '2019-04-08', '2019-04-14', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(68, '1916', '2019-04-15', '2019-04-21', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(69, '1917', '2019-04-22', '2019-04-28', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(70, '1918', '2019-04-29', '2019-05-05', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(71, '1919', '2019-05-06', '2019-05-12', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(72, '1920', '2019-05-13', '2019-05-19', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(73, '1921', '2019-05-20', '2019-05-26', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(74, '1922', '2019-05-27', '2019-06-02', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(75, '1923', '2019-06-03', '2019-06-09', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(76, '1924', '2019-06-10', '2019-06-16', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(77, '1925', '2019-06-17', '2019-06-23', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(78, '1926', '2019-06-24', '2019-06-30', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(79, '1927', '2019-07-01', '2019-07-07', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(80, '1928', '2019-07-08', '2019-07-14', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(81, '1929', '2019-07-15', '2019-07-21', NULL, NULL, NULL, NULL, '2018-11-05 12:51:49', 1, 2019, 2),
(82, '1930', '2019-07-22', '2019-07-28', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(83, '1931', '2019-07-29', '2019-08-04', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(84, '1932', '2019-08-05', '2019-08-11', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(85, '1933', '2019-08-12', '2019-08-18', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(86, '1934', '2019-08-19', '2019-08-25', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(87, '1935', '2019-08-26', '2019-09-01', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(88, '1936', '2019-09-02', '2019-09-08', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(89, '1937', '2019-09-09', '2019-09-15', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(90, '1938', '2019-09-16', '2019-09-22', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(91, '1939', '2019-09-23', '2019-09-29', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(92, '1940', '2019-09-30', '2019-10-06', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(93, '1941', '2019-10-07', '2019-10-13', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(94, '1942', '2019-10-14', '2019-10-20', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(95, '1943', '2019-10-21', '2019-10-27', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(96, '1944', '2019-10-28', '2019-11-03', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(97, '1945', '2019-11-04', '2019-11-10', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(98, '1946', '2019-11-11', '2019-11-17', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(99, '1947', '2019-11-18', '2019-11-24', NULL, NULL, NULL, NULL, '2018-11-05 12:51:50', 1, 2019, 2),
(100, '1948', '2019-11-25', '2019-12-01', NULL, NULL, NULL, NULL, '2018-11-05 12:51:51', 1, 2019, 2),
(101, '1949', '2019-12-02', '2019-12-08', NULL, NULL, NULL, NULL, '2018-11-05 12:51:51', 1, 2019, 2),
(102, '1950', '2019-12-09', '2019-12-15', NULL, NULL, NULL, NULL, '2018-11-05 12:51:51', 1, 2019, 2),
(103, '1951', '2019-12-16', '2019-12-22', NULL, NULL, NULL, NULL, '2018-11-05 12:51:51', 1, 2019, 2),
(104, '1952', '2019-12-23', '2019-12-29', NULL, NULL, NULL, NULL, '2018-11-05 12:51:51', 1, 2019, 2),
(105, '1801', '2018-01-01', '2018-01-07', NULL, NULL, NULL, NULL, '2018-11-30 10:48:57', 1, 2018, 1),
(106, '1802', '2018-01-08', '2018-01-14', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(107, '1803', '2018-01-15', '2018-01-21', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(108, '1804', '2018-01-22', '2018-01-28', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(109, '1805', '2018-01-29', '2018-02-04', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(110, '1806', '2018-02-05', '2018-02-11', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(111, '1807', '2018-02-12', '2018-02-18', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(112, '1808', '2018-02-19', '2018-02-25', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(113, '1809', '2018-02-26', '2018-03-04', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(114, '1810', '2018-03-05', '2018-03-11', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(115, '1811', '2018-03-12', '2018-03-18', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(116, '1812', '2018-03-19', '2018-03-25', NULL, NULL, NULL, NULL, '2018-11-30 10:48:58', 1, 2018, 1),
(117, '1813', '2018-03-26', '2018-04-01', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(118, '1814', '2018-04-02', '2018-04-08', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(119, '1815', '2018-04-09', '2018-04-15', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(120, '1816', '2018-04-16', '2018-04-22', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(121, '1817', '2018-04-23', '2018-04-29', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(122, '1818', '2018-04-30', '2018-05-06', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(123, '1819', '2018-05-07', '2018-05-13', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(124, '1820', '2018-05-14', '2018-05-20', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(125, '1821', '2018-05-21', '2018-05-27', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(126, '1822', '2018-05-28', '2018-06-03', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(127, '1823', '2018-06-04', '2018-06-10', NULL, NULL, NULL, NULL, '2018-11-30 10:48:59', 1, 2018, 1),
(128, '1824', '2018-06-11', '2018-06-17', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(129, '1825', '2018-06-18', '2018-06-24', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(130, '1826', '2018-06-25', '2018-07-01', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(131, '1827', '2018-07-02', '2018-07-08', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(132, '1828', '2018-07-09', '2018-07-15', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(133, '1829', '2018-07-16', '2018-07-22', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(134, '1830', '2018-07-23', '2018-07-29', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(135, '1831', '2018-07-30', '2018-08-05', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(136, '1832', '2018-08-06', '2018-08-12', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(137, '1833', '2018-08-13', '2018-08-19', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(138, '1834', '2018-08-20', '2018-08-26', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(139, '1835', '2018-08-27', '2018-09-02', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(140, '1836', '2018-09-03', '2018-09-09', NULL, NULL, NULL, NULL, '2018-11-30 10:49:00', 1, 2018, 1),
(141, '1837', '2018-09-10', '2018-09-16', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(142, '1838', '2018-09-17', '2018-09-23', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(143, '1839', '2018-09-24', '2018-09-30', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(144, '1840', '2018-10-01', '2018-10-07', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(145, '1841', '2018-10-08', '2018-10-14', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(146, '1842', '2018-10-15', '2018-10-21', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(147, '1843', '2018-10-22', '2018-10-28', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(148, '1844', '2018-10-29', '2018-11-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(149, '1845', '2018-11-05', '2018-11-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(150, '1846', '2018-11-12', '2018-11-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(151, '1847', '2018-11-19', '2018-11-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(152, '1848', '2018-11-26', '2018-12-02', NULL, NULL, NULL, NULL, '2018-11-30 10:49:01', 1, 2018, 1),
(153, '1849', '2018-12-03', '2018-12-09', NULL, NULL, NULL, NULL, '2018-11-30 10:49:02', 1, 2018, 1),
(154, '1850', '2018-12-10', '2018-12-16', NULL, NULL, NULL, NULL, '2018-11-30 10:49:02', 1, 2018, 1),
(155, '1851', '2018-12-17', '2018-12-23', NULL, NULL, NULL, NULL, '2018-11-30 10:49:02', 1, 2018, 1),
(156, '1852', '2018-12-24', '2018-12-30', NULL, NULL, NULL, NULL, '2018-11-30 10:49:02', 1, 2018, 1),
(157, '1801', '2018-01-01', '2018-01-07', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(158, '1802', '2018-01-08', '2018-01-14', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(159, '1803', '2018-01-15', '2018-01-21', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(160, '1804', '2018-01-22', '2018-01-28', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(161, '1805', '2018-01-29', '2018-02-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(162, '1806', '2018-02-05', '2018-02-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(163, '1807', '2018-02-12', '2018-02-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(164, '1808', '2018-02-19', '2018-02-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(165, '1809', '2018-02-26', '2018-03-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(166, '1810', '2018-03-05', '2018-03-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:40', 1, 2018, 2),
(167, '1811', '2018-03-12', '2018-03-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(168, '1812', '2018-03-19', '2018-03-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(169, '1813', '2018-03-26', '2018-04-01', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(170, '1814', '2018-04-02', '2018-04-08', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(171, '1815', '2018-04-09', '2018-04-15', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(172, '1816', '2018-04-16', '2018-04-22', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(173, '1817', '2018-04-23', '2018-04-29', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(174, '1818', '2018-04-30', '2018-05-06', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(175, '1819', '2018-05-07', '2018-05-13', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(176, '1820', '2018-05-14', '2018-05-20', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(177, '1821', '2018-05-21', '2018-05-27', NULL, NULL, NULL, NULL, '2018-11-30 10:49:41', 1, 2018, 2),
(178, '1822', '2018-05-28', '2018-06-03', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(179, '1823', '2018-06-04', '2018-06-10', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(180, '1824', '2018-06-11', '2018-06-17', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(181, '1825', '2018-06-18', '2018-06-24', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(182, '1826', '2018-06-25', '2018-07-01', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(183, '1827', '2018-07-02', '2018-07-08', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(184, '1828', '2018-07-09', '2018-07-15', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(185, '1829', '2018-07-16', '2018-07-22', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(186, '1830', '2018-07-23', '2018-07-29', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(187, '1831', '2018-07-30', '2018-08-05', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(188, '1832', '2018-08-06', '2018-08-12', NULL, NULL, NULL, NULL, '2018-11-30 10:49:42', 1, 2018, 2),
(189, '1833', '2018-08-13', '2018-08-19', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(190, '1834', '2018-08-20', '2018-08-26', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(191, '1835', '2018-08-27', '2018-09-02', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(192, '1836', '2018-09-03', '2018-09-09', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(193, '1837', '2018-09-10', '2018-09-16', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(194, '1838', '2018-09-17', '2018-09-23', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(195, '1839', '2018-09-24', '2018-09-30', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(196, '1840', '2018-10-01', '2018-10-07', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(197, '1841', '2018-10-08', '2018-10-14', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(198, '1842', '2018-10-15', '2018-10-21', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(199, '1843', '2018-10-22', '2018-10-28', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(200, '1844', '2018-10-29', '2018-11-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:43', 1, 2018, 2),
(201, '1845', '2018-11-05', '2018-11-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(202, '1846', '2018-11-12', '2018-11-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(203, '1847', '2018-11-19', '2018-11-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(204, '1848', '2018-11-26', '2018-12-02', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(205, '1849', '2018-12-03', '2018-12-09', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(206, '1850', '2018-12-10', '2018-12-16', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(207, '1851', '2018-12-17', '2018-12-23', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(208, '1852', '2018-12-24', '2018-12-30', NULL, NULL, NULL, NULL, '2018-11-30 10:49:44', 1, 2018, 2),
(209, '1801', '2018-01-01', '2018-01-07', NULL, NULL, NULL, NULL, '2018-11-30 10:49:55', 1, 2018, 3),
(210, '1802', '2018-01-08', '2018-01-14', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(211, '1803', '2018-01-15', '2018-01-21', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(212, '1804', '2018-01-22', '2018-01-28', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(213, '1805', '2018-01-29', '2018-02-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(214, '1806', '2018-02-05', '2018-02-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(215, '1807', '2018-02-12', '2018-02-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(216, '1808', '2018-02-19', '2018-02-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(217, '1809', '2018-02-26', '2018-03-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(218, '1810', '2018-03-05', '2018-03-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(219, '1811', '2018-03-12', '2018-03-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(220, '1812', '2018-03-19', '2018-03-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:56', 1, 2018, 3),
(221, '1813', '2018-03-26', '2018-04-01', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(222, '1814', '2018-04-02', '2018-04-08', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(223, '1815', '2018-04-09', '2018-04-15', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(224, '1816', '2018-04-16', '2018-04-22', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(225, '1817', '2018-04-23', '2018-04-29', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(226, '1818', '2018-04-30', '2018-05-06', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(227, '1819', '2018-05-07', '2018-05-13', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(228, '1820', '2018-05-14', '2018-05-20', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(229, '1821', '2018-05-21', '2018-05-27', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(230, '1822', '2018-05-28', '2018-06-03', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(231, '1823', '2018-06-04', '2018-06-10', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(232, '1824', '2018-06-11', '2018-06-17', NULL, NULL, NULL, NULL, '2018-11-30 10:49:57', 1, 2018, 3),
(233, '1825', '2018-06-18', '2018-06-24', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(234, '1826', '2018-06-25', '2018-07-01', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(235, '1827', '2018-07-02', '2018-07-08', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(236, '1828', '2018-07-09', '2018-07-15', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(237, '1829', '2018-07-16', '2018-07-22', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(238, '1830', '2018-07-23', '2018-07-29', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(239, '1831', '2018-07-30', '2018-08-05', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(240, '1832', '2018-08-06', '2018-08-12', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(241, '1833', '2018-08-13', '2018-08-19', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(242, '1834', '2018-08-20', '2018-08-26', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(243, '1835', '2018-08-27', '2018-09-02', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(244, '1836', '2018-09-03', '2018-09-09', NULL, NULL, NULL, NULL, '2018-11-30 10:49:58', 1, 2018, 3),
(245, '1837', '2018-09-10', '2018-09-16', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(246, '1838', '2018-09-17', '2018-09-23', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(247, '1839', '2018-09-24', '2018-09-30', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(248, '1840', '2018-10-01', '2018-10-07', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(249, '1841', '2018-10-08', '2018-10-14', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(250, '1842', '2018-10-15', '2018-10-21', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(251, '1843', '2018-10-22', '2018-10-28', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(252, '1844', '2018-10-29', '2018-11-04', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(253, '1845', '2018-11-05', '2018-11-11', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(254, '1846', '2018-11-12', '2018-11-18', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(255, '1847', '2018-11-19', '2018-11-25', NULL, NULL, NULL, NULL, '2018-11-30 10:49:59', 1, 2018, 3),
(256, '1848', '2018-11-26', '2018-12-02', NULL, NULL, NULL, NULL, '2018-11-30 10:50:00', 1, 2018, 3),
(257, '1849', '2018-12-03', '2018-12-09', NULL, NULL, NULL, NULL, '2018-11-30 10:50:00', 1, 2018, 3),
(258, '1850', '2018-12-10', '2018-12-16', NULL, NULL, NULL, NULL, '2018-11-30 10:50:00', 1, 2018, 3),
(259, '1851', '2018-12-17', '2018-12-23', NULL, NULL, NULL, NULL, '2018-11-30 10:50:00', 1, 2018, 3),
(260, '1852', '2018-12-24', '2018-12-30', NULL, NULL, NULL, NULL, '2018-11-30 10:50:00', 1, 2018, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_apertura`
--

CREATE TABLE `stock_apertura` (
  `id_stock_apertura` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `cantidad_tallos` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cantidad_disponible` int(11) NOT NULL,
  `disponibilidad` tinyint(1) NOT NULL DEFAULT '1',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dias` int(11) NOT NULL,
  `id_lote_re` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_empaquetado`
--

CREATE TABLE `stock_empaquetado` (
  `id_stock_empaquetado` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `cantidad_ingresada` float NOT NULL DEFAULT '0',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empaquetado` tinyint(1) NOT NULL DEFAULT '0',
  `cantidad_armada` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_frio`
--

CREATE TABLE `stock_frio` (
  `id_stock_frio` int(11) NOT NULL,
  `id_consumo` int(11) NOT NULL,
  `id_stock_apertura` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `id_semana` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `dias_maduracion` int(11) NOT NULL,
  `cantidad_ramos_estandar` float NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cantidad_disponible` float NOT NULL,
  `disponibilidad` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_guarde`
--

CREATE TABLE `stock_guarde` (
  `id_stock_guarde` int(11) NOT NULL,
  `id_variedad` int(11) NOT NULL,
  `id_clasificacion_unitaria` int(11) NOT NULL,
  `cantidad_tallos` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cantidad_disponible` int(11) NOT NULL,
  `disponibilidad` tinyint(1) NOT NULL DEFAULT '1',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dias` int(11) NOT NULL,
  `id_lote_re` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
(11, 'Cliente', '2018-11-06 08:30:27', 'A', 7, 'clientes'),
(12, 'Agencias de Carga', '2018-11-08 14:50:55', 'A', 7, 'agrencias_carga'),
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
(25, 'Comprobantes', '2019-02-08 15:37:18', 'A', 7, 'comprobante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_comprobante`
--

CREATE TABLE `tipo_comprobante` (
  `id_tipo_comprobante` int(11) NOT NULL,
  `codigo` char(2) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_comprobante`
--

INSERT INTO `tipo_comprobante` (`id_tipo_comprobante`, `codigo`, `nombre`, `fecha_registro`, `estado`) VALUES
(1, '01', 'FACTURA', '2019-01-16 08:56:31', 1),
(2, '02', 'NOTA DE CRÉDITO', '2019-01-16 08:56:43', 1),
(3, '05', 'NOTA DE DÉBITO', '2019-01-16 08:56:53', 1),
(4, '06', 'GUÍA DE REMISIÓN', '2019-01-16 08:57:07', 1),
(5, '07', 'COMPROBANTE DE RETENCIÓN', '2019-01-16 08:57:18', 1),
(6, '00', 'LOTE', '2019-02-08 15:38:38', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_identificacion`
--

CREATE TABLE `tipo_identificacion` (
  `id_tipo_identificacion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_identificacion`
--

INSERT INTO `tipo_identificacion` (`id_tipo_identificacion`, `nombre`, `codigo`, `estado`, `fecha_registro`) VALUES
(1, 'RUC', '04', 1, '2018-12-26 13:08:16'),
(2, 'CÉDULA', '05', 1, '2018-12-26 13:13:50'),
(3, 'PASAPORTE', '06', 1, '2018-12-26 13:13:58'),
(4, 'VENTA A CONSUMIDOR FINAL', '07', 1, '2019-02-08 15:40:32'),
(5, 'IDENTIFICACIÓN DEL EXTERIOR', '08', 1, '2019-02-08 15:40:32'),
(6, 'PLACA', '09', 1, '2019-02-08 15:40:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_impuesto`
--

CREATE TABLE `tipo_impuesto` (
  `id_tipo_impuesto` int(11) NOT NULL,
  `codigo_impuesto` int(2) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `porcentaje` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_impuesto`
--

INSERT INTO `tipo_impuesto` (`id_tipo_impuesto`, `codigo_impuesto`, `codigo`, `descripcion`, `porcentaje`, `estado`) VALUES
(8, 2, '0', 'IVA 0%', '0', 1),
(9, 2, '2', 'IVA 12%', '12', 1),
(10, 2, '3', 'IVA 14%', '14', 1),
(11, 2, '6', 'NO OBJETO DE IMPUESTO', 'NO OBJETO DE IMPUESTO', 1),
(12, 2, '7', 'EXENTO DE IVA', 'EXENTO DE IVA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id_unidad_medida` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `siglas` varchar(25) COLLATE utf8_bin NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `tipo` char(1) COLLATE utf8_bin NOT NULL COMMENT 'P => Peso; L => Longitud'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id_unidad_medida`, `nombre`, `siglas`, `fecha_registro`, `estado`, `tipo`) VALUES
(1, 'Centímetros', 'cm', '2018-12-06 12:32:45', 1, 'L'),
(2, 'Gramos', 'gr', '2018-12-06 12:32:45', 1, 'P');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(250) COLLATE utf8_bin NOT NULL,
  `correo` varchar(250) COLLATE utf8_bin NOT NULL,
  `username` varchar(250) COLLATE utf8_bin NOT NULL,
  `password` varchar(250) COLLATE utf8_bin NOT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'A',
  `imagen_perfil` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT 'logo_usuario.png',
  `punto_acceso` char(3) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre_completo`, `correo`, `username`, `password`, `id_rol`, `fecha_registro`, `estado`, `imagen_perfil`, `punto_acceso`) VALUES
(1, 'RAFAEL PRATS RECASEN', 'prats@pyganflor.com', 'rprats', '$2y$10$EQdvjE1T2lQDOICvTtSEB.KtLIW7GheK3HoqzBqiEpMdvYnbGVw8y', 1, '2018-10-02 10:50:24', 'A', 'imagen_perfil_2018_10_10_15_22_32-rprats.jpg', NULL),
(2, 'EDUARDO DAVALOS', 'eduardo.p@pyganflor.com', 'eduardo.p', '$2y$10$tYY0mK2AupU1bRt.QUws8uRK9QRdCv6Th.juwX8dgThMDJt8J9QV2', 2, '2018-10-04 15:35:13', 'I', 'logo_usuario.png', NULL),
(3, 'OBRIAN VILLASMIL', 'orbian@pyganflor.com', 'obrian', '$2y$10$gu0puJhnBYh9xIDBa3zW2eacJ8yqy.c/tc5ItuankGB5rtmYuukv.', 1, '2018-11-05 11:57:32', 'A', 'logo_usuario.png', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variedad`
--

CREATE TABLE `variedad` (
  `id_variedad` int(11) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_bin NOT NULL,
  `siglas` varchar(25) COLLATE utf8_bin NOT NULL,
  `minimo_apertura` int(11) NOT NULL,
  `maximo_apertura` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_planta` int(11) NOT NULL,
  `estandar_apertura` int(11) NOT NULL,
  `tallos_x_malla` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `variedad`
--

INSERT INTO `variedad` (`id_variedad`, `nombre`, `siglas`, `minimo_apertura`, `maximo_apertura`, `fecha_registro`, `estado`, `id_planta`, `estandar_apertura`, `tallos_x_malla`) VALUES
(1, 'GALAXY', 'GLX', 5, 9, '2018-10-25 09:01:47', 1, 1, 7, 50),
(2, 'XLENCE', 'XL', 5, 9, '2018-10-25 09:02:30', 1, 1, 7, 30),
(3, 'MILLION STAR', 'MS', 3, 10, '2018-10-25 09:03:46', 1, 1, 7, 50);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agencia_carga`
--
ALTER TABLE `agencia_carga`
  ADD PRIMARY KEY (`id_agencia_carga`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `aerolineas`
--
ALTER TABLE `agencia_transporte`
  ADD PRIMARY KEY (`id_agencia_transporte`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`);

--
-- Indices de la tabla `clasificacion_ramo`
--
ALTER TABLE `clasificacion_ramo`
  ADD PRIMARY KEY (`id_clasificacion_ramo`),
  ADD KEY `FK_ClasificacionRamo_ConfiguracionEmpresa` (`id_configuracion_empresa`),
  ADD KEY `FK_ClasificacionRamo_UnidadMedida` (`id_unidad_medida`);

--
-- Indices de la tabla `clasificacion_unitaria`
--
ALTER TABLE `clasificacion_unitaria`
  ADD PRIMARY KEY (`id_clasificacion_unitaria`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_ClasificacionUnitaria_ConfiguracionEmpresa` (`id_configuracion_empresa`),
  ADD KEY `FK_ClasificacionUnitaria_UnidadMedida` (`id_unidad_medida`),
  ADD KEY `FK_ClasificacionUnitaria_ClasificacionRamoEstandar` (`id_clasificacion_ramo_estandar`),
  ADD KEY `FK_ClasificacionUnitaria_ClasificacionRamoReal` (`id_clasificacion_ramo_real`);

--
-- Indices de la tabla `clasificacion_verde`
--
ALTER TABLE `clasificacion_verde`
  ADD PRIMARY KEY (`id_clasificacion_verde`),
  ADD UNIQUE KEY `fecha_ingreso` (`fecha_ingreso`),
  ADD KEY `FK_ClasificacionVerde_Semana` (`id_semana`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `cliente_agenciacarga`
--
ALTER TABLE `cliente_agenciacarga`
  ADD PRIMARY KEY (`id_cliente_agencia_carga`),
  ADD KEY `FK_ClienteAgenciaCarga_Cliente` (`id_cliente`),
  ADD KEY `FK_ClienteAgenciaCarga_AgenciaCarga` (`id_agencia_carga`);

--
-- Indices de la tabla `cliente_pedido_especificacion`
--
ALTER TABLE `cliente_pedido_especificacion`
  ADD PRIMARY KEY (`id_cliente_pedido_especificacion`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_especificacion` (`id_especificacion`);

--
-- Indices de la tabla `codigo_dae`
--
ALTER TABLE `codigo_dae`
  ADD PRIMARY KEY (`id_codigo_dae`),
  ADD KEY `codigo_pais` (`codigo_pais`);

--
-- Indices de la tabla `coloracion`
--
ALTER TABLE `coloracion`
  ADD PRIMARY KEY (`id_coloracion`),
  ADD KEY `FK_Coloracion_Marcacion` (`id_marcacion`);

--
-- Indices de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `id_envio` (`id_envio`),
  ADD KEY `tipo_comprobante` (`tipo_comprobante`);

--
-- Indices de la tabla `configuracion_empresa`
--
ALTER TABLE `configuracion_empresa`
  ADD PRIMARY KEY (`id_configuracion_empresa`),
  ADD UNIQUE KEY `estado` (`estado`);

--
-- Indices de la tabla `configuracion_user`
--
ALTER TABLE `configuracion_user`
  ADD PRIMARY KEY (`id_configuracion_user`),
  ADD KEY `FK_ConfiguracionUser_Usuario` (`id_usuario`);

--
-- Indices de la tabla `consumo`
--
ALTER TABLE `consumo`
  ADD PRIMARY KEY (`id_consumo`);

--
-- Indices de la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`id_contacto`);

--
-- Indices de la tabla `cosecha`
--
ALTER TABLE `cosecha`
  ADD PRIMARY KEY (`id_cosecha`);

--
-- Indices de la tabla `desglose_envio_factura`
--
ALTER TABLE `desglose_envio_factura`
  ADD PRIMARY KEY (`id_desglose_envio_factura`),
  ADD KEY `id_comprobante` (`id_comprobante`);

--
-- Indices de la tabla `desglose_recepcion`
--
ALTER TABLE `desglose_recepcion`
  ADD PRIMARY KEY (`id_desglose_recepcion`),
  ADD KEY `FK_DesgloseRecepcion_Variedad` (`id_variedad`),
  ADD KEY `FK_DesgloseRecepcion_Recepcion` (`id_recepcion`);

--
-- Indices de la tabla `detalle_clasificacion_verde`
--
ALTER TABLE `detalle_clasificacion_verde`
  ADD PRIMARY KEY (`id_detalle_clasificacion_verde`),
  ADD KEY `FK_DetalleClasificacionVerde_Variedad` (`id_variedad`),
  ADD KEY `FK_DetalleClasificacionVerde_ClasificacionUnitaria` (`id_clasificacion_unitaria`),
  ADD KEY `FK_DetalleClasificacionVerde_ClasificacionVerde` (`id_clasificacion_verde`);

--
-- Indices de la tabla `detalle_cliente`
--
ALTER TABLE `detalle_cliente`
  ADD PRIMARY KEY (`id_detalle_cliente`),
  ADD KEY `FK_DetalleCliente_Pais` (`codigo_pais`),
  ADD KEY `FK_DetalleCliente_Cliente` (`id_cliente`);

--
-- Indices de la tabla `detalle_cliente_contacto`
--
ALTER TABLE `detalle_cliente_contacto`
  ADD PRIMARY KEY (`id_detalle_cliente_contacto`),
  ADD KEY `id_detalle_cliente` (`id_detalle_cliente`),
  ADD KEY `id_detalle_contacto` (`id_contacto`);

--
-- Indices de la tabla `detalle_empaque`
--
ALTER TABLE `detalle_empaque`
  ADD PRIMARY KEY (`id_detalle_empaque`),
  ADD KEY `id_empaque` (`id_empaque`),
  ADD KEY `id_clasificacion_ramo` (`id_clasificacion_ramo`),
  ADD KEY `id_variedad` (`id_variedad`);

--
-- Indices de la tabla `detalle_envio`
--
ALTER TABLE `detalle_envio`
  ADD PRIMARY KEY (`id_detalle_envio`),
  ADD KEY `FK_DetalleEnvio_AgenciaTransporte` (`id_agencia_transporte`),
  ADD KEY `FK_DetalleEnvio_Envio` (`id_envio`),
  ADD KEY `FK_DetalleEnvio_Especificacion` (`id_especificacion`);

--
-- Indices de la tabla `detalle_especificacionempaque`
--
ALTER TABLE `detalle_especificacionempaque`
  ADD PRIMARY KEY (`id_detalle_especificacionempaque`),
  ADD KEY `FK_DetalleEspecificacionEmpaque_Variedad` (`id_variedad`),
  ADD KEY `FK_DetalleEspecificacionEmpaque_ClasificacionRamo` (`id_clasificacion_ramo`),
  ADD KEY `FK_DetalleEspecificacionEmpaque_EmpaqueE` (`id_empaque_e`),
  ADD KEY `FK_DetalleEspecificacionEmpaque_EmpaqueP` (`id_empaque_p`),
  ADD KEY `FK_DetalleEspecificacionEmpaque_EspecificacionEmpaque` (`id_especificacion_empaque`),
  ADD KEY `FK_DetalleEspecificacionEmpaque_UnidadMedida` (`id_unidad_medida`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id_detalle_factura`),
  ADD KEY `id_comprobante` (`id_comprobante`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle_pedido`),
  ADD KEY `FK_DetallePedido_Pedido` (`id_pedido`),
  ADD KEY `FK_DetallePedido_ClienteEspecificacion` (`id_cliente_especificacion`);

--
-- Indices de la tabla `distribucion`
--
ALTER TABLE `distribucion`
  ADD PRIMARY KEY (`id_distribucion`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id_documento`);

--
-- Indices de la tabla `empaque`
--
ALTER TABLE `empaque`
  ADD PRIMARY KEY (`id_empaque`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_Empaque_ConfiguracionEmpresa` (`id_configuracion_empresa`);

--
-- Indices de la tabla `envio`
--
ALTER TABLE `envio`
  ADD PRIMARY KEY (`id_envio`),
  ADD KEY `FK_Envio_Pedido` (`id_pedido`);

--
-- Indices de la tabla `especificacion`
--
ALTER TABLE `especificacion`
  ADD PRIMARY KEY (`id_especificacion`);

--
-- Indices de la tabla `especificacion_empaque`
--
ALTER TABLE `especificacion_empaque`
  ADD PRIMARY KEY (`id_especificacion_empaque`),
  ADD KEY `FK_EspecificacionEmpaque_Especificacion` (`id_especificacion`),
  ADD KEY `FK_EspecificacionEmpaque_Empaque` (`id_empaque`);

--
-- Indices de la tabla `grosor_ramo`
--
ALTER TABLE `grosor_ramo`
  ADD PRIMARY KEY (`id_grosor_ramo`);

--
-- Indices de la tabla `grupo_menu`
--
ALTER TABLE `grupo_menu`
  ADD PRIMARY KEY (`id_grupo_menu`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `icono`
--
ALTER TABLE `icono`
  ADD PRIMARY KEY (`id_icono`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  ADD PRIMARY KEY (`id_impuesto`),
  ADD KEY `codigo` (`codigo`);

--
-- Indices de la tabla `impuesto_desglose_envio_factura`
--
ALTER TABLE `impuesto_desglose_envio_factura`
  ADD PRIMARY KEY (`id_impuesto_desglose_envio_factura`),
  ADD KEY `codigo_impuesto` (`codigo_impuesto`),
  ADD KEY `id_desglose_envio_factura` (`id_desglose_envio_factura`),
  ADD KEY `codigo_porcentaje` (`codigo_porcentaje`);

--
-- Indices de la tabla `impuesto_detalle_factura`
--
ALTER TABLE `impuesto_detalle_factura`
  ADD PRIMARY KEY (`id_impuesto_detalle_factura`),
  ADD KEY `id_detalle_comprobante` (`id_detalle_factura`),
  ADD KEY `codigo_impuesto` (`codigo_impuesto`),
  ADD KEY `codigo_porcentaje` (`codigo_porcentaje`);

--
-- Indices de la tabla `informacion_adicional_factura`
--
ALTER TABLE `informacion_adicional_factura`
  ADD PRIMARY KEY (`id_informacion_adicional_factura`),
  ADD KEY `id_comprobante` (`id_comprobante`);

--
-- Indices de la tabla `inventario_frio`
--
ALTER TABLE `inventario_frio`
  ADD PRIMARY KEY (`id_inventario_frio`),
  ADD KEY `FK_InventarioFrio_Variedad` (`id_variedad`),
  ADD KEY `FK_InventarioFrio_ClasificacionRamo` (`id_clasificacion_ramo`),
  ADD KEY `FK_InventarioFrio_EmpaqueE` (`id_empaque_e`),
  ADD KEY `FK_InventarioFrio_EmpaqueP` (`id_empaque_p`),
  ADD KEY `FK_InventarioFrio_UnidadMedida` (`id_unidad_medida`);

--
-- Indices de la tabla `lote`
--
ALTER TABLE `lote`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `FK_Lote_Modulo` (`id_modulo`);

--
-- Indices de la tabla `lote_re`
--
ALTER TABLE `lote_re`
  ADD PRIMARY KEY (`id_lote_re`),
  ADD KEY `FK_LoteRE_Variedad` (`id_variedad`),
  ADD KEY `FK_LoteRE_ClasificacionUnitaria` (`id_clasificacion_unitaria`),
  ADD KEY `FK_LoteRE_ClasificacionVerde` (`id_clasificacion_verde`);

--
-- Indices de la tabla `marcacion`
--
ALTER TABLE `marcacion`
  ADD PRIMARY KEY (`id_marcacion`),
  ADD KEY `FK_Marcacion_EspecificacionEmpaque` (`id_especificacion_empaque`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_Menu_GrupoMenu` (`id_grupo_menu`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id_modulo`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_Modulo_Sector` (`id_sector`);

--
-- Indices de la tabla `pais`
--
ALTER TABLE `pais`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `FK_Pedido_Cliente` (`id_cliente`);

--
-- Indices de la tabla `planta`
--
ALTER TABLE `planta`
  ADD PRIMARY KEY (`id_planta`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `precio`
--
ALTER TABLE `precio`
  ADD PRIMARY KEY (`id_precio`),
  ADD KEY `FK_Precio_Variedad` (`id_variedad`),
  ADD KEY `FK_Precio_ClasificacionRamo` (`id_clasificacion_ramo`);

--
-- Indices de la tabla `recepcion`
--
ALTER TABLE `recepcion`
  ADD PRIMARY KEY (`id_recepcion`),
  ADD KEY `FK_Recepcion_Semana` (`id_semana`),
  ADD KEY `FK_Recepcion_Cosecha` (`id_cosecha`);

--
-- Indices de la tabla `recepcion_clasificacion_verde`
--
ALTER TABLE `recepcion_clasificacion_verde`
  ADD PRIMARY KEY (`id_recepcion_clasificacion_verde`),
  ADD KEY `FK_RecepcionClasificacionVerde_Recepcion` (`id_recepcion`),
  ADD KEY `FK_RecepcionClasificacionVerde_ClasificacionVerde` (`id_clasificacion_verde`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `rol_submenu`
--
ALTER TABLE `rol_submenu`
  ADD PRIMARY KEY (`id_rol_submenu`),
  ADD KEY `FK_RolSubmenu_Rol` (`id_rol`),
  ADD KEY `FK_RolSubmenu_Submenu` (`id_submenu`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id_sector`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `semana`
--
ALTER TABLE `semana`
  ADD PRIMARY KEY (`id_semana`),
  ADD KEY `FK_Semana_Variedad` (`id_variedad`);

--
-- Indices de la tabla `stock_apertura`
--
ALTER TABLE `stock_apertura`
  ADD PRIMARY KEY (`id_stock_apertura`),
  ADD KEY `FK_StockApertura_Variedad` (`id_variedad`),
  ADD KEY `FK_StockApertura_ClasificacionUnitaria` (`id_clasificacion_unitaria`),
  ADD KEY `FK_StockApertura_LoteRE` (`id_lote_re`);

--
-- Indices de la tabla `stock_empaquetado`
--
ALTER TABLE `stock_empaquetado`
  ADD PRIMARY KEY (`id_stock_empaquetado`),
  ADD KEY `FK_StockEmpaquetado_Variedad` (`id_variedad`);

--
-- Indices de la tabla `stock_frio`
--
ALTER TABLE `stock_frio`
  ADD PRIMARY KEY (`id_stock_frio`),
  ADD KEY `FK_StockFrio_Consumo` (`id_consumo`),
  ADD KEY `FK_StockFrio_ClasificacionUnitaria` (`id_clasificacion_unitaria`),
  ADD KEY `FK_StockFrio_StockApertura` (`id_stock_apertura`),
  ADD KEY `FK_StockFrio_Semana` (`id_semana`),
  ADD KEY `FK_StockFrio_Variedad` (`id_variedad`);

--
-- Indices de la tabla `stock_guarde`
--
ALTER TABLE `stock_guarde`
  ADD PRIMARY KEY (`id_stock_guarde`),
  ADD KEY `FK_StockGuarde_ClasificacionUnitaria` (`id_clasificacion_unitaria`),
  ADD KEY `FK_StockGuarde_LoteRE` (`id_lote_re`),
  ADD KEY `FK_StockGuarde_Variedad` (`id_variedad`);

--
-- Indices de la tabla `submenu`
--
ALTER TABLE `submenu`
  ADD PRIMARY KEY (`id_submenu`),
  ADD UNIQUE KEY `url` (`url`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_Submenu_Menu` (`id_menu`);

--
-- Indices de la tabla `tipo_comprobante`
--
ALTER TABLE `tipo_comprobante`
  ADD PRIMARY KEY (`id_tipo_comprobante`);

--
-- Indices de la tabla `tipo_identificacion`
--
ALTER TABLE `tipo_identificacion`
  ADD PRIMARY KEY (`id_tipo_identificacion`);

--
-- Indices de la tabla `tipo_impuesto`
--
ALTER TABLE `tipo_impuesto`
  ADD PRIMARY KEY (`id_tipo_impuesto`),
  ADD KEY `codigo_impuesto` (`codigo_impuesto`),
  ADD KEY `codigo` (`codigo`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `nombre_completo` (`nombre_completo`),
  ADD KEY `FK_Usuario_Rol` (`id_rol`);

--
-- Indices de la tabla `variedad`
--
ALTER TABLE `variedad`
  ADD PRIMARY KEY (`id_variedad`),
  ADD KEY `FK_Variedad_Planta` (`id_planta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agencia_carga`
--
ALTER TABLE `agencia_carga`
  MODIFY `id_agencia_carga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aerolineas`
--
ALTER TABLE `agencia_transporte`
  MODIFY `id_agencia_transporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clasificacion_ramo`
--
ALTER TABLE `clasificacion_ramo`
  MODIFY `id_clasificacion_ramo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clasificacion_unitaria`
--
ALTER TABLE `clasificacion_unitaria`
  MODIFY `id_clasificacion_unitaria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clasificacion_verde`
--
ALTER TABLE `clasificacion_verde`
  MODIFY `id_clasificacion_verde` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_agenciacarga`
--
ALTER TABLE `cliente_agenciacarga`
  MODIFY `id_cliente_agencia_carga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_pedido_especificacion`
--
ALTER TABLE `cliente_pedido_especificacion`
  MODIFY `id_cliente_pedido_especificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `codigo_dae`
--
ALTER TABLE `codigo_dae`
  MODIFY `id_codigo_dae` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `coloracion`
--
ALTER TABLE `coloracion`
  MODIFY `id_coloracion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id_comprobante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_empresa`
--
ALTER TABLE `configuracion_empresa`
  MODIFY `id_configuracion_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion_user`
--
ALTER TABLE `configuracion_user`
  MODIFY `id_configuracion_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `consumo`
--
ALTER TABLE `consumo`
  MODIFY `id_consumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contacto`
--
ALTER TABLE `contacto`
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cosecha`
--
ALTER TABLE `cosecha`
  MODIFY `id_cosecha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `desglose_envio_factura`
--
ALTER TABLE `desglose_envio_factura`
  MODIFY `id_desglose_envio_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `desglose_recepcion`
--
ALTER TABLE `desglose_recepcion`
  MODIFY `id_desglose_recepcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_clasificacion_verde`
--
ALTER TABLE `detalle_clasificacion_verde`
  MODIFY `id_detalle_clasificacion_verde` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_cliente`
--
ALTER TABLE `detalle_cliente`
  MODIFY `id_detalle_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_cliente_contacto`
--
ALTER TABLE `detalle_cliente_contacto`
  MODIFY `id_detalle_cliente_contacto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_empaque`
--
ALTER TABLE `detalle_empaque`
  MODIFY `id_detalle_empaque` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_envio`
--
ALTER TABLE `detalle_envio`
  MODIFY `id_detalle_envio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_especificacionempaque`
--
ALTER TABLE `detalle_especificacionempaque`
  MODIFY `id_detalle_especificacionempaque` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id_detalle_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `distribucion`
--
ALTER TABLE `distribucion`
  MODIFY `id_distribucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empaque`
--
ALTER TABLE `empaque`
  MODIFY `id_empaque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `envio`
--
ALTER TABLE `envio`
  MODIFY `id_envio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especificacion`
--
ALTER TABLE `especificacion`
  MODIFY `id_especificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especificacion_empaque`
--
ALTER TABLE `especificacion_empaque`
  MODIFY `id_especificacion_empaque` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grosor_ramo`
--
ALTER TABLE `grosor_ramo`
  MODIFY `id_grosor_ramo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `grupo_menu`
--
ALTER TABLE `grupo_menu`
  MODIFY `id_grupo_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `icono`
--
ALTER TABLE `icono`
  MODIFY `id_icono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=863;

--
-- AUTO_INCREMENT de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  MODIFY `id_impuesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `impuesto_desglose_envio_factura`
--
ALTER TABLE `impuesto_desglose_envio_factura`
  MODIFY `id_impuesto_desglose_envio_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impuesto_detalle_factura`
--
ALTER TABLE `impuesto_detalle_factura`
  MODIFY `id_impuesto_detalle_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informacion_adicional_factura`
--
ALTER TABLE `informacion_adicional_factura`
  MODIFY `id_informacion_adicional_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_frio`
--
ALTER TABLE `inventario_frio`
  MODIFY `id_inventario_frio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lote`
--
ALTER TABLE `lote`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lote_re`
--
ALTER TABLE `lote_re`
  MODIFY `id_lote_re` int(111) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcacion`
--
ALTER TABLE `marcacion`
  MODIFY `id_marcacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planta`
--
ALTER TABLE `planta`
  MODIFY `id_planta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `precio`
--
ALTER TABLE `precio`
  MODIFY `id_precio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recepcion`
--
ALTER TABLE `recepcion`
  MODIFY `id_recepcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recepcion_clasificacion_verde`
--
ALTER TABLE `recepcion_clasificacion_verde`
  MODIFY `id_recepcion_clasificacion_verde` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol_submenu`
--
ALTER TABLE `rol_submenu`
  MODIFY `id_rol_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id_sector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `semana`
--
ALTER TABLE `semana`
  MODIFY `id_semana` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=261;

--
-- AUTO_INCREMENT de la tabla `stock_apertura`
--
ALTER TABLE `stock_apertura`
  MODIFY `id_stock_apertura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_empaquetado`
--
ALTER TABLE `stock_empaquetado`
  MODIFY `id_stock_empaquetado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_frio`
--
ALTER TABLE `stock_frio`
  MODIFY `id_stock_frio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_guarde`
--
ALTER TABLE `stock_guarde`
  MODIFY `id_stock_guarde` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `submenu`
--
ALTER TABLE `submenu`
  MODIFY `id_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `tipo_comprobante`
--
ALTER TABLE `tipo_comprobante`
  MODIFY `id_tipo_comprobante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipo_identificacion`
--
ALTER TABLE `tipo_identificacion`
  MODIFY `id_tipo_identificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipo_impuesto`
--
ALTER TABLE `tipo_impuesto`
  MODIFY `id_tipo_impuesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `variedad`
--
ALTER TABLE `variedad`
  MODIFY `id_variedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clasificacion_ramo`
--
ALTER TABLE `clasificacion_ramo`
  ADD CONSTRAINT `FK_ClasificacionRamo_ConfiguracionEmpresa` FOREIGN KEY (`id_configuracion_empresa`) REFERENCES `configuracion_empresa` (`id_configuracion_empresa`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClasificacionRamo_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `clasificacion_unitaria`
--
ALTER TABLE `clasificacion_unitaria`
  ADD CONSTRAINT `FK_ClasificacionUnitaria_ClasificacionRamoEstandar` FOREIGN KEY (`id_clasificacion_ramo_estandar`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClasificacionUnitaria_ClasificacionRamoReal` FOREIGN KEY (`id_clasificacion_ramo_real`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClasificacionUnitaria_ConfiguracionEmpresa` FOREIGN KEY (`id_configuracion_empresa`) REFERENCES `configuracion_empresa` (`id_configuracion_empresa`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClasificacionUnitaria_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `clasificacion_verde`
--
ALTER TABLE `clasificacion_verde`
  ADD CONSTRAINT `FK_ClasificacionVerde_Semana` FOREIGN KEY (`id_semana`) REFERENCES `semana` (`id_semana`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente_agenciacarga`
--
ALTER TABLE `cliente_agenciacarga`
  ADD CONSTRAINT `FK_ClienteAgenciaCarga_AgenciaCarga` FOREIGN KEY (`id_agencia_carga`) REFERENCES `agencia_carga` (`id_agencia_carga`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClienteAgenciaCarga_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente_pedido_especificacion`
--
ALTER TABLE `cliente_pedido_especificacion`
  ADD CONSTRAINT `FK_ClientePedidoEspecificacion_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ClientePedidoEspecificacion_Especificacion` FOREIGN KEY (`id_especificacion`) REFERENCES `especificacion` (`id_especificacion`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `codigo_dae`
--
ALTER TABLE `codigo_dae`
  ADD CONSTRAINT `FK_codigo_dae_pais` FOREIGN KEY (`codigo_pais`) REFERENCES `pais` (`codigo`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `coloracion`
--
ALTER TABLE `coloracion`
  ADD CONSTRAINT `FK_Coloracion_Marcacion` FOREIGN KEY (`id_marcacion`) REFERENCES `marcacion` (`id_marcacion`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `configuracion_user`
--
ALTER TABLE `configuracion_user`
  ADD CONSTRAINT `FK_ConfiguracionUser_Usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `desglose_envio_factura`
--
ALTER TABLE `desglose_envio_factura`
  ADD CONSTRAINT `FK_desglose_envio_factura_comprobante` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobante` (`id_comprobante`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `desglose_recepcion`
--
ALTER TABLE `desglose_recepcion`
  ADD CONSTRAINT `FK_DesgloseRecepcion_Recepcion` FOREIGN KEY (`id_recepcion`) REFERENCES `recepcion` (`id_recepcion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DesgloseRecepcion_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_clasificacion_verde`
--
ALTER TABLE `detalle_clasificacion_verde`
  ADD CONSTRAINT `FK_DetalleClasificacionVerde_ClasificacionUnitaria` FOREIGN KEY (`id_clasificacion_unitaria`) REFERENCES `clasificacion_unitaria` (`id_clasificacion_unitaria`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleClasificacionVerde_ClasificacionVerde` FOREIGN KEY (`id_clasificacion_verde`) REFERENCES `clasificacion_verde` (`id_clasificacion_verde`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleClasificacionVerde_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_cliente`
--
ALTER TABLE `detalle_cliente`
  ADD CONSTRAINT `FK_DetalleCliente_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleCliente_Pais` FOREIGN KEY (`codigo_pais`) REFERENCES `pais` (`codigo`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_cliente_contacto`
--
ALTER TABLE `detalle_cliente_contacto`
  ADD CONSTRAINT `FK_DetalleClienteContacto_Contacto` FOREIGN KEY (`id_contacto`) REFERENCES `contacto` (`id_contacto`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleClienteContacto_DetalleCliente` FOREIGN KEY (`id_detalle_cliente`) REFERENCES `detalle_cliente` (`id_detalle_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_empaque`
--
ALTER TABLE `detalle_empaque`
  ADD CONSTRAINT `FK_DetalleEmpaque_ClasificacionRamo` FOREIGN KEY (`id_clasificacion_ramo`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEmpaque_Empaque` FOREIGN KEY (`id_empaque`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEmpaque_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_envio`
--
ALTER TABLE `detalle_envio`
  ADD CONSTRAINT `FK_DetalleEnvio_AgenciaTransporte` FOREIGN KEY (`id_agencia_transporte`) REFERENCES `agencia_transporte` (`id_agencia_transporte`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEnvio_Envio` FOREIGN KEY (`id_envio`) REFERENCES `envio` (`id_envio`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEnvio_Especificacion` FOREIGN KEY (`id_especificacion`) REFERENCES `especificacion` (`id_especificacion`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_especificacionempaque`
--
ALTER TABLE `detalle_especificacionempaque`
  ADD CONSTRAINT `FK_DetalleEspecificacionEmpaque_ClasificacionRamo` FOREIGN KEY (`id_clasificacion_ramo`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEspecificacionEmpaque_EmpaqueE` FOREIGN KEY (`id_empaque_e`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEspecificacionEmpaque_EmpaqueP` FOREIGN KEY (`id_empaque_p`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEspecificacionEmpaque_EspecificacionEmpaque` FOREIGN KEY (`id_especificacion_empaque`) REFERENCES `especificacion_empaque` (`id_especificacion_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEspecificacionEmpaque_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetalleEspecificacionEmpaque_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `FK_detalle_comprobante_comprobante` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobante` (`id_comprobante`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `FK_DetallePedido_ClienteEspecificacion` FOREIGN KEY (`id_cliente_especificacion`) REFERENCES `cliente_pedido_especificacion` (`id_cliente_pedido_especificacion`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DetallePedido_Pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `empaque`
--
ALTER TABLE `empaque`
  ADD CONSTRAINT `FK_Empaque_ConfiguracionEmpresa` FOREIGN KEY (`id_configuracion_empresa`) REFERENCES `configuracion_empresa` (`id_configuracion_empresa`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `envio`
--
ALTER TABLE `envio`
  ADD CONSTRAINT `FK_Envio_Pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `especificacion_empaque`
--
ALTER TABLE `especificacion_empaque`
  ADD CONSTRAINT `FK_EspecificacionEmpaque_Empaque` FOREIGN KEY (`id_empaque`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EspecificacionEmpaque_Especificacion` FOREIGN KEY (`id_especificacion`) REFERENCES `especificacion` (`id_especificacion`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `impuesto_detalle_factura`
--
ALTER TABLE `impuesto_detalle_factura`
  ADD CONSTRAINT `FK_impuesto_detalle_factura_detalle_factura` FOREIGN KEY (`id_detalle_factura`) REFERENCES `detalle_factura` (`id_detalle_factura`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_impuesto_detalle_factura_impuesto` FOREIGN KEY (`codigo_impuesto`) REFERENCES `impuesto` (`codigo`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `informacion_adicional_factura`
--
ALTER TABLE `informacion_adicional_factura`
  ADD CONSTRAINT `FK_informacion_adicional_factura_comprobante` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobante` (`id_comprobante`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `inventario_frio`
--
ALTER TABLE `inventario_frio`
  ADD CONSTRAINT `FK_InventarioFrio_ClasificacionRamo` FOREIGN KEY (`id_clasificacion_ramo`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_EmpaqueE` FOREIGN KEY (`id_empaque_e`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_EmpaqueP` FOREIGN KEY (`id_empaque_p`) REFERENCES `empaque` (`id_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_InventarioFrio_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `lote`
--
ALTER TABLE `lote`
  ADD CONSTRAINT `FK_Lote_Modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `lote_re`
--
ALTER TABLE `lote_re`
  ADD CONSTRAINT `FK_LoteRE_ClasificacionUnitaria` FOREIGN KEY (`id_clasificacion_unitaria`) REFERENCES `clasificacion_unitaria` (`id_clasificacion_unitaria`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_LoteRE_ClasificacionVerde` FOREIGN KEY (`id_clasificacion_verde`) REFERENCES `clasificacion_verde` (`id_clasificacion_verde`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_LoteRE_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `marcacion`
--
ALTER TABLE `marcacion`
  ADD CONSTRAINT `FK_Marcacion_EspecificacionEmpaque` FOREIGN KEY (`id_especificacion_empaque`) REFERENCES `especificacion_empaque` (`id_especificacion_empaque`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `FK_Menu_GrupoMenu` FOREIGN KEY (`id_grupo_menu`) REFERENCES `grupo_menu` (`id_grupo_menu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD CONSTRAINT `FK_Modulo_Sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `FK_Pedido_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `precio`
--
ALTER TABLE `precio`
  ADD CONSTRAINT `FK_Precio_ClasificacionRamo` FOREIGN KEY (`id_clasificacion_ramo`) REFERENCES `clasificacion_ramo` (`id_clasificacion_ramo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Precio_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `recepcion`
--
ALTER TABLE `recepcion`
  ADD CONSTRAINT `FK_Recepcion_Cosecha` FOREIGN KEY (`id_cosecha`) REFERENCES `cosecha` (`id_cosecha`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Recepcion_Semana` FOREIGN KEY (`id_semana`) REFERENCES `semana` (`id_semana`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `recepcion_clasificacion_verde`
--
ALTER TABLE `recepcion_clasificacion_verde`
  ADD CONSTRAINT `FK_RecepcionClasificacionVerde_ClasificacionVerde` FOREIGN KEY (`id_clasificacion_verde`) REFERENCES `clasificacion_verde` (`id_clasificacion_verde`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_RecepcionClasificacionVerde_Recepcion` FOREIGN KEY (`id_recepcion`) REFERENCES `recepcion` (`id_recepcion`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `rol_submenu`
--
ALTER TABLE `rol_submenu`
  ADD CONSTRAINT `FK_RolSubmenu_Rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_RolSubmenu_Submenu` FOREIGN KEY (`id_submenu`) REFERENCES `submenu` (`id_submenu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `semana`
--
ALTER TABLE `semana`
  ADD CONSTRAINT `FK_Semana_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_apertura`
--
ALTER TABLE `stock_apertura`
  ADD CONSTRAINT `FK_StockApertura_ClasificacionUnitaria` FOREIGN KEY (`id_clasificacion_unitaria`) REFERENCES `clasificacion_unitaria` (`id_clasificacion_unitaria`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockApertura_LoteRE` FOREIGN KEY (`id_lote_re`) REFERENCES `lote_re` (`id_lote_re`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockApertura_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_empaquetado`
--
ALTER TABLE `stock_empaquetado`
  ADD CONSTRAINT `FK_StockEmpaquetado_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_frio`
--
ALTER TABLE `stock_frio`
  ADD CONSTRAINT `FK_StockFrio_ClasificacionUnitaria` FOREIGN KEY (`id_clasificacion_unitaria`) REFERENCES `clasificacion_unitaria` (`id_clasificacion_unitaria`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockFrio_Consumo` FOREIGN KEY (`id_consumo`) REFERENCES `consumo` (`id_consumo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockFrio_Semana` FOREIGN KEY (`id_semana`) REFERENCES `semana` (`id_semana`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockFrio_StockApertura` FOREIGN KEY (`id_stock_apertura`) REFERENCES `stock_apertura` (`id_stock_apertura`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockFrio_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `stock_guarde`
--
ALTER TABLE `stock_guarde`
  ADD CONSTRAINT `FK_StockGuarde_ClasificacionUnitaria` FOREIGN KEY (`id_clasificacion_unitaria`) REFERENCES `clasificacion_unitaria` (`id_clasificacion_unitaria`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockGuarde_LoteRE` FOREIGN KEY (`id_lote_re`) REFERENCES `lote_re` (`id_lote_re`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_StockGuarde_Variedad` FOREIGN KEY (`id_variedad`) REFERENCES `variedad` (`id_variedad`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `submenu`
--
ALTER TABLE `submenu`
  ADD CONSTRAINT `FK_Submenu_Menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_Usuario_Rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `variedad`
--
ALTER TABLE `variedad`
  ADD CONSTRAINT `FK_Variedad_Planta` FOREIGN KEY (`id_planta`) REFERENCES `planta` (`id_planta`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
