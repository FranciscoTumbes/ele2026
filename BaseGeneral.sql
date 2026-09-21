-- =====================================================
-- SISTEMA ELECTORAL - REGIÓN TUMBES
-- Motor: MySQL 8.x / MariaDB 10.x (XAMPP)
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-05:00"; -- Hora Perú

CREATE DATABASE IF NOT EXISTS `cpiset`
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cpiset`;

-- =====================================================
-- 1. TABLAS MAESTRAS DE UBICACIÓN GEOGRÁFICA
-- =====================================================

CREATE TABLE `regiones` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(4) NOT NULL UNIQUE,
  `nombre` VARCHAR(100) NOT NULL,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `provincias` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `region_id` INT UNSIGNED NOT NULL,
  `codigo` VARCHAR(6) NOT NULL UNIQUE,
  `nombre` VARCHAR(100) NOT NULL,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`region_id`) REFERENCES `regiones`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `distritos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `provincia_id` INT UNSIGNED NOT NULL,
  `codigo` VARCHAR(8) NOT NULL UNIQUE,
  `nombre` VARCHAR(100) NOT NULL,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`provincia_id`) REFERENCES `provincias`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `centros_votacion` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `distrito_id` INT UNSIGNED NOT NULL,
  `codigo` VARCHAR(12) NOT NULL UNIQUE,
  `nombre` VARCHAR(150) NOT NULL,
  `direccion` VARCHAR(255),
  `tipo_local` ENUM('LOCAL','ESCUELA','COLISEO','OTRO') DEFAULT 'LOCAL',
  `latitud` DECIMAL(10,8),
  `longitud` DECIMAL(11,8),
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`distrito_id`) REFERENCES `distritos`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `mesas_sufragio` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `centro_id` INT UNSIGNED NOT NULL,
  `numero_mesa` VARCHAR(10) NOT NULL,
  `electores_habilitados` INT UNSIGNED DEFAULT 0,
  `estado` ENUM('ACTIVA','CERRADA','ANULADA') DEFAULT 'ACTIVA',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_mesa_centro` (`centro_id`, `numero_mesa`),
  FOREIGN KEY (`centro_id`) REFERENCES `centros_votacion`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 2. ELECCIONES Y CARGOS
-- =====================================================

CREATE TABLE `elecciones` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `region_id` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(200) NOT NULL,
  `tipo` ENUM('MUNICIPAL','REGIONAL') NOT NULL,
  `fecha_eleccion` DATE NOT NULL,
  `estado` ENUM('CONVOCADA','EN_CURSO','FINALIZADA','ANULADA') DEFAULT 'CONVOCADA',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`region_id`) REFERENCES `regiones`(`id`)
) ENGINE=InnoDB;

CREATE TABLE `cargos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `eleccion_id` INT UNSIGNED NOT NULL,
  `ambito` ENUM('REGIONAL','PROVINCIAL','DISTRITAL') NOT NULL,
  `nombre_cargo` VARCHAR(100) NOT NULL, -- Gobernador, Alcalde, Regidor, Consejero
  `titular` TINYINT(1) DEFAULT 1,       -- 1=titular, 0=accesitario
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`eleccion_id`) REFERENCES `elecciones`(`id`)
) ENGINE=InnoDB;

-- =====================================================
-- 3. AGRUPACIONES POLÍTICAS Y CANDIDATOS
-- =====================================================

CREATE TABLE `agrupaciones_politicas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(10) NOT NULL UNIQUE,
  `nombre` VARCHAR(200) NOT NULL,
  `siglas` VARCHAR(20) NOT NULL,
  `logo_url` VARCHAR(255),
  `color_hex` VARCHAR(7) DEFAULT '#000000',
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `candidatos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `agrupacion_id` INT UNSIGNED NOT NULL,
  `cargo_id` INT UNSIGNED NOT NULL,
  `ambito_id` INT UNSIGNED DEFAULT NULL, -- provincia o distrito según cargo
  `tipo_ambito` ENUM('PROVINCIA','DISTRITO','REGION') DEFAULT NULL,
  `dni` VARCHAR(8) NOT NULL UNIQUE,
  `nombres` VARCHAR(150) NOT NULL,
  `apellido_paterno` VARCHAR(100) NOT NULL,
  `apellido_materno` VARCHAR(100) NOT NULL,
  `foto_url` VARCHAR(255),
  `posicion_lista` INT UNSIGNED DEFAULT 1,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`agrupacion_id`) REFERENCES `agrupaciones_politicas`(`id`),
  FOREIGN KEY (`cargo_id`) REFERENCES `cargos`(`id`)
) ENGINE=InnoDB;

