-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-12-2024 a las 18:58:44
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
-- Base de datos: `tickets`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(2, 'configuroweb', '1234abcd..');

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
(12, 'Cerrado', 'ticket');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`id`, `ticket_id`, `sender`, `message`, `created_at`) VALUES
(102, 18, 'Mauricio Sevilla', 'hola', '2024-12-17 15:32:30'),
(103, 18, 'Mauricio Sevilla', 'hola', '2024-12-17 15:33:01'),
(104, 18, 'Mauricio Sevilla', 'test', '2024-12-17 15:40:19');

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
(102, 34, 'task 1', '2024-12-27 12:48:47', '2024-12-27 14:34:37', 9),
(103, 34, 'task 2', '2024-12-27 12:48:47', '2024-12-27 14:35:37', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(11) DEFAULT NULL,
  `email_id` varchar(300) DEFAULT NULL,
  `subject` varchar(300) DEFAULT NULL,
  `task_type` varchar(300) DEFAULT NULL,
  `prioprity` int(11) DEFAULT NULL,
  `ticket` longtext DEFAULT NULL,
  `attachment` varchar(300) DEFAULT NULL,
  `admin_remark` longtext DEFAULT NULL,
  `posting_date` date DEFAULT NULL,
  `admin_remark_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `ticket`
--

INSERT INTO `ticket` (`id`, `ticket_id`, `email_id`, `subject`, `task_type`, `prioprity`, `ticket`, `attachment`, `admin_remark`, `posting_date`, `admin_remark_date`, `status`) VALUES
(23, '12', 'hola@cweb.com', 'importante', 'Incidente Lógica', 1, 'importante', NULL, 'REALIZANDO TAREA 2', '2024-12-17', '2024-12-27 16:57:52', 12),
(24, '13', 'hola@cweb.com', 'importante2', 'Incidente Lógica', 1, 'importante 2', NULL, 'Finalizado', '2024-12-17', '2024-12-27 16:58:03', 10),
(25, '14', 'hola@cweb.com', 'no urgente', 'Incidente Lógica', 4, 'no urgente', NULL, NULL, '2024-12-17', '2024-12-26 13:44:47', 11),
(26, '15', 'hola@cweb.com', 'urgente', 'Fallo a Nivel de Servidor', 3, 'urgente', NULL, '', '2024-10-17', '2024-12-26 20:26:24', 11),
(27, '16', 'pcliente@cweb.com', 'ticket nuevo', 'Fallo a Nivel de Servidor', 5, 'Ticket de prueba', NULL, 'Ticket en proceso', '2024-10-24', '2024-12-26 20:26:14', 11),
(28, '17', 'pcliente@cweb.com', 'Test ticket 2', 'Incidente Lógica', 1, 'test 2', NULL, 'Revisado', '2024-12-24', '2024-12-26 13:44:47', 11),
(29, '18', 'test2@test.com', 'test2', 'Incidente Lógica', 3, 'sas', NULL, NULL, '2024-12-26', '2024-12-26 13:47:20', 11),
(30, '19', 'test2@test.com', 'ticket nuevo', 'Incidente Lógica', 4, 'nuevo', NULL, 'sas', '2024-12-26', '2024-12-26 14:51:19', 12),
(31, '20', 'test2@test.com', '3', 'Incidente Lógica', 3, '3', NULL, '3', '2024-12-26', '2024-12-26 14:50:12', 10),
(32, '21', 'test2@test.com', '4', 'Incidente Lógica', 3, '4', NULL, 'si', '2024-12-26', '2024-12-26 14:50:06', 10),
(33, '22', 'test2@test.com', '5', 'Incidente Lógica', 3, '5', NULL, 'cerrado', '2024-11-26', '2024-12-26 17:07:27', 12),
(34, '23', 'desarrolladorsafeteck@hotmail.com', 'Testeo email', 'Incidente Lógica', 1, 'Testeo notificaciones email', NULL, 'Solucionado', '2024-12-27', '2024-12-27 17:35:37', 12);

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
  `status` int(11) DEFAULT NULL,
  `posting_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `alt_email`, `password`, `mobile`, `gender`, `address`, `status`, `posting_date`) VALUES
(1, 'Mauricio Sevilla', 'hola@cweb.com', 'admin@cweb.com', '1234abcd..', '3162430081', 'male', 'Calle 45 23 ', NULL, '2021-04-22 12:25:19'),
(2, 'Pedro Cliente', 'pcliente@cweb.com', 'pecliente@cweb.com', '1234abcd..', '3025869471', 'm', 'Sample Address only', NULL, '2022-11-29 03:28:28'),
(7, 'test 2', 'test2@test.com', NULL, '$2y$10$lgmQb7mQbrmUAAkgYMFcv.S7CYL4hJ9WGtwVQ.LsdM9ACXsEJZuIe', '107398140', 'male', NULL, NULL, '2024-12-24 17:56:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `prioridades`
--
ALTER TABLE `prioridades`
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
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de la tabla `prioridades`
--
ALTER TABLE `prioridades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
