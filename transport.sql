-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Nov 25, 2024 at 06:41 PM
-- Server version: 11.5.2-MariaDB-ubu2404
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
  `lead_time` int(11) DEFAULT NULL COMMENT 'time to arrive before your flight (in mins)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) UNSIGNED NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `group_size` int(11) DEFAULT NULL,
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
  `meta` text DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
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
  `resolved` datetime DEFAULT NULL,
  `resolved_by` varchar(100) DEFAULT NULL,
  `resolution` text DEFAULT NULL
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
  `end_date` datetime DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `passengers` int(11) DEFAULT NULL,
  `pu_location` int(11) DEFAULT NULL,
  `do_location` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `airline_id` int(11) DEFAULT NULL,
  `flight_number` varchar(20) DEFAULT NULL,
  `vehicle_pu_options` enum('pick up from staging','guest will have vehicle') DEFAULT NULL,
  `vehicle_do_options` enum('return to staging','leave vehicle with guest') DEFAULT NULL,
  `eta` datetime DEFAULT NULL,
  `etd` datetime DEFAULT NULL,
  `iata` varchar(5) DEFAULT NULL,
  `guest_notes` text DEFAULT NULL,
  `driver_notes` text DEFAULT NULL,
  `general_notes` text DEFAULT NULL,
  `finalized` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'When this changes to true - all the respective notifications should be sent',
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
-- Table structure for table `trip_summary`
--

CREATE TABLE `trip_summary` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL COMMENT '1-5 stars - how was the trip?',
  `road_conditions` int(11) DEFAULT NULL COMMENT '1-5',
  `weather_conditions` int(11) DEFAULT NULL COMMENT '1-5'
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
  `vehicle_mileage` int(11) DEFAULT NULL,
  `vehicle_fuel` int(11) DEFAULT NULL,
  `vehicle_clean_interior` tinyint(1) DEFAULT NULL,
  `vehicle_clean_exterior` tinyint(1) DEFAULT NULL,
  `vehicle_restock` tinyint(1) DEFAULT NULL,
  `vehicle_issues` text DEFAULT NULL,
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
  `date_time` datetime DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `target` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Est duration from previous waypoint',
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
  `passengers` int(11) DEFAULT NULL,
  `require_cdl` tinyint(1) NOT NULL DEFAULT 0,
  `mileage` int(11) DEFAULT NULL,
  `check_engine` tinyint(1) NOT NULL DEFAULT 0,
  `default_staging_location_id` int(11) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_locations`
--

CREATE TABLE `vehicle_locations` (
  `id` int(11) UNSIGNED NOT NULL,
  `datetimestamp` datetime DEFAULT NULL COMMENT 'Latest location update',
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL COMMENT 'Driver to last drive vehicle',
  `location_id` int(11) DEFAULT NULL,
  `fuel_level` int(11) DEFAULT NULL COMMENT 'Integer representing percentage (e.g. 50 means 50%)',
  `mileage` int(11) DEFAULT NULL,
  `clean_exterior` tinyint(1) DEFAULT NULL,
  `clean_interior` tinyint(1) DEFAULT NULL,
  `needs_restocking` tinyint(1) DEFAULT NULL COMMENT 'Need to be restocked with snacks',
  `concerns` text DEFAULT NULL,
  `note` text DEFAULT NULL
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
  `id` int(11) UNSIGNED NOT NULL
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
-- Indexes for table `snags`
--
ALTER TABLE `snags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_summary`
--
ALTER TABLE `trip_summary`
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
-- Indexes for table `vehicle_locations`
--
ALTER TABLE `vehicle_locations`
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
-- AUTO_INCREMENT for table `snags`
--
ALTER TABLE `snags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_summary`
--
ALTER TABLE `trip_summary`
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
-- AUTO_INCREMENT for table `vehicle_locations`
--
ALTER TABLE `vehicle_locations`
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
