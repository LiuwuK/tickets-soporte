-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-01-2025 a las 22:01:32
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
-- Base de datos: `test`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fecha` date DEFAULT NULL,
  `proyecto_id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `nombre`, `fecha`, `proyecto_id`, `descripcion`) VALUES
(25, 'Actividad 1', '2025-01-09', 13, 'asd'),
(26, 'actividad 2', '2025-01-26', 13, 'asd'),
(27, 'actividad 1', '2025-01-04', 14, 'tst'),
(28, 'Actividad 1', '2025-01-16', 15, 'asdasd');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `nombre`) VALUES
(1, 'Ingeniero'),
(2, 'Comercial'),
(3, 'Gerencia/Finanzas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `nombre_ciudad` varchar(100) NOT NULL,
  `region_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id`, `nombre_ciudad`, `region_id`) VALUES
(1, 'Arica', 15),
(2, 'Iquique', 1),
(3, 'Antofagasta', 2),
(4, 'Copiapó', 3),
(5, 'La Serena', 4),
(6, 'Valparaíso', 5),
(7, 'Santiago', 13),
(8, 'Rancagua', 6),
(9, 'Talca', 7),
(10, 'Chillán', 16),
(11, 'Concepción', 8),
(12, 'Temuco', 9),
(13, 'Valdivia', 14),
(14, 'Puerto Montt', 10),
(15, 'Coyhaique', 11),
(16, 'Punta Arenas', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion_proyecto`
--

CREATE TABLE `clasificacion_proyecto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clasificacion_proyecto`
--

INSERT INTO `clasificacion_proyecto` (`id`, `nombre`) VALUES
(1, 'Tecnología'),
(2, 'Guardias');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos_proyecto`
--

CREATE TABLE `contactos_proyecto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `numero` varchar(12) NOT NULL,
  `proyecto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos_proyecto`
--

INSERT INTO `contactos_proyecto` (`id`, `nombre`, `correo`, `cargo`, `numero`, `proyecto_id`) VALUES
(4, 'Juan ', 'juan@gmail.com', 'ingeniero', '56949291218', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `type` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id`, `nombre`, `type`) VALUES
(7, 'Pendiente', 'task'),
(8, 'En proceso', 'task'),
(9, 'Completada', 'task'),
(10, 'En revisión', 'ticket'),
(11, 'Abierto', 'ticket'),
(12, 'Cerrado', 'ticket'),
(13, 'Primer visita o contacto con Cliente', 'project'),
(14, 'Verificando si existe Proyecto', 'project'),
(15, 'Calificando la oportunidad', 'project'),
(16, 'Desarrollando la Solución (Config)', 'project'),
(17, 'Cotizado al Cliente', 'project'),
(18, 'Negociando y Cerrando el Proyecto', 'project');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licitacion_proyecto`
--

CREATE TABLE `licitacion_proyecto` (
  `id` int(11) NOT NULL,
  `licitacion_id` varchar(255) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `portal` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `licitacion_proyecto`
--

INSERT INTO `licitacion_proyecto` (`id`, `licitacion_id`, `proyecto_id`, `portal`) VALUES
(3, '17413034134', 13, 'test2'),
(4, '123', 14, 'portal test');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `creada_en` datetime DEFAULT current_timestamp(),
  `mensaje` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `ticket_id` int(11) NOT NULL,
  `admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prioridades`
--

CREATE TABLE `prioridades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prioridades`
--

INSERT INTO `prioridades` (`id`, `nombre`, `nivel`) VALUES
(1, 'Importante', 4),
(3, 'Urgente', 3),
(4, 'No urgente', 2),
(5, 'Pregunta', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cliente` varchar(255) NOT NULL,
  `ciudad` int(11) DEFAULT NULL,
  `estado_id` int(11) NOT NULL,
  `ingeniero_responsable` int(11) DEFAULT NULL,
  `bom` text DEFAULT NULL,
  `distribuidor` varchar(255) DEFAULT 'Por definir',
  `costo_software` int(20) DEFAULT 0,
  `costo_hardware` int(20) DEFAULT 0,
  `resumen` text DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `comercial_responsable` int(11) NOT NULL,
  `monto` int(20) DEFAULT 0,
  `tipo` int(2) NOT NULL,
  `clasificacion` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `cliente`, `ciudad`, `estado_id`, `ingeniero_responsable`, `bom`, `distribuidor`, `costo_software`, `costo_hardware`, `resumen`, `fecha_creacion`, `comercial_responsable`, `monto`, `tipo`, `clasificacion`) VALUES
