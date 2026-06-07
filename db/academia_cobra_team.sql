-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-06-2026 a las 22:44:55
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
  `cinturon_actual` varchar(50) DEFAULT 'Cinta Blanca',
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
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id_clase` int(11) NOT NULL,
  `id_disciplina` int(11) NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `fecha_hora` datetime NOT NULL
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
  ADD KEY `id_disciplina` (`id_disciplina`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `id_alumno` (`id_alumno`,`id_clase`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `id_disciplina` (`id_disciplina`);

--
-- Indices de la tabla `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`id_disciplina`),
  ADD UNIQUE KEY `nombre_disciplina` (`nombre_disciplina`);

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
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `id_disciplina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `alumnos_disciplinas_ibfk_2` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`id_disciplina`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`id_clase`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `clases_ibfk_1` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`id_disciplina`) ON DELETE CASCADE;

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
