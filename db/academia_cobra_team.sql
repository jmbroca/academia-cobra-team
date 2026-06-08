-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-06-2026 a las 04:38:43
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
-- Base de datos: `academia_cobra_team`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_registro` date DEFAULT curdate(),
  `estatus` enum('Activo','Inactivo') DEFAULT 'Activo',
  `rol` enum('estudiante','admin') DEFAULT 'estudiante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos_disciplinas`
--

CREATE TABLE `alumnos_disciplinas` (
  `id_registro` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `id_disciplina` int(11) NOT NULL,
  `id_cinturon` int(11) DEFAULT NULL,
  `porcentaje_progreso` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id_asistencia` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `estatus` enum('Presente','Falta') NOT NULL DEFAULT 'Presente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cinturones`
--

CREATE TABLE `cinturones` (
  `id_cinturon` int(11) NOT NULL,
  `id_disciplina` int(11) NOT NULL,
  `nombre_cinturon` varchar(50) NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cinturones`
--

INSERT INTO `cinturones` (`id_cinturon`, `id_disciplina`, `nombre_cinturon`, `orden`) VALUES
(1, 1, 'Cinta Blanca', 1),
(2, 1, 'Cinta Amarilla', 2),
(3, 1, 'Cinta Naranja', 3),
(4, 1, 'Cinta Verde', 4),
(5, 1, 'Cinta Azul', 5),
(6, 1, 'Cinta Marrón', 6),
(7, 1, 'Cinta Negra 1er Dan', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id_clase` int(11) NOT NULL,
  `id_disciplina` int(11) NOT NULL,
  `id_instructor` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencias_historial`
--

CREATE TABLE `competencias_historial` (
  `id_competencia` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `nombre_torneo` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `resultado` enum('1er lugar','2do lugar','3er lugar','Participación') NOT NULL,
  `fecha_competencia` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disciplinas`
--

CREATE TABLE `disciplinas` (
  `id_disciplina` int(11) NOT NULL,
  `nombre_disciplina` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `disciplinas`
--

INSERT INTO `disciplinas` (`id_disciplina`, `nombre_disciplina`) VALUES
(3, 'Boxeo'),
(4, 'Kickboxing'),
(1, 'Muay Thai'),
(2, 'Taekwondo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenes_historial`
--

CREATE TABLE `examenes_historial` (
  `id_examen` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `cinturon_obtenido` varchar(50) NOT NULL,
  `calificacion` int(11) NOT NULL,
  `estatus` enum('Aprobado','Reprobado') DEFAULT 'Aprobado',
  `fecha_examen` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructores`
--

CREATE TABLE `instructores` (
  `id_instructor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rango` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logros`
--

CREATE TABLE `logros` (
  `id_logro` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_obtencion` date NOT NULL,
  `tipo_icono` enum('trofeo','medalla','cinturon','estrella') DEFAULT 'medalla'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `estatus` enum('Pagado','Pendiente','Atrasado') DEFAULT 'Pendiente',
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia','N/A') DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `alumnos_disciplinas`
--
ALTER TABLE `alumnos_disciplinas`
  ADD PRIMARY KEY (`id_registro`),
  ADD UNIQUE KEY `id_alumno` (`id_alumno`,`id_disciplina`),
  ADD KEY `id_disciplina` (`id_disciplina`),
  ADD KEY `id_cinturon` (`id_cinturon`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `id_alumno` (`id_alumno`,`id_clase`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `cinturones`
--
ALTER TABLE `cinturones`
  ADD PRIMARY KEY (`id_cinturon`),
  ADD KEY `id_disciplina` (`id_disciplina`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `id_disciplina` (`id_disciplina`),
  ADD KEY `id_instructor` (`id_instructor`);

--
-- Indices de la tabla `competencias_historial`
--
ALTER TABLE `competencias_historial`
  ADD PRIMARY KEY (`id_competencia`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indices de la tabla `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`id_disciplina`),
  ADD UNIQUE KEY `nombre_disciplina` (`nombre_disciplina`);

--
-- Indices de la tabla `examenes_historial`
--
ALTER TABLE `examenes_historial`
  ADD PRIMARY KEY (`id_examen`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indices de la tabla `instructores`
--
ALTER TABLE `instructores`
  ADD PRIMARY KEY (`id_instructor`);

--
-- Indices de la tabla `logros`
--
ALTER TABLE `logros`
  ADD PRIMARY KEY (`id_logro`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `alumnos_disciplinas`
--
ALTER TABLE `alumnos_disciplinas`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cinturones`
--
ALTER TABLE `cinturones`
  MODIFY `id_cinturon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `competencias_historial`
--
ALTER TABLE `competencias_historial`
  MODIFY `id_competencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `id_disciplina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `examenes_historial`
--
ALTER TABLE `examenes_historial`
  MODIFY `id_examen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instructores`
--
ALTER TABLE `instructores`
  MODIFY `id_instructor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logros`
--
ALTER TABLE `logros`
  MODIFY `id_logro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos_disciplinas`
--
ALTER TABLE `alumnos_disciplinas`
  ADD CONSTRAINT `alumnos_disciplinas_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `alumnos_disciplinas_ibfk_2` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`id_disciplina`) ON DELETE CASCADE,
  ADD CONSTRAINT `alumnos_disciplinas_ibfk_3` FOREIGN KEY (`id_cinturon`) REFERENCES `cinturones` (`id_cinturon`);

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`id_clase`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cinturones`
--
ALTER TABLE `cinturones`
  ADD CONSTRAINT `cinturones_ibfk_1` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`id_disciplina`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `clases_ibfk_1` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`id_disciplina`) ON DELETE CASCADE,
  ADD CONSTRAINT `clases_ibfk_2` FOREIGN KEY (`id_instructor`) REFERENCES `instructores` (`id_instructor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `competencias_historial`
--
ALTER TABLE `competencias_historial`
  ADD CONSTRAINT `competencias_historial_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `examenes_historial`
--
ALTER TABLE `examenes_historial`
  ADD CONSTRAINT `examenes_historial_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `logros`
--
ALTER TABLE `logros`
  ADD CONSTRAINT `logros_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
