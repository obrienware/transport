<?php
require_once 'class.data.php';
if (!$db) $db = new data();

function countWaypoints($tripId) {
  global $db;
  $sql = "SELECT COUNT(*) FROM trip_waypoints WHERE trip_id = :trip_id";
  $data = ['trip_id' => $tripId];
  return $db->get_var($sql, $data);
}

function getCurrentWaypointSequence($tripId) {
  global $db;
  $sql = "SELECT seq FROM trip_waypoints WHERE trip_id = :trip_id AND reached IS NULL ORDER BY seq LIMIT 1";
  $data = ['trip_id' => $tripId];
  return $db->get_var($sql, $data);
}

function getWaypoint($tripId, $seq) {
  global $db;
  $sql = "
    SELECT 
      w.*,
      CASE WHEN l.short_name IS NULL THEN l.name ELSE l.short_name END AS location,
      l.lat, l.lon
    FROM trip_waypoints w
    LEFT OUTER JOIN locations l ON l.id = w.location_id
    WHERE 
      w.trip_id = :trip_id 
      AND w.seq = :seq";
  $data = ['trip_id' => $tripId, 'seq' => $seq];
  return $db->get_row($sql, $data);
}