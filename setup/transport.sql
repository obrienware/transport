-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Dec 02, 2024 at 02:27 PM
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

--
-- Dumping data for table `airlines`
--

INSERT INTO `airlines` (`id`, `name`, `flight_number_prefix`, `image_filename`) VALUES
(1, 'Lufthansa', 'LH', 'Airlines-Lufthansa.png'),
(2, 'Delta Air Lines', 'DL', 'Airlines-Delta-Airlines.png'),
(3, 'Southwest Airlines', 'WN', 'Airlines-Southwest.png'),
(4, 'Frontier Airlines', 'F9', 'Airlines-Frontier-Airlines.png'),
(5, 'United Airlines', 'UA', 'Airlines-United-Airlines.png'),
(6, 'British Airways', 'BA', 'Airlines-British-Airways.png'),
(7, 'Aer Lingus', 'EIN', 'Aer_Lingus-Logo-1.png'),
(8, 'Aeromexico', 'AM', 'Airlines-Aeromexico.png'),
(9, 'Air Canada', 'AC', 'Airlines-Air-Canada.png'),
(10, 'Air France', 'AF', 'Airlines-Air-France.png'),
(11, 'Alaska Airlines', 'AS', 'Airlines-Alaska-Airlines.png'),
(12, 'Allegiant', 'G4', 'Airlines-Allegiant.png'),
(13, 'American Airlines', 'AA', 'Airlines-American-Airlines.png'),
(14, 'Breeze Airways', 'MX', 'Breeze.jpg'),
(15, 'Cayman Airways', 'KX', 'Airlines-Cayman-Airways.png'),
(16, 'Copa Airlines', 'CM', 'Airlines-Copa-Airlines.png'),
(17, 'Denver Air Connection', 'KG', 'Airlines-Denver-Air-Connection.png'),
(18, 'Edelweiss', 'WK', 'Airlines-Edelweiss.png'),
(19, 'Icelandair', 'FI', 'Airlines-Icelandair.png'),
(20, 'JetBlue Airways', 'B6', 'Airlines-JetBlue.png'),
(21, 'Southern Airways Express', 'SOO', 'Airlines-Southern-Airways-Express.png'),
(22, 'Sun Country Airlines', 'SY', 'Airlines-Suncountry.png'),
(23, 'Turkish Airlines', 'TK', 'TurkishAirlines.jpg'),
(24, 'Viva Aerobus', 'VB', 'FIDS-BIDS_156x28.jpg'),
(25, 'Volaris', 'Y4', 'Airlines-Volaris.png'),
(26, 'WestJet', 'WS', 'Airlines-Westjet.png');

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
  `message_template` text DEFAULT NULL,
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
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) UNSIGNED NOT NULL,
  `requestor_id` int(11) DEFAULT NULL,
  `summary` varchar(1000) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL COMMENT 'When the trip should start',
  `pickup_date` datetime DEFAULT NULL COMMENT 'When you need to pick the guest/group up',
  `end_date` datetime DEFAULT NULL COMMENT 'When the trip would complete',
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
  `last_update` datetime DEFAULT NULL,
  `last_updated_by` varchar(100) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `fuel_level` int(11) DEFAULT NULL,
  `clean_interior` tinyint(1) DEFAULT NULL COMMENT 'The interior needs to be cleaned',
  `clean_exterior` tinyint(1) DEFAULT NULL COMMENT 'The exterior needs to be cleaned',
  `restock` tinyint(1) DEFAULT NULL COMMENT 'The vehicle needs to be restocked with refreshments, etc.',
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL
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

--
-- Dumping data for table `weather_codes`
--

INSERT INTO `weather_codes` (`id`, `code`, `description`, `icon_day`, `icon_night`) VALUES
(1, '0', 'Clear sky', 'wi-day-sunny', 'wi-night-clear'),
(2, '1', 'Mainly clear', 'wi-day-sunny-overcast', 'wi-night-alt-cloudy'),
(3, '2', 'Partly cloudy', 'wi-day-cloudy', 'wi-night-cloudy'),
(4, '3', 'Overcast', 'wi-cloud', 'wi-cloud'),
(5, '45', 'Fog', 'wi-day-fog', 'wi-night-fog'),
(6, '48', 'Depositing rime fog', 'wi-day-fog', 'wi-night-fog'),
(7, '51', 'Light drizzle', 'wi-day-sprinkle', 'wi-night-sprinkle'),
(8, '53', 'Moderate drizzle', 'wi-day-showers', 'wi-night-showers'),
(9, '55', 'Dense drizzle', 'wi-day-showers', 'wi-night-showers'),
(10, '56', 'Light freezing drizzle', 'wi-day-rain-mix', 'wi-night-alt-rain-mix'),
(11, '57', 'Dense freezing drizzle', 'wi-day-rain-mix', 'wi-night-alt-rain-mix'),
(12, '61', 'Slight rain', 'wi-day-showers', 'wi-night-alt-rain'),
(13, '63', 'Moderate rain', 'wi-day-showers', 'wi-night-alt-rain'),
(14, '65', 'Heavy rain', 'wi-day-showers', 'wi-night-alt-rain'),
(15, '66', 'Light freezing rain', 'wi-day-sleet', 'wi-night-sleet'),
(16, '67', 'Heavy freezing rain', 'wi-day-sleet', 'wi-night-sleet'),
(17, '71', 'Slight snowfall', 'wi-day-snow', 'wi-night-alt-snow'),
(18, '73', 'Moderate snowfall', 'wi-day-snow', 'wi-night-alt-snow'),
(19, '75', 'Heavy snowfall', 'wi-day-snow', 'wi-night-alt-snow'),
(20, '77', 'Snow grains', 'wi-day-snow', 'wi-night-alt-snow'),
(21, '80', 'Slight rain showers', 'wi-day-rain', 'wi-night-alt-rain'),
(22, '81', 'Moderate rain showers', 'wi-day-rain', 'wi-night-alt-rain'),
(23, '82', 'Violent rain showers', 'wi-day-rain', 'wi-night-alt-rain'),
(24, '85', 'Slight snow showers', 'wi-day-snow', 'wi-night-alt-snow'),
(25, '86', 'Heavy snow showers', 'wi-day-snow', 'wi-night-alt-snow'),
(26, '95', 'Thunderstorms', 'wi-day-lightning', 'wi-night-alt-lightning'),
(27, '96', 'Thunderstorms with slight hail', 'wi-day-sleet-storm', 'wi-night-alt-snow-thunderstorm'),
(28, '99', 'Thunderstorms with heavy hail', 'wi-day-sleet-storm', 'wi-night-alt-snow-thunderstorm');

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
