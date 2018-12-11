-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-12-2018 a las 17:24:25
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
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `descripcion` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `fecha_pedido` date NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empaquetado` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `id_cliente`, `estado`, `descripcion`, `fecha_pedido`, `fecha_registro`, `empaquetado`) VALUES
(1, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2018-12-11', '2018-12-11 10:27:59', 0),
(2, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2018-12-17', '2018-12-11 10:27:59', 0),
(3, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2018-12-24', '2018-12-11 10:27:59', 0),
(4, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2018-12-31', '2018-12-11 10:28:00', 0),
(5, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-01-07', '2018-12-11 10:28:00', 0),
(6, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-01-14', '2018-12-11 10:28:00', 0),
(7, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-01-21', '2018-12-11 10:28:00', 0),
(8, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-01-28', '2018-12-11 10:28:01', 0),
(9, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-02-04', '2018-12-11 10:28:01', 0),
(10, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-02-11', '2018-12-11 10:28:01', 0),
(11, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-02-18', '2018-12-11 10:28:01', 0),
(12, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-02-25', '2018-12-11 10:28:01', 0),
(13, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-03-04', '2018-12-11 10:28:02', 0),
(14, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-03-11', '2018-12-11 10:28:02', 0),
(15, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-03-18', '2018-12-11 10:28:02', 0),
(16, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-03-25', '2018-12-11 10:28:02', 0),
(17, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-04-01', '2018-12-11 10:28:02', 0),
(18, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-04-08', '2018-12-11 10:28:02', 0),
(19, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-04-15', '2018-12-11 10:28:03', 0),
(20, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-04-22', '2018-12-11 10:28:03', 0),
(21, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-04-29', '2018-12-11 10:28:03', 0),
(22, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-05-06', '2018-12-11 10:28:03', 0),
(23, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-05-13', '2018-12-11 10:28:03', 0),
(24, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-05-20', '2018-12-11 10:28:03', 0),
(25, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-05-27', '2018-12-11 10:28:04', 0),
(26, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-06-03', '2018-12-11 10:28:04', 0),
(27, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-06-10', '2018-12-11 10:28:04', 0),
(28, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-06-17', '2018-12-11 10:28:04', 0),
(29, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-06-24', '2018-12-11 10:28:04', 0),
(30, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-07-01', '2018-12-11 10:28:04', 0),
(31, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-07-08', '2018-12-11 10:28:05', 0),
(32, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-07-15', '2018-12-11 10:28:05', 0),
(33, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-07-22', '2018-12-11 10:28:05', 0),
(34, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-07-29', '2018-12-11 10:28:05', 0),
(35, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-08-05', '2018-12-11 10:28:05', 0),
(36, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-08-12', '2018-12-11 10:28:05', 0),
(37, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-08-19', '2018-12-11 10:28:05', 0),
(38, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-08-26', '2018-12-11 10:28:05', 0),
(39, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-09-02', '2018-12-11 10:28:06', 0),
(40, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-09-09', '2018-12-11 10:28:06', 0),
(41, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-09-16', '2018-12-11 10:28:06', 0),
(42, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-09-23', '2018-12-11 10:28:06', 0),
(43, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-09-30', '2018-12-11 10:28:06', 0),
(44, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-10-07', '2018-12-11 10:28:06', 0),
(45, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-10-14', '2018-12-11 10:28:06', 0),
(46, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-10-21', '2018-12-11 10:28:06', 0),
(47, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-10-28', '2018-12-11 10:28:06', 0),
(48, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-11-04', '2018-12-11 10:28:06', 0),
(49, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-11-11', '2018-12-11 10:28:06', 0),
(50, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-11-18', '2018-12-11 10:28:06', 0),
(51, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-11-25', '2018-12-11 10:28:06', 0),
(52, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-12-02', '2018-12-11 10:28:07', 0),
(53, 4, 1, 'Pedidos de los lunes de 2 Combo 1', '2019-12-09', '2018-12-11 10:28:07', 0),
(54, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2018-12-17', '2018-12-11 10:32:38', 0),
(55, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2018-12-31', '2018-12-11 10:32:38', 0),
(56, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-01-14', '2018-12-11 10:32:38', 0),
(57, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-01-28', '2018-12-11 10:32:38', 0),
(58, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-02-11', '2018-12-11 10:32:38', 0),
(59, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-02-25', '2018-12-11 10:32:38', 0),
(60, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-03-11', '2018-12-11 10:32:38', 0),
(61, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-03-25', '2018-12-11 10:32:38', 0),
(62, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-04-08', '2018-12-11 10:32:39', 0),
(63, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-04-22', '2018-12-11 10:32:39', 0),
(64, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-05-06', '2018-12-11 10:32:39', 0),
(65, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-05-20', '2018-12-11 10:32:39', 0),
(66, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-06-03', '2018-12-11 10:32:39', 0),
(67, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-06-17', '2018-12-11 10:32:39', 0),
(68, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-07-01', '2018-12-11 10:32:40', 0),
(69, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-07-15', '2018-12-11 10:32:40', 0),
(70, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-07-29', '2018-12-11 10:32:40', 0),
(71, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-08-12', '2018-12-11 10:32:40', 0),
(72, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-08-26', '2018-12-11 10:32:40', 0),
(73, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-09-09', '2018-12-11 10:32:40', 0),
(74, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-09-23', '2018-12-11 10:32:40', 0),
(75, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-10-07', '2018-12-11 10:32:40', 0),
(76, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-10-21', '2018-12-11 10:32:40', 0),
(77, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-11-04', '2018-12-11 10:32:40', 0),
(78, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-11-18', '2018-12-11 10:32:40', 0),
(79, 1, 1, 'Pedidos de los lunes cada 2 semanas de 1 Combo 4', '2019-12-02', '2018-12-11 10:32:41', 0),
(80, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2018-12-11', '2018-12-11 10:33:38', 0),
(81, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2018-12-18', '2018-12-11 10:33:38', 0),
(82, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2018-12-25', '2018-12-11 10:33:38', 0),
(83, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-01-01', '2018-12-11 10:33:38', 0),
(84, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-01-08', '2018-12-11 10:33:38', 0),
(85, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-01-15', '2018-12-11 10:33:39', 0),
(86, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-01-22', '2018-12-11 10:33:39', 0),
(87, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-01-29', '2018-12-11 10:33:39', 0),
(88, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-02-05', '2018-12-11 10:33:39', 0),
(89, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-02-12', '2018-12-11 10:33:39', 0),
(90, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-02-19', '2018-12-11 10:33:39', 0),
(91, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-02-26', '2018-12-11 10:33:39', 0),
(92, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-03-05', '2018-12-11 10:33:39', 0),
(93, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-03-12', '2018-12-11 10:33:39', 0),
(94, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-03-19', '2018-12-11 10:33:40', 0),
(95, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-03-26', '2018-12-11 10:33:40', 0),
(96, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-04-02', '2018-12-11 10:33:40', 0),
(97, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-04-09', '2018-12-11 10:33:40', 0),
(98, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-04-16', '2018-12-11 10:33:40', 0),
(99, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-04-23', '2018-12-11 10:33:40', 0),
(100, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-04-30', '2018-12-11 10:33:40', 0),
(101, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-05-07', '2018-12-11 10:33:40', 0),
(102, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-05-14', '2018-12-11 10:33:40', 0),
(103, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-05-21', '2018-12-11 10:33:40', 0),
(104, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-05-28', '2018-12-11 10:33:40', 0),
(105, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-06-04', '2018-12-11 10:33:40', 0),
(106, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-06-11', '2018-12-11 10:33:41', 0),
(107, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-06-18', '2018-12-11 10:33:41', 0),
(108, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-06-25', '2018-12-11 10:33:41', 0),
(109, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-07-02', '2018-12-11 10:33:41', 0),
(110, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-07-09', '2018-12-11 10:33:41', 0),
(111, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-07-16', '2018-12-11 10:33:41', 0),
(112, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-07-23', '2018-12-11 10:33:41', 0),
(113, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-07-30', '2018-12-11 10:33:41', 0),
(114, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-08-06', '2018-12-11 10:33:41', 0),
(115, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-08-13', '2018-12-11 10:33:41', 0),
(116, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-08-20', '2018-12-11 10:33:41', 0),
(117, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-08-27', '2018-12-11 10:33:42', 0),
(118, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-09-03', '2018-12-11 10:33:42', 0),
(119, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-09-10', '2018-12-11 10:33:42', 0),
(120, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-09-17', '2018-12-11 10:33:42', 0),
(121, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-09-24', '2018-12-11 10:33:42', 0),
(122, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-10-01', '2018-12-11 10:33:42', 0),
(123, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-10-08', '2018-12-11 10:33:42', 0),
(124, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-10-15', '2018-12-11 10:33:42', 0),
(125, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-10-22', '2018-12-11 10:33:42', 0),
(126, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-10-29', '2018-12-11 10:33:42', 0),
(127, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-11-05', '2018-12-11 10:33:42', 0),
(128, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-11-12', '2018-12-11 10:33:42', 0),
(129, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-11-19', '2018-12-11 10:33:42', 0),
(130, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-11-26', '2018-12-11 10:33:43', 0),
(131, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-12-03', '2018-12-11 10:33:43', 0),
(132, 3, 1, 'Pedidos de los martes de 1 Combo 2', '2019-12-10', '2018-12-11 10:33:43', 0),
(133, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2018-12-11', '2018-12-11 10:38:50', 0),
(134, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2018-12-18', '2018-12-11 10:38:50', 0),
(135, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2018-12-25', '2018-12-11 10:38:50', 0),
(136, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-01-01', '2018-12-11 10:38:50', 0),
(137, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-01-08', '2018-12-11 10:38:50', 0),
(138, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-01-15', '2018-12-11 10:38:51', 0),
(139, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-01-22', '2018-12-11 10:38:51', 0),
(140, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-01-29', '2018-12-11 10:38:51', 0),
(141, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-02-05', '2018-12-11 10:38:51', 0),
(142, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-02-12', '2018-12-11 10:38:51', 0),
(143, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-02-19', '2018-12-11 10:38:51', 0),
(144, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-02-26', '2018-12-11 10:38:51', 0),
(145, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-03-05', '2018-12-11 10:38:52', 0),
(146, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-03-12', '2018-12-11 10:38:52', 0),
(147, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-03-19', '2018-12-11 10:38:52', 0),
(148, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-03-26', '2018-12-11 10:38:52', 0),
(149, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-04-02', '2018-12-11 10:38:52', 0),
(150, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-04-09', '2018-12-11 10:38:52', 0),
(151, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-04-16', '2018-12-11 10:38:52', 0),
(152, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-04-23', '2018-12-11 10:38:53', 0),
(153, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-04-30', '2018-12-11 10:38:53', 0),
(154, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-05-07', '2018-12-11 10:38:53', 0),
(155, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-05-14', '2018-12-11 10:38:53', 0),
(156, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-05-21', '2018-12-11 10:38:53', 0),
(157, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-05-28', '2018-12-11 10:38:53', 0),
(158, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-06-04', '2018-12-11 10:38:53', 0),
(159, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-06-11', '2018-12-11 10:38:53', 0),
(160, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-06-18', '2018-12-11 10:38:54', 0),
(161, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-06-25', '2018-12-11 10:38:54', 0),
(162, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-07-02', '2018-12-11 10:38:54', 0),
(163, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-07-09', '2018-12-11 10:38:54', 0),
(164, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-07-16', '2018-12-11 10:38:54', 0),
(165, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-07-23', '2018-12-11 10:38:54', 0),
(166, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-07-30', '2018-12-11 10:38:54', 0),
(167, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-08-06', '2018-12-11 10:38:54', 0),
(168, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-08-13', '2018-12-11 10:38:54', 0),
(169, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-08-20', '2018-12-11 10:38:54', 0),
(170, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-08-27', '2018-12-11 10:38:55', 0),
(171, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-09-03', '2018-12-11 10:38:55', 0),
(172, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-09-10', '2018-12-11 10:38:55', 0),
(173, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-09-17', '2018-12-11 10:38:55', 0),
(174, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-09-24', '2018-12-11 10:38:55', 0),
(175, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-10-01', '2018-12-11 10:38:55', 0),
(176, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-10-08', '2018-12-11 10:38:55', 0),
(177, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-10-15', '2018-12-11 10:38:55', 0),
(178, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-10-22', '2018-12-11 10:38:55', 0),
(179, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-10-29', '2018-12-11 10:38:55', 0),
(180, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-11-05', '2018-12-11 10:38:56', 0),
(181, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-11-12', '2018-12-11 10:38:56', 0),
(182, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-11-19', '2018-12-11 10:38:56', 0),
(183, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-11-26', '2018-12-11 10:38:56', 0),
(184, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-12-03', '2018-12-11 10:38:56', 0),
(185, 4, 1, 'Pedidos de los martes de 2 Combo 5', '2019-12-10', '2018-12-11 10:38:56', 0),
(186, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2018-12-14', '2018-12-11 10:40:53', 0),
(187, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-01-14', '2018-12-11 10:40:53', 0),
(188, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-02-14', '2018-12-11 10:40:53', 0),
(189, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-03-14', '2018-12-11 10:40:54', 0),
(190, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-04-14', '2018-12-11 10:40:54', 0),
(191, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-05-14', '2018-12-11 10:40:54', 0),
(192, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-06-14', '2018-12-11 10:40:54', 0),
(193, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-07-14', '2018-12-11 10:40:54', 0),
(194, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-08-14', '2018-12-11 10:40:54', 0),
(195, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-09-14', '2018-12-11 10:40:54', 0),
(196, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-10-14', '2018-12-11 10:40:54', 0),
(197, 1, 1, 'Pedidos de los días 14 de cada mes de 2 Combo 3', '2019-11-14', '2018-12-11 10:40:55', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `FK_Pedido_Cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `FK_Pedido_Cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
