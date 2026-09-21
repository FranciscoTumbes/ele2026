-- =====================================================
-- SEED DE DATOS DE PRUEBA - SISTEMA ELECTORAL TUMBES
-- Ejecutar DESPUÉS de BaseGeneral.sql
-- =====================================================

USE `cpiset`;

-- 1. ELECCIÓN PRESIDENCIAL
INSERT INTO `elecciones` (`region_id`, `nombre`, `tipo`, `fecha_eleccion`, `estado`) VALUES
(1, 'Elecciones Regionales y Municipales 2026 - Tumbes', 'REGIONAL', '2026-10-06', 'EN_CURSO');

-- 2. CARGOS
INSERT INTO `cargos` (`eleccion_id`, `ambito`, `nombre_cargo`, `titular`) VALUES
(1, 'REGIONAL', 'Gobernador Regional', 1),
(1, 'REGIONAL', 'Vicegobernador Regional', 1),
(1, 'REGIONAL', 'Consejero Regional', 1),
(1, 'PROVINCIAL', 'Alcalde Provincial', 1),
(1, 'PROVINCIAL', 'Regidor Provincial', 1),
(1, 'DISTRITAL', 'Alcalde Distrital', 1),
(1, 'DISTRITAL', 'Regidor Distrital', 1);

-- 3. CENTROS DE VOTACIÓN (Distrito Tumbes, provincia Tumbes)
INSERT INTO `centros_votacion` (`distrito_id`, `codigo`, `nombre`, `direccion`, `tipo_local`) VALUES
(1, 'CV-TUM-001', 'I.E. 011 Cesar Vallejo', 'Av. Tumbes Norte 234', 'ESCUELA'),
(1, 'CV-TUM-002', 'I.E. 006 Mercedes Matilde Avalos', 'Jr. Bolivar 567', 'ESCUELA'),
(2, 'CV-COR-001', 'I.E. 096 Crl. José Andres Rázuri', 'Av. Corrales 100', 'ESCUELA');

-- 4. MESAS DE SUFRAGIO
INSERT INTO `mesas_sufragio` (`centro_id`, `numero_mesa`, `electores_habilitados`, `estado`) VALUES
-- Centro 1: I.E. 011 Cesar Vallejo
(1, '000100', 250, 'ACTIVA'),
(1, '000101', 280, 'ACTIVA'),
(1, '000102', 260, 'ACTIVA'),
(1, '000103', 300, 'ACTIVA'),
(1, '000104', 275, 'ACTIVA'),
-- Centro 2: I.E. 006 Mercedes Matilde Avalos
(2, '000200', 310, 'ACTIVA'),
(2, '000201', 290, 'ACTIVA'),
(2, '000202', 265, 'ACTIVA'),
-- Centro 3: I.E. 096 José Andres Rázuri (Corrales)
(3, '000300', 320, 'ACTIVA'),
(3, '000301', 295, 'ACTIVA');

-- 5. CANDIDATOS A GOBERNADOR REGIONAL (cargo_id = 1)
INSERT INTO `candidatos` (`agrupacion_id`, `cargo_id`, `ambito_id`, `tipo_ambito`, `dni`, `nombres`, `apellido_paterno`, `apellido_materno`, `posicion_lista`) VALUES
(1, 1, 1, 'REGION', '00000001', 'Luis Alberto',   'Garcia',    'Mendoza',  1),
(2, 1, 1, 'REGION', '00000002', 'Maria Elena',     'Torres',    'Vasquez',  1),
(3, 1, 1, 'REGION', '00000003', 'Carlos Eduardo',  'Ramirez',   'Soto',     1),
(4, 1, 1, 'REGION', '00000004', 'Ana Patricia',    'Flores',    'Herrera',  1),
(5, 1, 1, 'REGION', '00000005', 'Jorge Antonio',   'Navarro',   'Cruz',     1),
(6, 1, 1, 'REGION', '00000006', 'Rosa Maria',      'Chinchay',  'Pardo',    1),
(7, 1, 1, 'REGION', '00000007', 'Pedro Miguel',    'Zapata',    'Olaya',    1),
(8, 1, 1, 'REGION', '00000008', 'Carmen Lucia',    'Vilchez',   'Adrianzen',1),
(9, 1, 1, 'REGION', '00000009', 'Fernando Jose',   'Juarez',    'Marchena', 1),
(10,1, 1, 'REGION', '00000010', 'Gladys Isabel',   'Palacios',  'Morales',  1);

-- 6. USUARIO DIGITADOR (password: digi123)
INSERT INTO `usuarios` (`username`, `password_hash`, `nombres`, `rol_id`, `centro_id`) VALUES
('digitador1', '$2y$12$0zghkEvwX0qU2aKEYJ.DbuD6DRFKYGMv85gpjxCcliQV7xfs.Qcq6', 'Juan Operador', 3, 1);
