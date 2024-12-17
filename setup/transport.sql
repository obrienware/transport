-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Dec 17, 2024 at 04:43 PM
-- Server version: 11.6.2-MariaDB-ubu2404
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `transport`
--

-- --------------------------------------------------------

--
-- Table structure for table `airlines`
--

CREATE TABLE `airlines` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `flight_number_prefix` varchar(10) DEFAULT NULL,
  `image_filename` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `airports`
--

CREATE TABLE `airports` (
  `id` int(11) UNSIGNED NOT NULL,
  `iata` varchar(5) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `staging_location_id` int(11) DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL COMMENT 'time to arrive before your flight (in mins)',
  `arrival_instructions_small` text DEFAULT NULL,
  `arrival_instructions_group` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `affected_tables` varchar(255) DEFAULT NULL,
  `before` text DEFAULT NULL,
  `after` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `meta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `authentication_log`
--

CREATE TABLE `authentication_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `successful` tinyint(1) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `node` varchar(255) NOT NULL,
  `config` longtext NOT NULL,
  `json5` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_log`
--

CREATE TABLE `config_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `node` varchar(50) DEFAULT NULL,
  `config` text DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `can_submit_requests` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_blockout_dates`
--

CREATE TABLE `driver_blockout_dates` (
  `id` int(11) UNSIGNED NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `from` datetime DEFAULT NULL,
  `till` datetime DEFAULT NULL,
  `note` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `requestor_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `driver_ids` varchar(100) DEFAULT NULL COMMENT 'List of drivers (ids) assigned to this event',
  `vehicle_ids` varchar(100) DEFAULT NULL COMMENT 'List of vehicles assigned to this event',
  `notes` text DEFAULT NULL,
  `confirmed` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flight_data`
--

CREATE TABLE `flight_data` (
  `id` int(11) UNSIGNED NOT NULL,
  `row` bigint(20) DEFAULT NULL,
  `flight_number` varchar(50) DEFAULT NULL,
  `status_live` tinyint(1) NOT NULL DEFAULT 0,
  `status_text` varchar(255) DEFAULT NULL,
  `status_icon` varchar(10) DEFAULT NULL,
  `airport_origin` varchar(255) DEFAULT NULL,
  `airport_origin_iata` varchar(10) DEFAULT NULL,
  `scheduled_departure` datetime DEFAULT NULL,
  `estimated_departure` datetime DEFAULT NULL,
  `real_departure` datetime DEFAULT NULL,
  `airport_destination` varchar(255) DEFAULT NULL,
  `airport_destination_iata` varchar(10) DEFAULT NULL,
  `scheduled_arrival` datetime DEFAULT NULL,
  `estimated_arrival` datetime DEFAULT NULL,
  `real_arrival` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `map_address` varchar(1024) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lon` decimal(10,7) DEFAULT NULL,
  `place_id` varchar(100) DEFAULT NULL COMMENT 'Google Place ID',
  `type` varchar(100) DEFAULT NULL,
  `iata` varchar(5) DEFAULT NULL,
  `message_template` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opt_in_text`
--

CREATE TABLE `opt_in_text` (
  `id` int(11) UNSIGNED NOT NULL,
  `tel` varchar(50) DEFAULT NULL,
  `opt_in` datetime DEFAULT NULL,
  `opt_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `snags`
--

CREATE TABLE `snags` (
  `id` int(11) UNSIGNED NOT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `acknowledged` datetime DEFAULT NULL,
  `acknowledged_by` varchar(100) DEFAULT NULL,
  `resolved` datetime DEFAULT NULL,
  `resolved_by` varchar(100) DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `text_out`
--

CREATE TABLE `text_out` (
  `id` int(11) UNSIGNED NOT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `recipient` varchar(50) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `result` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) UNSIGNED NOT NULL,
  `requestor_id` int(11) DEFAULT NULL,
  `summary` varchar(1000) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `guests` varchar(1024) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `passengers` int(11) DEFAULT NULL,
  `pu_location` int(11) DEFAULT NULL,
  `do_location` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `airline_id` int(11) DEFAULT NULL,
  `flight_number` varchar(20) DEFAULT NULL,
  `flight_status` varchar(255) DEFAULT NULL,
  `flight_status_as_at` datetime DEFAULT NULL,
  `flight_info` text DEFAULT NULL,
  `vehicle_pu_options` enum('pick up from staging','guest will have vehicle','commence from current location') DEFAULT NULL,
  `vehicle_do_options` enum('return to staging','leave vehicle with guest','remain at destination') DEFAULT NULL,
  `eta` datetime DEFAULT NULL,
  `etd` datetime DEFAULT NULL,
  `iata` varchar(5) DEFAULT NULL,
  `guest_notes` text DEFAULT NULL,
  `driver_notes` text DEFAULT NULL,
  `general_notes` text DEFAULT NULL,
  `confirmed` datetime DEFAULT NULL,
  `linked_trip_id` int(11) DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `completed` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_surveys`
--

CREATE TABLE `trip_surveys` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `rating_trip` int(11) DEFAULT NULL,
  `rating_weather` int(11) DEFAULT NULL,
  `rating_road` int(11) DEFAULT NULL,
  `guest_issues` text DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_waypoints`
--

CREATE TABLE `trip_waypoints` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `pickup` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Guest pick up location?',
  `description` varchar(100) DEFAULT NULL,
  `reached` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `change_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Set this to true to force the user to change their password',
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `roles` varchar(1024) DEFAULT NULL COMMENT 'We''ll use this as a set (array)',
  `position` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `cdl` tinyint(1) NOT NULL DEFAULT 0,
  `personal_preferences` text DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_blockouts`
--

CREATE TABLE `user_blockouts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `from_datetime` datetime DEFAULT NULL,
  `to_datetime` datetime DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) UNSIGNED NOT NULL,
  `color` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `passengers` int(11) DEFAULT NULL,
  `require_cdl` tinyint(1) NOT NULL DEFAULT 0,
  `mileage` int(11) DEFAULT NULL,
  `check_engine` tinyint(1) NOT NULL DEFAULT 0,
  `default_staging_location_id` int(11) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `last_updated_by` varchar(100) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `fuel_level` int(11) DEFAULT NULL,
  `clean_interior` tinyint(1) DEFAULT NULL,
  `clean_exterior` tinyint(1) DEFAULT NULL,
  `restock` tinyint(1) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_documents`
--

CREATE TABLE `vehicle_documents` (
  `id` int(11) UNSIGNED NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded` datetime DEFAULT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_maintenance_repair_jobs`
--

CREATE TABLE `vehicle_maintenance_repair_jobs` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_maintenance_schedules`
--

CREATE TABLE `vehicle_maintenance_schedules` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weather_codes`
--

CREATE TABLE `weather_codes` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon_day` varchar(100) DEFAULT NULL,
  `icon_night` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhook_clicksend`
--

CREATE TABLE `webhook_clicksend` (
  `id` int(11) UNSIGNED NOT NULL,
  `originalsenderid` varchar(50) DEFAULT NULL,
  `body` varchar(1000) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `sms` varchar(50) DEFAULT NULL,
  `custom_string` varchar(255) DEFAULT NULL,
  `to` varchar(50) DEFAULT NULL,
  `original_message_id` varchar(100) DEFAULT NULL,
  `originalmessageid` varchar(100) DEFAULT NULL,
  `customstring` varchar(255) DEFAULT NULL,
  `from` varchar(50) DEFAULT NULL,
  `originalmessage` varchar(1000) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subaccount_id` int(11) DEFAULT NULL,
  `original_body` varchar(1000) DEFAULT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `message_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `_flight_check`
--

CREATE TABLE `_flight_check` (
  `id` int(11) UNSIGNED NOT NULL,
  `flight_number` varchar(20) DEFAULT NULL,
  `last_checked` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `airports`
--
ALTER TABLE `airports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datetimestamp` (`datetimestamp`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `authentication_log`
--
ALTER TABLE `authentication_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datetimestamp` (`datetimestamp`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `node` (`node`);

--
-- Indexes for table `config_log`
--
ALTER TABLE `config_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datetimestamp` (`datetimestamp`),
  ADD KEY `node` (`node`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_blockout_dates`
--
ALTER TABLE `driver_blockout_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flight_data`
--
ALTER TABLE `flight_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `row` (`row`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opt_in_text`
--
ALTER TABLE `opt_in_text`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tel` (`tel`);

--
-- Indexes for table `snags`
--
ALTER TABLE `snags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `text_out`
--
ALTER TABLE `text_out`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_surveys`
--
ALTER TABLE `trip_surveys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_waypoints`
--
ALTER TABLE `trip_waypoints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `email_address` (`email_address`);

--
-- Indexes for table `user_blockouts`
--
ALTER TABLE `user_blockouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_maintenance_repair_jobs`
--
ALTER TABLE `vehicle_maintenance_repair_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_maintenance_schedules`
--
ALTER TABLE `vehicle_maintenance_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weather_codes`
--
ALTER TABLE `weather_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webhook_clicksend`
--
ALTER TABLE `webhook_clicksend`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `_flight_check`
--
ALTER TABLE `_flight_check`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flight_number` (`flight_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airlines`
--
ALTER TABLE `airlines`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `airports`
--
ALTER TABLE `airports`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `authentication_log`
--
ALTER TABLE `authentication_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_log`
--
ALTER TABLE `config_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_blockout_dates`
--
ALTER TABLE `driver_blockout_dates`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flight_data`
--
ALTER TABLE `flight_data`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opt_in_text`
--
ALTER TABLE `opt_in_text`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `snags`
--
ALTER TABLE `snags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `text_out`
--
ALTER TABLE `text_out`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_surveys`
--
ALTER TABLE `trip_surveys`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_waypoints`
--
ALTER TABLE `trip_waypoints`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_blockouts`
--
ALTER TABLE `user_blockouts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_maintenance_repair_jobs`
--
ALTER TABLE `vehicle_maintenance_repair_jobs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_maintenance_schedules`
--
ALTER TABLE `vehicle_maintenance_schedules`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weather_codes`
--
ALTER TABLE `weather_codes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webhook_clicksend`
--
ALTER TABLE `webhook_clicksend`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `_flight_check`
--
ALTER TABLE `_flight_check`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