(13, 'proyecto 2', 'Cliente test', 9, 17, 11, NULL, 'Safeteck', 200, 300, 'asdasdad', '2025-01-14', 11, 1208123, 1, 1),
(14, 'test', 'Cliente test', 6, 13, NULL, NULL, 'Por definir', 0, 100, 'proyecto test', '2025-01-14', 11, 0, 1, 1),
(15, 'test', 'Cliente test', 6, 13, 13, NULL, 'Por definir', 0, 150, 'test', '2025-01-15', 13, 0, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

CREATE TABLE `regiones` (
  `id` int(11) NOT NULL,
  `nombre_region` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`id`, `nombre_region`) VALUES
(1, 'Región de Arica y Parinacota'),
(2, 'Región de Tarapacá'),
(3, 'Región de Antofagasta'),
(4, 'Región de Atacama'),
(5, 'Región de Coquimbo'),
(6, 'Región de Valparaíso'),
(7, 'Región Metropolitana de Santiago'),
(8, 'Región del Libertador General Bernardo O’Higgins'),
(9, 'Región del Maule'),
(10, 'Región de Ñuble'),
(11, 'Región del Biobío'),
(12, 'Región de La Araucanía'),
(13, 'Región de Los Ríos'),
(14, 'Región de Los Lagos'),
(15, 'Región de Aysén del General Carlos Ibáñez del Campo'),
(16, 'Región de Magallanes y de la Antártica Chilena');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `titulo` text NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado_id` int(11) DEFAULT 7
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`id`, `ticket_id`, `titulo`, `fecha_creacion`, `fecha_actualizacion`, `estado_id`) VALUES
(112, 35, 'task 1', '2025-01-14 10:27:43', '2025-01-14 10:27:43', 7),
(113, 35, 'task 2', '2025-01-14 10:38:07', '2025-01-14 10:38:07', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `email_id` varchar(300) DEFAULT NULL,
  `subject` varchar(300) DEFAULT NULL,
  `task_type` varchar(300) DEFAULT NULL,
  `prioprity` int(11) DEFAULT NULL,
  `ticket` longtext DEFAULT NULL,
  `attachment` varchar(300) DEFAULT NULL,
  `admin_remark` longtext DEFAULT NULL,
  `posting_date` date DEFAULT NULL,
  `admin_remark_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ticket_img` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `ticket`
--

INSERT INTO `ticket` (`id`, `email_id`, `subject`, `task_type`, `prioprity`, `ticket`, `attachment`, `admin_remark`, `posting_date`, `admin_remark_date`, `status`, `user_id`, `ticket_img`) VALUES
(35, 'desarrolladorsafeteck@hotmail.com', 'Ticket de prueba', 'Incidente Lógica', 3, 'test', NULL, 'SI', '2025-01-02', '2025-01-14 14:12:29', 10, 8, NULL),
(36, 'desarrolladorsafeteck@hotmail.com', 'Ticket de prueba 2', 'Fallo a Nivel de Servidor', 5, 'test 2', NULL, 'test2', '2025-01-02', '2025-01-02 16:44:09', 10, 8, NULL),
(50, 'desarrolladorsafeteck@hotmail.com', 'test imagen admin', 'Incidente Lógica', 3, 'test imagen admin', NULL, NULL, '2025-01-15', '2025-01-15 20:57:38', 11, 11, 'assets/uploads/ticket_678820e96eb729.63443395.png'),
(51, 'desarrolladorsafeteck@hotmail.com', 'test imagen cliente', 'Fallo a Nivel de Servidor', 4, 'asas', NULL, NULL, '2025-01-15', '2025-01-15 20:58:28', 11, 11, 'assets/uploads/ticket_6788217460e789.35945211.png'),
(52, 'test@test.com', 'test imagen 2', 'Incidente Lógica', 3, 'asdad', NULL, NULL, '2025-01-15', '2025-01-15 20:58:56', 11, 13, '../assets/uploads/ticket_678821900bbb68.88942021.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_proyecto`
--

CREATE TABLE `tipo_proyecto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_proyecto`
--

INSERT INTO `tipo_proyecto` (`id`, `nombre`) VALUES
(1, 'Licitación'),
(2, 'Contacto ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `alt_email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `posting_date` timestamp NULL DEFAULT current_timestamp(),
  `rol` varchar(256) DEFAULT 'user',
  `cargo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `alt_email`, `password`, `mobile`, `gender`, `address`, `status`, `posting_date`, `rol`, `cargo`) VALUES
(11, 'Kevin Antecao', 'desarrolladorsafeteck@hotmail.com', 'test@test.com', '$2y$10$pCYT1DDd5z0NbBBeIv9gv.Wj1tNYLH0qAdK5gYdKE/MT1gH1xMySy', '999357718', 'male', 'asd123', 1, '2025-01-03 17:00:38', 'user', 1),
(13, 'New user', 'test@test.com', NULL, '$2y$10$68AmtkGRTqCqC6qVEvQ3AeiqFLOz01MKUOtqEohlzynNDO.jDatX2', '999357718', 'male', NULL, 1, '2025-01-03 17:00:38', 'admin', 1),
(15, 'test user', 'test2@test.com', NULL, '$2y$10$6NcnerdAtQu9eAsQaUUnaOs9X1DZgKLa7C48PzalWosIB410oY1eW', '1231231312', NULL, NULL, 1, '2025-01-14 16:35:08', 'user', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id` (`region_id`);

--
-- Indices de la tabla `clasificacion_proyecto`
--
ALTER TABLE `clasificacion_proyecto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contactos_proyecto`
--
ALTER TABLE `contactos_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `licitacion_proyecto`
--
ALTER TABLE `licitacion_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_licproyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `prioridades`
--
ALTER TABLE `prioridades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estado_id` (`estado_id`),
  ADD KEY `fk_user_proyecto` (`comercial_responsable`),
  ADD KEY `fk_type` (`tipo`),
  ADD KEY `fk_class` (`clasificacion`),
  ADD KEY `fk_ciudad` (`ciudad`),
  ADD KEY `fk_ingeniero_id` (`ingeniero_responsable`);

--
-- Indices de la tabla `regiones`
--
ALTER TABLE `regiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `fk_estado` (`estado_id`);

--
-- Indices de la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prioprity` (`prioprity`),
  ADD KEY `fk_ticket_estado` (`status`);

--
-- Indices de la tabla `tipo_proyecto`
--
ALTER TABLE `tipo_proyecto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cargo_id` (`cargo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `clasificacion_proyecto`
--
ALTER TABLE `clasificacion_proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `contactos_proyecto`
--
ALTER TABLE `contactos_proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `licitacion_proyecto`
--
ALTER TABLE `licitacion_proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `prioridades`
--
ALTER TABLE `prioridades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `regiones`
--
ALTER TABLE `regiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `tipo_proyecto`
--
ALTER TABLE `tipo_proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `ciudades_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`);

--
-- Filtros para la tabla `contactos_proyecto`
--
ALTER TABLE `contactos_proyecto`
  ADD CONSTRAINT `FK_proyecto_id` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `licitacion_proyecto`
--
ALTER TABLE `licitacion_proyecto`
  ADD CONSTRAINT `FK_licproyecto_id` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_ciudad` FOREIGN KEY (`ciudad`) REFERENCES `ciudades` (`id`),
  ADD CONSTRAINT `fk_class` FOREIGN KEY (`clasificacion`) REFERENCES `clasificacion_proyecto` (`id`),
  ADD CONSTRAINT `fk_ingeniero_id` FOREIGN KEY (`ingeniero_responsable`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_type` FOREIGN KEY (`tipo`) REFERENCES `tipo_proyecto` (`id`),
  ADD CONSTRAINT `fk_user_proyecto` FOREIGN KEY (`comercial_responsable`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`),
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_prioridad` FOREIGN KEY (`prioprity`) REFERENCES `prioridades` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_estado` FOREIGN KEY (`status`) REFERENCES `estados` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_cargo_id` FOREIGN KEY (`cargo`) REFERENCES `cargos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
