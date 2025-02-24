# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 11.6.2-MariaDB-ubu2404)
# Database: transport
# Generation Time: 2025-02-24 17:24:51 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table _flight_check
# ------------------------------------------------------------

CREATE TABLE `_flight_check` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `flight_number` varchar(20) DEFAULT NULL,
  `last_checked` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flight_number` (`flight_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table airlines
# ------------------------------------------------------------

CREATE TABLE `airlines` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `flight_number_prefix` varchar(10) DEFAULT NULL,
  `image_filename` varchar(1000) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table airport_locations
# ------------------------------------------------------------

CREATE TABLE `airport_locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `airport_id` int(11) DEFAULT NULL,
  `airline_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'arrivals or departures',
  `location_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table airports
# ------------------------------------------------------------

CREATE TABLE `airports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iata` varchar(5) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `staging_location_id` int(11) DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL COMMENT 'time to arrive before your flight (in mins)',
  `travel_time` int(11) DEFAULT NULL COMMENT 'time to travel to/from the airport',
  `arrival_instructions_small` text DEFAULT NULL,
  `arrival_instructions_group` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table audit_trail
# ------------------------------------------------------------

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetimestamp` datetime DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `affected_tables` varchar(255) DEFAULT NULL,
  `before` text DEFAULT NULL,
  `after` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `meta` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datetimestamp` (`datetimestamp`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



# Dump of table authentication_log
# ------------------------------------------------------------

CREATE TABLE `authentication_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetimestamp` datetime DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `successful` tinyint(1) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datetimestamp` (`datetimestamp`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



# Dump of table config
# ------------------------------------------------------------

CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node` varchar(255) NOT NULL,
  `config` longtext NOT NULL,
  `json5` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `node` (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



# Dump of table config_log
# ------------------------------------------------------------

CREATE TABLE `config_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetimestamp` datetime DEFAULT NULL,
  `node` varchar(50) DEFAULT NULL,
  `config` text DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datetimestamp` (`datetimestamp`),
  KEY `node` (`node`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



# Dump of table departments
# ------------------------------------------------------------

CREATE TABLE `departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `can_submit_requests` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table driver_notes
# ------------------------------------------------------------

CREATE TABLE `driver_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table email_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_templates`;

CREATE TABLE `email_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `available_variables` varchar(1000) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;

INSERT INTO `email_templates` (`id`, `name`, `content`, `available_variables`, `created`, `created_by`, `modified`, `modified_by`, `archived`, `archived_by`)
VALUES
	(1,'Email Requestor New Trip','Hello {{name}},\n\nThe following trip has been scheduled:\n\n{{tripSummary}}\n\nPlease find your guest information sheet attached. Please have your guest scan the QR code on the sheet in order to recieve timely notifications relevant to their trip.\n\nRegards,\nTransportation Team','name, tripSummary',NULL,NULL,NULL,NULL,NULL,NULL),
	(2,'Email Requestor Trip Change','Hello {{name}},\n\nThe following changes have been made to the trip:\n\n{{tripDate}}\n{{tripSummary}}\n\n{{changes}}\n\nPlease find your guest information sheet attached. Please have your guest scan the QR code on the sheet in order to recieve timely notifications relevant to their trip.\n\nRegards,\nTransportation Team\n','name, tripDate, tripSummary, changes',NULL,NULL,NULL,NULL,NULL,NULL),
	(3,'Email Requestor New Event','Hello {{name}},\n\nThe following event has been scheduled:\n\n{{startDate}} - {{endDate}}\n{{eventName}}\n\nRegards,\nTransportation Team\n','name, startDate, endDate, eventName',NULL,NULL,NULL,NULL,NULL,NULL),
	(4,'Email Requestor Event Change','Hello {{name}},\n\nThe following changes have been made to the event:\n\n{{startDate}} - {{endDate}}\n{{eventName}}\n\n{{changes}}\n\n\nRegards,\nTransportation Team\n','name, startDate, endDate, eventName',NULL,NULL,NULL,NULL,NULL,NULL),
	(5,'Email Driver New Trip','Hello {{name}},\n\nThe following trip has been assigned to you:\n\n{{tripDate}}\n{{tripSummary}}\n\nPlease find your information sheet attached. This trip will automatically be tracked in your app.\n\nRegards,\nTransportation Team\n','name, tripDate, tripSummary',NULL,NULL,NULL,NULL,NULL,NULL),
	(6,'Email Driver Trip Change','Hello {{name}},\n\nThe following changes have been made to the trip:\n\n{{tripDate}}\n{{tripSummary}}\n\n{{changes}}\n\nPlease find your information sheet attached. This trip will automatically be tracked in your app.\n\nRegards,\nTransportation Team\n','name, tripDate, tripSummary, changes',NULL,NULL,NULL,NULL,NULL,NULL),
	(7,'Email Driver New Event','Hello {{name}},\n\nThe following event has been assigned to you:\n\n{{startDate}} - {{endDate}}\n{{eventName}}\n\nRegards,\nTransportation Team\n','name, startDate, endDate, eventName',NULL,NULL,NULL,NULL,NULL,NULL),
	(8,'Email Driver Event Change','Hello {{name}},\n\nThe following changes have been made to the event:\n\n{{startDate}} - {{endDate}}\n{{eventName}}\n\n{{changes}}\n\n\nRegards,\nTransportation Team','name, startDate, endDate, eventName',NULL,NULL,'2025-01-04 12:27:10','obrienware',NULL,NULL),
	(9,'Email Manager New Trip Request','Hello {{name}},\n\nThe following trip has been requested:\n\n{{tripDate}}\n{{summary}}\n\nRequestor ({{requestorEmail}}) Note(s):\n{{notes}}\n\n\nRegards,\nTransportation Team','name, summary, tripDate, notes, requestorEmail',NULL,NULL,'2025-01-04 13:29:42','obrienware',NULL,NULL),
	(10,'Email Manager New Event Request','Hello {{name}},\n\nThe following event has been requested:\n\n{{startDate}} - {{endDate}}\n{{summary}}\n\nRequestor ({{requestorEmail}}) Note(s):\n{{notes}}\n\n\nRegards,\nTransportation Team\n','name, summary, startDate, endDate, notes, requestorEmail',NULL,NULL,'2025-01-04 13:29:34','obrienware',NULL,NULL),
	(11,'Email Manager Trip Request Cancellation','Hello {{name}},\n\nThe following trip request has been cancelled by {{requestorEmail}}:\n\n{{tripDate}}\n{{summary}}\n\n\nRegards,\nTransportation Team','name, summary, tripDate, requestorEmail',NULL,NULL,NULL,NULL,NULL,NULL),
	(12,'Email Manager Event Request Cancellation','Hello {{name}},\n\nThe following event request has been cancelled by {{requestorEmail}}:\n\n{{startDate}} - {{endDate}}\n{{summary}}\n\n\nRegards,\nTransportation Team\n','name, summary, startDate, endDate, requestorEmail',NULL,NULL,NULL,NULL,NULL,NULL),
	(13,'Email Requestor Trip Deleted','Hello {{name}},\n\nThe following trip has been cancelled/deleted:\n\n{{tripDate}}\n{{tripSummary}}\n\n\nRegards,\nTransportation Team\n','name, tripDate, tripSummary',NULL,NULL,NULL,NULL,NULL,NULL),
	(14,'Email Driver Trip Deleted','Hello {{name}},\n\nThe following trip has been cancelled/deleted:\n\n{{tripDate}}\n{{tripSummary}}\n\n\nRegards,\nTransportation Team\n','name, tripDate, tripSummary',NULL,NULL,NULL,NULL,NULL,NULL),
	(15,'Email Requestor Event Deleted','Hello {{name}},\n\nThe following event has been cancelled/deleted:\n\n{{startDate}} - {{endDate}}\n{{eventName}}\n\n\nRegards,\nTransportation Team\n','name, startDate, endDate, eventName',NULL,NULL,NULL,NULL,NULL,NULL),
	(16,'Email Driver Event Deleted','Hello {{name}},\n\nThe following event has been cancelled/deleted:\n\n{{startDate}} - {{endDate}}\n{{eventName}}\n\n\nRegards,\nTransportation Team\n','name, startDate, endDate, eventName',NULL,NULL,NULL,NULL,NULL,NULL),
	(17,'Email Basic','Hello {{name}},\n\n{{content}}\n\nRegards,\nTransportation Team\n','name, content',NULL,NULL,NULL,NULL,NULL,NULL),
	(18,'Email Requestor New Reservation','Hello {{name}},\n\nThe following vehicle reservation has been made:\n\n{{startDateTime}} - {{endDateTime}}\nGuest: {{guest}}\nVehicle: {{vehicle}}\n\nRegards,\nTransportation Team','name, guest, reason, startDateTime, endDateTime, vehicle',NULL,NULL,'2025-01-31 15:52:55','richard',NULL,NULL),
	(19,'Email Requestor Vehicle Reservation Deleted','Hello {{name}},\n\nThe following vehicle reservation for {{guest}} has been cancelled/deleted:\n\n{{startDateTime}} - {{endDateTime}}\n\n\nRegards,\nTransportation Team\n','name, guest, startDateTime, endDateTime',NULL,NULL,'2025-01-31 15:51:21','richard',NULL,NULL),
	(20,'Email Requestor Vehicle Reservation Change','Hello {{name}},\n\nThe following changes have been made to the vehicle reservation for: {{guest}}\n\n{{startDateTime}} - {{endDateTime}}\n\n{{changes}}\n\n\nRegards,\nTransportation Team\n','name, guest, startDateTime, endDateTime, changes',NULL,NULL,'2025-01-31 15:50:19','richard',NULL,NULL);

/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table event_messages
# ------------------------------------------------------------

CREATE TABLE `event_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetimestamp` datetime DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table events
# ------------------------------------------------------------

CREATE TABLE `events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  `original_request` text DEFAULT NULL,
  `cancellation_requested` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table flight_data
# ------------------------------------------------------------

CREATE TABLE `flight_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `row` (`row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table guests
# ------------------------------------------------------------

CREATE TABLE `guests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table image_library
# ------------------------------------------------------------

CREATE TABLE `image_library` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table locations
# ------------------------------------------------------------

CREATE TABLE `locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table opt_in_text
# ------------------------------------------------------------

CREATE TABLE `opt_in_text` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tel` varchar(50) DEFAULT NULL,
  `opt_in` datetime DEFAULT NULL,
  `opt_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tel` (`tel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table snag_images
# ------------------------------------------------------------

CREATE TABLE `snag_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `snag_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `snag_id` (`snag_id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table snags
# ------------------------------------------------------------

CREATE TABLE `snags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `logged` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `summary` varchar(1024) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `acknowledged` datetime DEFAULT NULL,
  `acknowledged_by` varchar(100) DEFAULT NULL,
  `resolved` datetime DEFAULT NULL,
  `resolved_by` varchar(100) DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logged` (`logged`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table text_out
# ------------------------------------------------------------

CREATE TABLE `text_out` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetimestamp` datetime DEFAULT NULL,
  `recipient` varchar(50) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `result` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table trip_messages
# ------------------------------------------------------------

CREATE TABLE `trip_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetimestamp` datetime DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table trip_surveys
# ------------------------------------------------------------

CREATE TABLE `trip_surveys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) DEFAULT NULL,
  `datetimestamp` datetime DEFAULT NULL,
  `rating_trip` int(11) DEFAULT NULL,
  `rating_weather` int(11) DEFAULT NULL,
  `rating_road` int(11) DEFAULT NULL,
  `guest_issues` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table trip_waypoints
# ------------------------------------------------------------

CREATE TABLE `trip_waypoints` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `pickup` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Guest pick up location?',
  `description` varchar(100) DEFAULT NULL,
  `reached` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table trips
# ------------------------------------------------------------

CREATE TABLE `trips` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `require_more_information` tinyint(1) DEFAULT NULL,
  `cancellation_requested` datetime DEFAULT NULL,
  `linked_trip_id` int(11) DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `completed` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  `original_request` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table user_blockouts
# ------------------------------------------------------------

CREATE TABLE `user_blockouts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `from_datetime` datetime DEFAULT NULL,
  `to_datetime` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email_address` (`email_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table vehicle_documents
# ------------------------------------------------------------

CREATE TABLE `vehicle_documents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table vehicle_maintenance
# ------------------------------------------------------------

CREATE TABLE `vehicle_maintenance` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(11) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table vehicle_maintenance_repair_jobs
# ------------------------------------------------------------

CREATE TABLE `vehicle_maintenance_repair_jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table vehicle_maintenance_schedules
# ------------------------------------------------------------

CREATE TABLE `vehicle_maintenance_schedules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table vehicle_reservations
# ------------------------------------------------------------

CREATE TABLE `vehicle_reservations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `requestor_id` int(11) DEFAULT NULL,
  `start_trip_id` int(11) DEFAULT NULL,
  `end_trip_id` int(11) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `confirmed` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  `original_request` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_id` (`guest_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `start_trip_id` (`start_trip_id`),
  KEY `end_trip_id` (`end_trip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table vehicles
# ------------------------------------------------------------

CREATE TABLE `vehicles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `color` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `passengers` int(11) DEFAULT NULL,
  `require_cdl` tinyint(1) NOT NULL DEFAULT 0,
  `mileage` int(11) DEFAULT NULL,
  `check_engine` tinyint(1) DEFAULT NULL,
  `default_staging_location_id` int(11) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `last_updated_by` varchar(100) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `fuel_level` int(11) DEFAULT NULL,
  `clean_interior` tinyint(1) DEFAULT NULL,
  `clean_exterior` tinyint(1) DEFAULT NULL,
  `restock` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `archived` datetime DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table weather_codes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `weather_codes`;

CREATE TABLE `weather_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon_day` varchar(100) DEFAULT NULL,
  `icon_night` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

LOCK TABLES `weather_codes` WRITE;
/*!40000 ALTER TABLE `weather_codes` DISABLE KEYS */;

INSERT INTO `weather_codes` (`id`, `code`, `description`, `icon_day`, `icon_night`)
VALUES
	(1,'0','Clear sky','wi-day-sunny','wi-night-clear'),
	(2,'1','Mainly clear','wi-day-sunny-overcast','wi-night-alt-cloudy'),
	(3,'2','Partly cloudy','wi-day-cloudy','wi-night-cloudy'),
	(4,'3','Overcast','wi-cloud','wi-cloud'),
	(5,'45','Fog','wi-day-fog','wi-night-fog'),
	(6,'48','Depositing rime fog','wi-day-fog','wi-night-fog'),
	(7,'51','Light drizzle','wi-day-sprinkle','wi-night-sprinkle'),
	(8,'53','Moderate drizzle','wi-day-showers','wi-night-showers'),
	(9,'55','Dense drizzle','wi-day-showers','wi-night-showers'),
	(10,'56','Light freezing drizzle','wi-day-rain-mix','wi-night-alt-rain-mix'),
	(11,'57','Dense freezing drizzle','wi-day-rain-mix','wi-night-alt-rain-mix'),
	(12,'61','Slight rain','wi-day-showers','wi-night-alt-rain'),
	(13,'63','Moderate rain','wi-day-showers','wi-night-alt-rain'),
	(14,'65','Heavy rain','wi-day-showers','wi-night-alt-rain'),
	(15,'66','Light freezing rain','wi-day-sleet','wi-night-sleet'),
	(16,'67','Heavy freezing rain','wi-day-sleet','wi-night-sleet'),
	(17,'71','Slight snowfall','wi-day-snow','wi-night-alt-snow'),
	(18,'73','Moderate snowfall','wi-day-snow','wi-night-alt-snow'),
	(19,'75','Heavy snowfall','wi-day-snow','wi-night-alt-snow'),
	(20,'77','Snow grains','wi-day-snow','wi-night-alt-snow'),
	(21,'80','Slight rain showers','wi-day-rain','wi-night-alt-rain'),
	(22,'81','Moderate rain showers','wi-day-rain','wi-night-alt-rain'),
	(23,'82','Violent rain showers','wi-day-rain','wi-night-alt-rain'),
	(24,'85','Slight snow showers','wi-day-snow','wi-night-alt-snow'),
	(25,'86','Heavy snow showers','wi-day-snow','wi-night-alt-snow'),
	(26,'95','Thunderstorms','wi-day-lightning','wi-night-alt-lightning'),
	(27,'96','Thunderstorms with slight hail','wi-day-sleet-storm','wi-night-alt-snow-thunderstorm'),
	(28,'99','Thunderstorms with heavy hail','wi-day-sleet-storm','wi-night-alt-snow-thunderstorm');

/*!40000 ALTER TABLE `weather_codes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table webhook_clicksend
# ------------------------------------------------------------

CREATE TABLE `webhook_clicksend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `message_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;



# Dump of table webhook_twilio
# ------------------------------------------------------------

CREATE TABLE `webhook_twilio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ToCountry` varchar(50) DEFAULT NULL,
  `ToState` varchar(50) DEFAULT NULL,
  `SmsMessageSid` varchar(50) DEFAULT NULL,
  `NumMedia` int(11) DEFAULT NULL,
  `ToCity` varchar(50) DEFAULT NULL,
  `FromZip` varchar(50) DEFAULT NULL,
  `SmsSid` varchar(50) DEFAULT NULL,
  `OptOutType` varchar(50) DEFAULT NULL,
  `FromState` varchar(50) DEFAULT NULL,
  `SmsStatus` varchar(50) DEFAULT NULL,
  `FromCity` varchar(50) DEFAULT NULL,
  `Body` text DEFAULT NULL,
  `FromCountry` varchar(50) DEFAULT NULL,
  `To` varchar(50) DEFAULT NULL,
  `MessagingServiceSid` varchar(50) DEFAULT NULL,
  `ToZip` varchar(50) DEFAULT NULL,
  `NumSegments` int(11) DEFAULT NULL,
  `MessageSid` varchar(50) DEFAULT NULL,
  `AccountSid` varchar(50) DEFAULT NULL,
  `From` varchar(50) DEFAULT NULL,
  `ApiVersion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
