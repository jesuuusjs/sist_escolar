-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-05-2025 a las 21:36:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `escolares`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `numCuenta_alumno` varchar(9) NOT NULL,
  `nombre_alumno` varchar(100) DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `domicilio` varchar(150) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `promedio` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`numCuenta_alumno`, `nombre_alumno`, `genero`, `fecha_nacimiento`, `domicilio`, `telefono`, `correo`, `promedio`) VALUES
('202410001', 'Juan Perez Ramirez', NULL, NULL, NULL, NULL, 'juaniris8748@gmail.com', 7.5),
('424094371', 'Jesus Santiago Arias', 'Masculino', '2005-01-04', 'Paseo Del Oro, Joyas de Cuautitlán, CP 54803 , Mexico', '5573830651', 'jesusiris8748@gmail.mx', 9.1),
('424569871', 'Sarahi Santiago Arias', NULL, NULL, NULL, NULL, 'sarahi129@unam.mx', 9.5),
('456742569', 'Jesus Santiago Gomez', NULL, NULL, NULL, NULL, 'jesusi88is8748@gmail.com', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_asignaturas`
--

CREATE TABLE `alumno_asignaturas` (
  `id_historial` int(11) NOT NULL,
  `numCuenta_alumno` varchar(9) NOT NULL,
  `clave_asig` varchar(20) NOT NULL,
  `grupo` varchar(20) NOT NULL,
  `fecha_inscripcion` date NOT NULL,
  `aprobada` tinyint(1) DEFAULT 0,
  `calificacion` decimal(3,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_asignaturas`
--

INSERT INTO `alumno_asignaturas` (`id_historial`, `numCuenta_alumno`, `clave_asig`, `grupo`, `fecha_inscripcion`, `aprobada`, `calificacion`) VALUES
(4, '424094371', '100', '1101', '0000-00-00', 0, NULL),
(5, '424094371', '101', '1101', '0000-00-00', 0, NULL),
(6, '424094371', '106', '1101', '0000-00-00', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_licenciatura`
--

CREATE TABLE `alumno_licenciatura` (
  `numCuenta_alumno` varchar(9) NOT NULL,
  `clave_lic` varchar(20) NOT NULL,
  `fecha_inscripcion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignatura`
--

CREATE TABLE `asignatura` (
  `clave_asig` varchar(20) NOT NULL,
  `nombre_asignatura` varchar(100) NOT NULL,
  `creditos_asig` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `clave_lic` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignatura`
--

INSERT INTO `asignatura` (`clave_asig`, `nombre_asignatura`, `creditos_asig`, `semestre`, `clave_lic`) VALUES
('100', 'ADMINISTRACION I', 6, 1, '308'),
('101', 'COMUNICACION ORAL Y ESCRITA', 6, 1, '308'),
('102', 'INFORMATICA I', 12, 1, '308'),
('104', 'TALLER DE COMPONENTES DE HARDWARE', 3, 1, '308'),
('105', 'ANALISIS Y DISEÑO DE ALGORITMOS', 8, 1, '308'),
('106', 'MATEMATICAS I', 8, 1, '308'),
('107', 'PROGRAMACION I', 8, 1, '308');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula`
--

CREATE TABLE `aula` (
  `id_aula` int(11) NOT NULL,
  `nombre_aula` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aula`
--

INSERT INTO `aula` (`id_aula`, `nombre_aula`) VALUES
(1, '214'),
(2, '1104'),
(3, '212'),
(4, '211');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dosificacion`
--

CREATE TABLE `dosificacion` (
  `id_dosificacion` int(11) NOT NULL,
  `clave_lic` varchar(20) NOT NULL,
  `numCuenta_alumno` varchar(9) NOT NULL,
  `nombre_alumno` varchar(100) NOT NULL,
  `turno` varchar(20) NOT NULL,
  `fecha_atcion` date NOT NULL,
  `hora_atcion` time NOT NULL,
  `num_atcion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dosificacion`
--

INSERT INTO `dosificacion` (`id_dosificacion`, `clave_lic`, `numCuenta_alumno`, `nombre_alumno`, `turno`, `fecha_atcion`, `hora_atcion`, `num_atcion`) VALUES
(1746419985, '308', '456742569', 'Jesus Santiago Gomez', 'Matutino', '2025-05-06', '23:39:00', 3),
(1746491407, '308', '424094371', 'Jesus Santiago Arias', 'Matutino', '2025-05-09', '20:09:00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE `grupo` (
  `num_grupo` varchar(20) NOT NULL,
  `cupo_maximo` int(11) NOT NULL,
  `inscritos` int(11) NOT NULL DEFAULT 0,
  `clave_lic` varchar(20) NOT NULL,
  `clave_asig` varchar(20) NOT NULL,
  `id_aula` int(11) NOT NULL,
  `clave_prof` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo`
--

INSERT INTO `grupo` (`num_grupo`, `cupo_maximo`, `inscritos`, `clave_lic`, `clave_asig`, `id_aula`, `clave_prof`) VALUES
('1101', 20, 1, '308', '100', 1, '0001'),
('1101', 21, 1, '308', '101', 1, '0002'),
('1101', 22, 0, '308', '102', 1, '0003'),
('1101', 21, 0, '308', '104', 1, '0006'),
('1101', 20, 0, '308', '105', 1, '0004'),
('1101', 22, 1, '308', '106', 1, '0005'),
('1101', 21, 0, '308', '107', 1, 'P002');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `num_grupo` varchar(20) NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado') DEFAULT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id_horario`, `num_grupo`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
(1, '1101', 'Lunes', '08:00:00', '10:00:00'),
(2, '1101', 'Lunes', '10:00:00', '11:30:00'),
(3, '1101', 'Lunes', '11:30:00', '13:30:00'),
(4, '1101', 'Martes', '08:00:00', '10:00:00'),
(5, '1101', 'Martes', '10:00:00', '11:30:00'),
(6, '1101', 'Martes', '11:30:00', '13:00:00'),
(7, '1101', 'Miercoles', '07:30:00', '09:00:00'),
(8, '1101', 'Miercoles', '10:30:00', '12:00:00'),
(9, '1101', 'Miercoles', '12:00:00', '14:00:00'),
(10, '1101', 'Jueves', '08:00:00', '10:00:00'),
(11, '1101', 'Jueves', '10:00:00', '11:30:00'),
(12, '1101', 'Jueves', '11:30:00', '13:00:00'),
(13, '1101', 'Viernes', '07:30:00', '09:00:00'),
(14, '1101', 'Viernes', '09:00:00', '10:30:00'),
(15, '1101', 'Viernes', '10:30:00', '12:30:00'),
(16, '1101', 'Viernes', '12:30:00', '14:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licenciatura`
--

CREATE TABLE `licenciatura` (
  `clave_lic` varchar(20) NOT NULL,
  `nombre_lic` varchar(100) NOT NULL,
  `semestres_tot` int(11) NOT NULL,
  `creditos_totales` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `licenciatura`
--

INSERT INTO `licenciatura` (`clave_lic`, `nombre_lic`, `semestres_tot`, `creditos_totales`) VALUES
('308', 'Informatica', 9, 450);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `clave_prof` varchar(20) NOT NULL,
  `nombre_prof` varchar(100) NOT NULL,
  `correo_prof` varchar(100) NOT NULL,
  `telefono_prof` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`clave_prof`, `nombre_prof`, `correo_prof`, `telefono_prof`) VALUES
('0001', 'JULIO CESAR GUTIERREZ PELAEZ', '', NULL),
('0002', 'DULCE MA. LIGIA MALO ORTEGA', '', NULL),
('0003', 'LIANA LOPEZ PACHECO', '', NULL),
('0004', 'GILTA PIXAN DALAI CASCO URRUTIA', '', NULL),
('0005', 'JOSE ANTONIO HERNANDEZ SORIANO', '', NULL),
('0006', 'OSCAR HERNANDEZ SANCHEZ', '', NULL),
('P001', 'Valentin Roldan Vazquez', '', NULL),
('P002', 'Mauricio Jaques Soto', '', NULL),
('P003', 'Juan Silva', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `numcuenta` varchar(9) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('alumno','administrador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `numcuenta`, `password`, `rol`) VALUES
(1, '423657891', '$2y$10$3/gDGhhtdO07gBoPxDtvcO8ZmpkDv/4mfpMM.tlxL1OIzAutowze6', 'administrador'),
(2, '424094371', '$2y$10$5fUrjdTbiNumO6CUrMnUZOhe3lmB9W/1gRr7FA6HLFrx/qH1gOR/.', 'alumno'),
(8, '654797979', '$2y$10$gUFJwqncdu7PqRYu4/4RBuz0cVwbE3fLtDWYsLCr7jKkCMRbZZ9cy', 'administrador'),
(9, '424569871', '$2y$10$pr0bCnmepVWsVB6CAt2ZjOsFGHgguOLRQBSuzslT0/IDhADT3Ixxu', 'alumno'),
(10, '202410001', '$2y$10$.Uqhs1bjecoGyAFy6yINFOS0CCy.BfHVsnKIGeQuHIFTqdLzmfwI6', 'alumno'),
(11, '456742569', '$2y$10$Tvek0bOf/XVOZsg9ev1.9OUF6TIC2goa0PSUkDzlZVOmmzFu2hd9y', 'alumno');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_grupos_completos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_grupos_completos` (
`num_grupo` varchar(20)
,`cupo_maximo` int(11)
,`inscritos` int(11)
,`clave_lic` varchar(20)
,`clave_asig` varchar(20)
,`id_aula` int(11)
,`clave_prof` varchar(20)
,`nombre_asignatura` varchar(100)
,`nombre_prof` varchar(100)
,`nombre_aula` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_grupos_completos`
--
DROP TABLE IF EXISTS `v_grupos_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_grupos_completos`  AS SELECT `g`.`num_grupo` AS `num_grupo`, `g`.`cupo_maximo` AS `cupo_maximo`, `g`.`inscritos` AS `inscritos`, `g`.`clave_lic` AS `clave_lic`, `g`.`clave_asig` AS `clave_asig`, `g`.`id_aula` AS `id_aula`, `g`.`clave_prof` AS `clave_prof`, `a`.`nombre_asignatura` AS `nombre_asignatura`, `p`.`nombre_prof` AS `nombre_prof`, `au`.`nombre_aula` AS `nombre_aula` FROM (((`grupo` `g` join `asignatura` `a` on(`g`.`clave_asig` = `a`.`clave_asig`)) join `profesor` `p` on(`g`.`clave_prof` = `p`.`clave_prof`)) join `aula` `au` on(`g`.`id_aula` = `au`.`id_aula`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`numCuenta_alumno`);

--
-- Indices de la tabla `alumno_asignaturas`
--
ALTER TABLE `alumno_asignaturas`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `numCuenta_alumno` (`numCuenta_alumno`),
  ADD KEY `clave_asig` (`clave_asig`);

--
-- Indices de la tabla `alumno_licenciatura`
--
ALTER TABLE `alumno_licenciatura`
  ADD PRIMARY KEY (`numCuenta_alumno`,`clave_lic`),
  ADD KEY `clave_lic` (`clave_lic`);

--
-- Indices de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD PRIMARY KEY (`clave_asig`),
  ADD KEY `clave_lic` (`clave_lic`);

--
-- Indices de la tabla `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`id_aula`);

--
-- Indices de la tabla `dosificacion`
--
ALTER TABLE `dosificacion`
  ADD PRIMARY KEY (`id_dosificacion`),
  ADD KEY `numCuenta_alumno` (`numCuenta_alumno`),
  ADD KEY `fk_clave_lic` (`clave_lic`);

--
-- Indices de la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`num_grupo`,`clave_asig`),
  ADD KEY `fk_grupo_asignatura` (`clave_asig`),
  ADD KEY `fk_grupo_licenciatura` (`clave_lic`),
  ADD KEY `fk_grupo_aula` (`id_aula`),
  ADD KEY `fk_grupo_profesor` (`clave_prof`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `num_grupo` (`num_grupo`);

--
-- Indices de la tabla `licenciatura`
--
ALTER TABLE `licenciatura`
  ADD PRIMARY KEY (`clave_lic`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`clave_prof`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numcuenta` (`numcuenta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumno_asignaturas`
--
ALTER TABLE `alumno_asignaturas`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `aula`
--
ALTER TABLE `aula`
  MODIFY `id_aula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`numCuenta_alumno`) REFERENCES `usuarios` (`numcuenta`),
  ADD CONSTRAINT `fk_alumno_usuario` FOREIGN KEY (`numCuenta_alumno`) REFERENCES `usuarios` (`numcuenta`);

--
-- Filtros para la tabla `alumno_asignaturas`
--
ALTER TABLE `alumno_asignaturas`
  ADD CONSTRAINT `alumno_asignaturas_ibfk_1` FOREIGN KEY (`numCuenta_alumno`) REFERENCES `alumno` (`numCuenta_alumno`),
  ADD CONSTRAINT `alumno_asignaturas_ibfk_2` FOREIGN KEY (`clave_asig`) REFERENCES `asignatura` (`clave_asig`);

--
-- Filtros para la tabla `alumno_licenciatura`
--
ALTER TABLE `alumno_licenciatura`
  ADD CONSTRAINT `alumno_licenciatura_ibfk_1` FOREIGN KEY (`numCuenta_alumno`) REFERENCES `alumno` (`numCuenta_alumno`),
  ADD CONSTRAINT `alumno_licenciatura_ibfk_2` FOREIGN KEY (`clave_lic`) REFERENCES `licenciatura` (`clave_lic`);

--
-- Filtros para la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD CONSTRAINT `asignatura_ibfk_1` FOREIGN KEY (`clave_lic`) REFERENCES `licenciatura` (`clave_lic`);

--
-- Filtros para la tabla `dosificacion`
--
ALTER TABLE `dosificacion`
  ADD CONSTRAINT `dosificacion_ibfk_2` FOREIGN KEY (`numCuenta_alumno`) REFERENCES `alumno` (`numCuenta_alumno`),
  ADD CONSTRAINT `fk_clave_lic` FOREIGN KEY (`clave_lic`) REFERENCES `licenciatura` (`clave_lic`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD CONSTRAINT `fk_grupo_asignatura` FOREIGN KEY (`clave_asig`) REFERENCES `asignatura` (`clave_asig`),
  ADD CONSTRAINT `fk_grupo_aula` FOREIGN KEY (`id_aula`) REFERENCES `aula` (`id_aula`),
  ADD CONSTRAINT `fk_grupo_licenciatura` FOREIGN KEY (`clave_lic`) REFERENCES `licenciatura` (`clave_lic`),
  ADD CONSTRAINT `fk_grupo_profesor` FOREIGN KEY (`clave_prof`) REFERENCES `profesor` (`clave_prof`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`num_grupo`) REFERENCES `grupo` (`num_grupo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
