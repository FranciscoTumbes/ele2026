-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-09-2026 a las 16:02:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cpiset`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actas_sufragio`
--

CREATE TABLE `actas_sufragio` (
  `id` int(10) UNSIGNED NOT NULL,
  `mesa_id` int(10) UNSIGNED NOT NULL,
  `eleccion_id` int(10) UNSIGNED NOT NULL,
  `electores_habilitados` int(10) UNSIGNED DEFAULT 0,
  `votos_validos` int(10) UNSIGNED DEFAULT 0,
  `votos_blancos` int(10) UNSIGNED DEFAULT 0,
  `votos_nulos` int(10) UNSIGNED DEFAULT 0,
  `votos_impugnados` int(10) UNSIGNED DEFAULT 0,
  `total_votantes` int(10) UNSIGNED DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `estado` enum('PENDIENTE','DIGITADA','VERIFICADA','CONSOLIDADA') DEFAULT 'PENDIENTE',
  `digitado_por` int(10) UNSIGNED DEFAULT NULL,
  `verificado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_digitacion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actas_sufragio`
--

INSERT INTO `actas_sufragio` (`id`, `mesa_id`, `eleccion_id`, `electores_habilitados`, `votos_validos`, `votos_blancos`, `votos_nulos`, `votos_impugnados`, `total_votantes`, `observaciones`, `estado`, `digitado_por`, `verificado_por`, `fecha_digitacion`, `created_at`, `updated_at`) VALUES
(3, 9, 1, 300, 185, 20, 21, 0, 226, NULL, 'DIGITADA', 9, NULL, '2026-09-22 23:50:01', '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(4, 10, 1, 300, 90, 10, 50, 0, 150, NULL, 'DIGITADA', 9, NULL, '2026-09-23 01:36:29', '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(5, 11, 1, 300, 196, 30, 40, 0, 266, NULL, 'DIGITADA', 9, NULL, '2026-09-23 03:46:35', '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(6, 12, 1, 300, 106, 50, 50, 0, 206, NULL, 'DIGITADA', 9, NULL, '2026-09-23 04:03:01', '2026-09-22 21:03:01', '2026-09-22 21:03:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agrupaciones_politicas`
--

CREATE TABLE `agrupaciones_politicas` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `siglas` varchar(20) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `color_hex` varchar(7) DEFAULT '#000000',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `agrupaciones_politicas`
--

INSERT INTO `agrupaciones_politicas` (`id`, `codigo`, `nombre`, `siglas`, `logo_url`, `color_hex`, `activo`, `created_at`) VALUES
(1, 'AP11', 'ACCION POPULAR', 'AP', NULL, '#FFD700', 1, '2026-09-22 21:30:00'),
(2, 'AP12', 'ALIANZA PARA EL PROGRESO', 'APP', NULL, '#0066CC', 1, '2026-09-22 21:30:00'),
(3, 'AP13', 'FE EN EL PERU', 'FEP', NULL, '#1bf174', 1, '2026-09-22 21:30:00'),
(4, 'AP14', 'PARTIDO APRISTA PERUANO', 'PAP', NULL, '#FFFFFF', 1, '2026-09-22 21:30:00'),
(5, 'AP15', 'PARTIDO DEMOCRATICO SOMOS PERU', 'SP', NULL, '#660099', 1, '2026-09-22 21:30:00'),
(6, 'AP16', 'PARTIDO POLITICO NACIONAL PERU LIBRE', 'PL', NULL, '#FF0000', 1, '2026-09-22 21:30:00'),
(7, 'AP17', 'RENOVACION POPULAR PERU', 'RP', NULL, '#0000FF', 1, '2026-09-22 21:30:00'),
(8, 'AP18', 'RENOVACION TUMBESINA', 'RT', NULL, '#CC3399', 1, '2026-09-22 21:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `tabla` varchar(50) NOT NULL,
  `registro_id` varchar(50) NOT NULL,
  `accion` enum('INSERT','UPDATE','DELETE','LOGIN','LOGOUT') NOT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `usuario_id`, `tabla`, `registro_id`, `accion`, `datos_anteriores`, `datos_nuevos`, `ip`, `created_at`) VALUES
(2, 1, 'actas_sufragio', '2', 'INSERT', NULL, NULL, '::1', '2026-09-22 15:55:44'),
(3, 9, 'actas_sufragio', '3', 'INSERT', NULL, NULL, '::1', '2026-09-22 16:50:01'),
(4, 9, 'actas_sufragio', '4', 'INSERT', NULL, NULL, '::1', '2026-09-22 18:36:29'),
(5, 9, 'actas_sufragio', '5', 'INSERT', NULL, NULL, '::1', '2026-09-22 20:46:35'),
(6, 9, 'actas_sufragio', '6', 'INSERT', NULL, NULL, '::1', '2026-09-22 21:03:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `candidatos`
--

CREATE TABLE `candidatos` (
  `id` int(10) UNSIGNED NOT NULL,
  `agrupacion_id` int(10) UNSIGNED NOT NULL,
  `cargo_id` int(10) UNSIGNED NOT NULL,
  `ambito_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_ambito` enum('PROVINCIA','DISTRITO','REGION') DEFAULT NULL,
  `dni` varchar(8) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `posicion_lista` int(10) UNSIGNED DEFAULT 1,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `candidatos`
--

INSERT INTO `candidatos` (`id`, `agrupacion_id`, `cargo_id`, `ambito_id`, `tipo_ambito`, `dni`, `nombres`, `apellido_paterno`, `apellido_materno`, `foto_url`, `posicion_lista`, `activo`, `created_at`) VALUES
(13, 1, 1, 54, 'DISTRITO', '00000001', 'LORENZO SOTERO', 'DIOS', 'YACILA', NULL, 1, 1, '2026-09-22 21:35:00'),
(14, 2, 1, 54, 'DISTRITO', '00000002', 'CARMEN VICTORIA', 'CASTILLO', 'VALDIVIEZO', NULL, 1, 1, '2026-09-22 21:35:00'),
(15, 3, 1, 54, 'DISTRITO', '00000003', 'JOSE ARMANDO', 'VILCHEZ', 'BARRIENTOS', NULL, 1, 1, '2026-09-22 21:35:00'),
(16, 4, 1, 54, 'DISTRITO', '00000004', 'JOSE MARTIN', 'MOGOLLON', 'MEDINA', NULL, 1, 1, '2026-09-22 21:35:00'),
(17, 5, 1, 54, 'DISTRITO', '00000005', 'ALEJANDRO', 'AREVALO', 'ORTIZ', NULL, 1, 1, '2026-09-22 21:35:00'),
(18, 6, 1, 54, 'DISTRITO', '00000006', 'JAVIER', 'SUCLUPE', 'SANDOVAL', NULL, 1, 1, '2026-09-22 21:35:00'),
(19, 7, 1, 54, 'DISTRITO', '00000007', 'CARMEN', 'CHIROQUE', 'PAICO', NULL, 1, 1, '2026-09-22 21:35:00'),
(20, 8, 1, 54, 'DISTRITO', '00000008', 'GRACIELA KATHERINE', 'VALDEZ', 'ZAPATA', NULL, 1, 1, '2026-09-22 21:35:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id` int(10) UNSIGNED NOT NULL,
  `eleccion_id` int(10) UNSIGNED NOT NULL,
  `ambito` enum('REGIONAL','PROVINCIAL','DISTRITAL') NOT NULL,
  `nombre_cargo` varchar(100) NOT NULL,
  `titular` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `eleccion_id`, `ambito`, `nombre_cargo`, `titular`, `created_at`) VALUES
(1, 1, 'PROVINCIAL', 'Alcalde Provincial', 1, '2026-09-22 21:40:00'),
(2, 1, 'PROVINCIAL', 'Regidor Provincial', 0, '2026-09-22 21:40:00'),
(3, 1, 'DISTRITAL', 'Alcalde Distrital', 0, '2026-09-22 21:40:00'),
(4, 2, 'REGIONAL', 'Gobernador Regional', 1, '2026-09-22 21:40:00'),
(5, 2, 'REGIONAL', 'Vicegobernador Regional', 0, '2026-09-22 21:40:00'),
(6, 2, 'REGIONAL', 'Consejero Regional', 0, '2026-09-22 21:40:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros_votacion`
--

CREATE TABLE `centros_votacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `distrito_id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(12) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `tipo_local` enum('LOCAL','ESCUELA','COLISEO','OTRO') DEFAULT 'LOCAL',
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `centros_votacion`
--

INSERT INTO `centros_votacion` (`id`, `distrito_id`, `codigo`, `nombre`, `direccion`, `tipo_local`, `latitud`, `longitud`, `activo`, `created_at`) VALUES
(6, 54, '01.04', 'IE 019 ISABEL SALINAS CUENCA DE ESPINOZA', 'Corrales, Tumbes', 'ESCUELA', -3.58900000, -80.42100000, 1, '2026-09-22 21:00:00'),
(7, 54, '01.05', 'IE 023 DIVINO JESUS DE NAZARET', 'Corrales, Tumbes', 'ESCUELA', -3.59100000, -80.42300000, 1, '2026-09-22 21:00:00'),
(8, 54, '01.06', 'IE TECNICO 7 DE ENERO', 'Corrales, Tumbes', 'ESCUELA', -3.59300000, -80.42500000, 1, '2026-09-22 21:00:00'),
(9, 54, '01.07', 'IE 020 HILARIO CARRASCO VINCES', 'Corrales, Tumbes', 'ESCUELA', -3.59500000, -80.42700000, 1, '2026-09-22 21:00:00'),
(10, 54, '01.08', 'IE 025 REPUBLICA DEL ECUADOR', 'Corrales, Tumbes', 'ESCUELA', -3.59700000, -80.42900000, 1, '2026-09-22 21:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_acta_candidato`
--

CREATE TABLE `detalle_acta_candidato` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acta_id` int(10) UNSIGNED NOT NULL,
  `candidato_id` int(10) UNSIGNED NOT NULL,
  `votos_obtenidos` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_acta_candidato`
--

INSERT INTO `detalle_acta_candidato` (`id`, `acta_id`, `candidato_id`, `votos_obtenidos`, `created_at`, `updated_at`) VALUES
(10, 3, 13, 10, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(11, 3, 14, 100, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(12, 3, 15, 10, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(13, 3, 16, 11, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(14, 3, 17, 12, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(15, 3, 18, 13, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(16, 3, 19, 14, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(17, 3, 20, 15, '2026-09-22 16:50:01', '2026-09-22 16:50:01'),
(18, 4, 13, 10, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(19, 4, 14, 50, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(20, 4, 15, 5, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(21, 4, 16, 5, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(22, 4, 17, 5, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(23, 4, 18, 5, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(24, 4, 19, 5, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(25, 4, 20, 5, '2026-09-22 18:36:29', '2026-09-22 18:36:29'),
(26, 5, 13, 5, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(27, 5, 14, 100, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(28, 5, 15, 10, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(29, 5, 16, 20, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(30, 5, 17, 20, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(31, 5, 18, 20, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(32, 5, 19, 20, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(33, 5, 20, 1, '2026-09-22 20:46:35', '2026-09-22 20:46:35'),
(34, 6, 13, 50, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(35, 6, 14, 50, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(36, 6, 15, 1, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(37, 6, 16, 1, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(38, 6, 17, 1, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(39, 6, 18, 1, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(40, 6, 19, 1, '2026-09-22 21:03:01', '2026-09-22 21:03:01'),
(41, 6, 20, 1, '2026-09-22 21:03:01', '2026-09-22 21:03:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distritos`
--

CREATE TABLE `distritos` (
  `id` int(10) UNSIGNED NOT NULL,
  `provincia_id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(9) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `distritos`
--

INSERT INTO `distritos` (`id`, `provincia_id`, `codigo`, `nombre`, `activo`, `created_at`) VALUES
(53, 7, 'TUM-01-01', 'Tumbes', 1, '2026-09-21 21:11:47'),
(54, 7, 'TUM-01-02', 'Corrales', 1, '2026-09-21 21:11:47'),
(55, 7, 'TUM-01-03', 'San Jacinto', 1, '2026-09-21 21:11:47'),
(56, 7, 'TUM-01-04', 'San Juan de la Virgen', 1, '2026-09-21 21:11:47'),
(57, 7, 'TUM-01-05', 'Pampas de Hospital', 1, '2026-09-21 21:11:47'),
(58, 7, 'TUM-01-06', 'La Cruz', 1, '2026-09-21 21:11:47'),
(59, 8, 'TUM-02-01', 'Zorritos', 1, '2026-09-21 21:11:47'),
(60, 8, 'TUM-02-02', 'Casitas', 1, '2026-09-21 21:11:47'),
(61, 8, 'TUM-02-03', 'Canoas de Punta Sal', 1, '2026-09-21 21:11:47'),
(62, 9, 'TUM-03-01', 'Zarumilla', 1, '2026-09-21 21:11:47'),
(63, 9, 'TUM-03-02', 'Aguas Verdes', 1, '2026-09-21 21:11:47'),
(64, 9, 'TUM-03-03', 'Matapalo', 1, '2026-09-21 21:11:47'),
(65, 9, 'TUM-03-04', 'Papayal', 1, '2026-09-21 21:11:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elecciones`
--

CREATE TABLE `elecciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `tipo` enum('MUNICIPAL','REGIONAL') NOT NULL,
  `fecha_eleccion` date NOT NULL,
  `estado` enum('CONVOCADA','EN_CURSO','FINALIZADA','ANULADA') DEFAULT 'CONVOCADA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `elecciones`
--

INSERT INTO `elecciones` (`id`, `region_id`, `nombre`, `tipo`, `fecha_eleccion`, `estado`, `created_at`) VALUES
(1, 1, 'Alcaldía', 'MUNICIPAL', '2026-10-04', 'CONVOCADA', '2026-09-22 13:30:36'),
(2, 1, 'Gobierno Regional', 'REGIONAL', '2026-10-04', 'CONVOCADA', '2026-09-22 13:31:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `electores`
--

CREATE TABLE `electores` (
  `id` int(10) UNSIGNED NOT NULL,
  `dni` varchar(8) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('M','F') DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `mesa_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('HABILITADO','TAQUILLADO','FALLECIDO','INHABILITADO') DEFAULT 'HABILITADO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas_sufragio`
--

CREATE TABLE `mesas_sufragio` (
  `id` int(10) UNSIGNED NOT NULL,
  `centro_id` int(10) UNSIGNED NOT NULL,
  `numero_mesa` varchar(10) NOT NULL,
  `electores_habilitados` int(10) UNSIGNED DEFAULT 0,
  `estado` enum('ACTIVA','CERRADA','ANULADA') DEFAULT 'ACTIVA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mesas_sufragio`
--

INSERT INTO `mesas_sufragio` (`id`, `centro_id`, `numero_mesa`, `electores_habilitados`, `estado`, `created_at`) VALUES
(9, 6, '80265', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(10, 6, '80266', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(11, 6, '80267', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(12, 6, '80268', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(13, 6, '80269', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(14, 7, '80270', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(15, 7, '80271', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(16, 7, '80272', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(17, 7, '80273', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(18, 7, '80274', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(19, 6, '80275', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(20, 6, '80276', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(21, 6, '80277', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(22, 8, '80278', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(23, 8, '80279', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(24, 8, '80280', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(25, 8, '80281', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(26, 8, '80282', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(27, 8, '80283', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(28, 8, '80284', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(29, 8, '80285', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(30, 8, '80286', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(31, 8, '80287', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(32, 8, '80288', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(33, 8, '80289', 300, 'ACTIVA', '2026-09-22 21:05:00'),
(34, 8, '80290', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(35, 8, '80291', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(36, 8, '80292', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(37, 8, '80293', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(38, 8, '80294', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(39, 8, '80295', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(40, 8, '80296', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(41, 8, '80297', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(42, 8, '80298', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(43, 8, '80299', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(44, 8, '80300', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(45, 9, '80301', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(46, 9, '80302', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(47, 9, '80303', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(48, 9, '80304', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(49, 9, '80305', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(50, 9, '80306', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(51, 9, '80307', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(52, 9, '80308', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(53, 9, '80309', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(54, 9, '80310', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(55, 9, '80311', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(56, 9, '80312', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(57, 9, '80313', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(58, 9, '80314', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(59, 9, '80315', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(60, 10, '80316', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(61, 10, '80317', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(62, 10, '80318', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(63, 10, '80319', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(64, 10, '80320', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(65, 10, '80321', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(66, 10, '80322', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(67, 10, '80323', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(68, 10, '80324', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(69, 10, '80325', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(70, 10, '80326', 100, 'ACTIVA', '2026-09-22 21:05:00'),
(71, 10, '80327', 100, 'ACTIVA', '2026-09-22 21:05:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

CREATE TABLE `provincias` (
  `id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`id`, `region_id`, `codigo`, `nombre`, `activo`, `created_at`) VALUES
(7, 1, 'TUM-01', 'Tumbes', 1, '2026-09-21 20:59:16'),
(8, 1, 'TUM-02', 'Contralmirante Villar', 1, '2026-09-21 20:59:16'),
(9, 1, 'TUM-03', 'Zarumilla', 1, '2026-09-21 20:59:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

CREATE TABLE `regiones` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(4) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`id`, `codigo`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'TUM', 'Tumbes', 1, '2026-09-21 13:58:19', '2026-09-21 13:58:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'ADMIN', 'Administrador del sistema', '2026-09-21 13:58:19'),
(2, 'JURADO', 'Jurado electoral', '2026-09-21 13:58:19'),
(3, 'DIGITADOR', 'Digitador de actas', '2026-09-21 13:58:19'),
(4, 'CONSULTA', 'Solo consulta de resultados', '2026-09-21 13:58:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_usuario`
--

CREATE TABLE `sesiones_usuario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `login_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `rol_id` int(10) UNSIGNED NOT NULL,
  `centro_id` int(10) UNSIGNED DEFAULT NULL,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `nombres`, `email`, `rol_id`, `centro_id`, `ultimo_acceso`, `activo`, `created_at`) VALUES
(1, 'admin1', '$2y$12$wfyueCGsclrxWVkNY/b.VOqfNM5CxviWibmsPXUs3yfXVW3NS0RzW', 'Administrador', NULL, 1, NULL, '2026-09-22 23:00:29', 1, '2026-09-21 13:58:19'),
(8, 'Prueba', '$2y$12$wfyueCGsclrxWVkNY/b.VOqfNM5CxviWibmsPXUs3yfXVW3NS0RzW', 'Francisco', 'panchogrupo@hotmail.com', 1, NULL, '2026-09-22 23:00:42', 1, '2026-09-21 22:37:57'),
(9, 'Francisco', '$2y$10$6RQAQnKfhFXM07IhlkejBu3hf4hp267H/6EzH7H3Q.AxSSTkZzudK', 'Francisco', 'admin@cpiset.gob.pe', 1, NULL, '2026-09-23 20:12:06', 1, '2026-09-22 22:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `votos`
--

CREATE TABLE `votos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mesa_id` int(10) UNSIGNED NOT NULL,
  `eleccion_id` int(10) UNSIGNED NOT NULL,
  `candidato_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_voto` enum('VALIDO','BLANCO','NULO') NOT NULL,
  `hora_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actas_sufragio`
--
ALTER TABLE `actas_sufragio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_acta` (`mesa_id`,`eleccion_id`),
  ADD KEY `eleccion_id` (`eleccion_id`);

--
-- Indices de la tabla `agrupaciones_politicas`
--
ALTER TABLE `agrupaciones_politicas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tabla_registro` (`tabla`,`registro_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `candidatos`
--
ALTER TABLE `candidatos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `agrupacion_id` (`agrupacion_id`),
  ADD KEY `cargo_id` (`cargo_id`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eleccion_id` (`eleccion_id`);

--
-- Indices de la tabla `centros_votacion`
--
ALTER TABLE `centros_votacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `distrito_id` (`distrito_id`);

--
-- Indices de la tabla `detalle_acta_candidato`
--
ALTER TABLE `detalle_acta_candidato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_acta_candidato` (`acta_id`,`candidato_id`),
  ADD KEY `candidato_id` (`candidato_id`);

--
-- Indices de la tabla `distritos`
--
ALTER TABLE `distritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `provincia_id` (`provincia_id`);

--
-- Indices de la tabla `elecciones`
--
ALTER TABLE `elecciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id` (`region_id`);

--
-- Indices de la tabla `electores`
--
ALTER TABLE `electores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `idx_mesa` (`mesa_id`);

--
-- Indices de la tabla `mesas_sufragio`
--
ALTER TABLE `mesas_sufragio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_mesa_centro` (`centro_id`,`numero_mesa`);

--
-- Indices de la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `region_id` (`region_id`);

--
-- Indices de la tabla `regiones`
--
ALTER TABLE `regiones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `sesiones_usuario`
--
ALTER TABLE `sesiones_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `centro_id` (`centro_id`);

--
-- Indices de la tabla `votos`
--
ALTER TABLE `votos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mesa_eleccion` (`mesa_id`,`eleccion_id`),
  ADD KEY `idx_candidato` (`candidato_id`),
  ADD KEY `eleccion_id` (`eleccion_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actas_sufragio`
--
ALTER TABLE `actas_sufragio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `agrupaciones_politicas`
--
ALTER TABLE `agrupaciones_politicas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `candidatos`
--
ALTER TABLE `candidatos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `centros_votacion`
--
ALTER TABLE `centros_votacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_acta_candidato`
--
ALTER TABLE `detalle_acta_candidato`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `distritos`
--
ALTER TABLE `distritos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `elecciones`
--
ALTER TABLE `elecciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `electores`
--
ALTER TABLE `electores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas_sufragio`
--
ALTER TABLE `mesas_sufragio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `provincias`
--
ALTER TABLE `provincias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `regiones`
--
ALTER TABLE `regiones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sesiones_usuario`
--
ALTER TABLE `sesiones_usuario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `votos`
--
ALTER TABLE `votos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actas_sufragio`
--
ALTER TABLE `actas_sufragio`
  ADD CONSTRAINT `actas_sufragio_ibfk_1` FOREIGN KEY (`mesa_id`) REFERENCES `mesas_sufragio` (`id`),
  ADD CONSTRAINT `actas_sufragio_ibfk_2` FOREIGN KEY (`eleccion_id`) REFERENCES `elecciones` (`id`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `candidatos`
--
ALTER TABLE `candidatos`
  ADD CONSTRAINT `candidatos_ibfk_1` FOREIGN KEY (`agrupacion_id`) REFERENCES `agrupaciones_politicas` (`id`),
  ADD CONSTRAINT `candidatos_ibfk_2` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`);

--
-- Filtros para la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD CONSTRAINT `cargos_ibfk_1` FOREIGN KEY (`eleccion_id`) REFERENCES `elecciones` (`id`);

--
-- Filtros para la tabla `centros_votacion`
--
ALTER TABLE `centros_votacion`
  ADD CONSTRAINT `centros_votacion_ibfk_1` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_acta_candidato`
--
ALTER TABLE `detalle_acta_candidato`
  ADD CONSTRAINT `detalle_acta_candidato_ibfk_1` FOREIGN KEY (`acta_id`) REFERENCES `actas_sufragio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_acta_candidato_ibfk_2` FOREIGN KEY (`candidato_id`) REFERENCES `candidatos` (`id`);

--
-- Filtros para la tabla `distritos`
--
ALTER TABLE `distritos`
  ADD CONSTRAINT `distritos_ibfk_1` FOREIGN KEY (`provincia_id`) REFERENCES `provincias` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `elecciones`
--
ALTER TABLE `elecciones`
  ADD CONSTRAINT `elecciones_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`);

--
-- Filtros para la tabla `electores`
--
ALTER TABLE `electores`
  ADD CONSTRAINT `electores_ibfk_1` FOREIGN KEY (`mesa_id`) REFERENCES `mesas_sufragio` (`id`);

--
-- Filtros para la tabla `mesas_sufragio`
--
ALTER TABLE `mesas_sufragio`
  ADD CONSTRAINT `mesas_sufragio_ibfk_1` FOREIGN KEY (`centro_id`) REFERENCES `centros_votacion` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD CONSTRAINT `provincias_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `sesiones_usuario`
--
ALTER TABLE `sesiones_usuario`
  ADD CONSTRAINT `sesiones_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`centro_id`) REFERENCES `centros_votacion` (`id`);

--
-- Filtros para la tabla `votos`
--
ALTER TABLE `votos`
  ADD CONSTRAINT `votos_ibfk_1` FOREIGN KEY (`mesa_id`) REFERENCES `mesas_sufragio` (`id`),
  ADD CONSTRAINT `votos_ibfk_2` FOREIGN KEY (`eleccion_id`) REFERENCES `elecciones` (`id`),
  ADD CONSTRAINT `votos_ibfk_3` FOREIGN KEY (`candidato_id`) REFERENCES `candidatos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `votos_ibfk_4` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