-- =====================================================
-- 4. ELECTORES (PADRÓN)
-- =====================================================

CREATE TABLE `electores` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `dni` VARCHAR(8) NOT NULL UNIQUE,
  `nombres` VARCHAR(150) NOT NULL,
  `apellido_paterno` VARCHAR(100) NOT NULL,
  `apellido_materno` VARCHAR(100) NOT NULL,
  `fecha_nacimiento` DATE,
  `sexo` ENUM('M','F') DEFAULT NULL,
  `direccion` VARCHAR(255),
  `mesa_id` INT UNSIGNED NOT NULL,
  `estado` ENUM('HABILITADO','TAQUILLADO','FALLECIDO','INHABILITADO') DEFAULT 'HABILITADO',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_mesa` (`mesa_id`),
  FOREIGN KEY (`mesa_id`) REFERENCES `mesas_sufragio`(`id`)
) ENGINE=InnoDB;



-- =====================================================
-- 6. ACTAS Y TOTALIZACIÓN
-- =====================================================

CREATE TABLE `actas_sufragio` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `mesa_id` INT UNSIGNED NOT NULL,
  `eleccion_id` INT UNSIGNED NOT NULL,
  `electores_habilitados` INT UNSIGNED DEFAULT 0,
  `votos_validos` INT UNSIGNED DEFAULT 0,
  `votos_blancos` INT UNSIGNED DEFAULT 0,
  `votos_nulos` INT UNSIGNED DEFAULT 0,
  `votos_impugnados` INT UNSIGNED DEFAULT 0,
  `total_votantes` INT UNSIGNED DEFAULT 0,
  `observaciones` TEXT,
  `estado` ENUM('PENDIENTE','DIGITADA','VERIFICADA','CONSOLIDADA') DEFAULT 'PENDIENTE',
  `digitado_por` INT UNSIGNED,
  `verificado_por` INT UNSIGNED,
  `fecha_digitacion` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_acta` (`mesa_id`, `eleccion_id`),
  FOREIGN KEY (`mesa_id`) REFERENCES `mesas_sufragio`(`id`),
  FOREIGN KEY (`eleccion_id`) REFERENCES `elecciones`(`id`)
) ENGINE=InnoDB;

CREATE TABLE `detalle_acta_candidato` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `acta_id` INT UNSIGNED NOT NULL,
  `candidato_id` INT UNSIGNED NOT NULL,
  `votos_obtenidos` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_acta_candidato` (`acta_id`, `candidato_id`),
  FOREIGN KEY (`acta_id`) REFERENCES `actas_sufragio`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`candidato_id`) REFERENCES `candidatos`(`id`)
) ENGINE=InnoDB;

-- =====================================================
-- 7. USUARIOS DEL SISTEMA Y SEGURIDAD
-- =====================================================

CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `nombres` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) UNIQUE,
  `rol_id` INT UNSIGNED NOT NULL,
  `centro_id` INT UNSIGNED DEFAULT NULL, -- si es digitador de centro
  `ultimo_acceso` TIMESTAMP NULL,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`),
  FOREIGN KEY (`centro_id`) REFERENCES `centros_votacion`(`id`)
) ENGINE=InnoDB;

CREATE TABLE `sesiones_usuario` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `ip` VARCHAR(45),
  `user_agent` VARCHAR(500),
  `login_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `logout_at` TIMESTAMP NULL,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB;

-- =====================================================
-- 8. AUDITORÍA
-- =====================================================

CREATE TABLE `auditoria` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED,
  `tabla` VARCHAR(50) NOT NULL,
  `registro_id` VARCHAR(50) NOT NULL,
  `accion` ENUM('INSERT','UPDATE','DELETE','LOGIN','LOGOUT') NOT NULL,
  `datos_anteriores` JSON,
  `datos_nuevos` JSON,
  `ip` VARCHAR(45),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_tabla_registro` (`tabla`, `registro_id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB;

-- =====================================================
-- 5. REGISTRO DE VOTOS (TRANSACCIÓN)
-- =====================================================

CREATE TABLE `votos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `mesa_id` INT UNSIGNED NOT NULL,
  `eleccion_id` INT UNSIGNED NOT NULL,
  `candidato_id` INT UNSIGNED DEFAULT NULL, -- NULL si es blanco/nulo
  `tipo_voto` ENUM('VALIDO','BLANCO','NULO') NOT NULL,
  `hora_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` INT UNSIGNED,                -- digitador que registró
  INDEX `idx_mesa_eleccion` (`mesa_id`, `eleccion_id`),
  INDEX `idx_candidato` (`candidato_id`),
  FOREIGN KEY (`mesa_id`) REFERENCES `mesas_sufragio`(`id`),
  FOREIGN KEY (`eleccion_id`) REFERENCES `elecciones`(`id`),
  FOREIGN KEY (`candidato_id`) REFERENCES `candidatos`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB;

-- =====================================================
-- 9. DATOS INICIALES (SEED)
-- =====================================================

INSERT INTO `regiones` (`codigo`, `nombre`) VALUES ('TUM', 'Tumbes');



INSERT INTO `roles` (`nombre`, `descripcion`) VALUES
('ADMIN', 'Administrador del sistema'),
('JURADO', 'Jurado electoral'),
('DIGITADOR', 'Digitador de actas'),
('CONSULTA', 'Solo consulta de resultados');

-- Usuario admin por defecto (password: admin123 - CAMBIAR EN PRODUCCIÓN)
INSERT INTO `usuarios` (`username`, `password_hash`, `nombres`, `rol_id`) VALUES
('admin', '$2y$12$8hJtZOW.C80eXhHDPFGcreZZqIVqIl98GhDltHTn2.C8K.hSMGIfS', 'Administrador', 1);

INSERT INTO `provincias` (`region_id`, `codigo`, `nombre`) VALUES
(1, 'TUM-01', 'Tumbes'),
(1, 'TUM-02', 'Contralmirante Villar'),
(1, 'TUM-03', 'Zarumilla');

INSERT INTO `distritos` (`provincia_id`, `codigo`, `nombre`) VALUES
-- Tumbes
(7, 'TUM-01-01', 'Tumbes'),
(7, 'TUM-01-02', 'Corrales'),
(7, 'TUM-01-03', 'San Jacinto'),
(7, 'TUM-01-04', 'San Juan de la Virgen'),
(7, 'TUM-01-05', 'Pampas de Hospital'),
(7, 'TUM-01-06', 'La Cruz'),
-- Contralmirante Villar
(8, 'TUM-02-01', 'Zorritos'),
(8, 'TUM-02-02', 'Casitas'),
(8, 'TUM-02-03', 'Canoas de Punta Sal'),
-- Zarumilla
(9, 'TUM-03-01', 'Zarumilla'),
(9, 'TUM-03-02', 'Aguas Verdes'),
(9, 'TUM-03-03', 'Matapalo'),
(9, 'TUM-03-04', 'Papayal');

INSERT INTO `agrupaciones_politicas` (`codigo`, `nombre`, `siglas`, `color_hex`) VALUES
('AP01', 'Alianza para el Progreso', 'APP', '#0066CC'),
('AP02', 'Fe en el Peru', 'FPT', '#1bf174'),
('AP03', 'Acción Popular', 'ACT', '#db4509'),
('AP04', 'Fuerza Popular', 'FPT', '#d6bb98'),
('AP05', 'Somos Perú', 'SPT', '#660099'),
('AP06', 'Movimiento Pueblo Unido', 'MPU', '#006666'),
('AP07', 'Renovación Tumbesina', 'RT', '#CC3399'),
('AP08', 'Acción Vecinal', 'AV', '#996633'),
('AP09', 'Movimiento Regional Cambio Tumbesino', 'MCT', '#336699'),
('AP10', 'Participación Ciudadana Tumbes', 'PCT', '#990000');