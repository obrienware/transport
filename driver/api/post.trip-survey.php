<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));

$cleanExterior = NULL;
$cleanInterior = NULL;
if ($json->exteriorClean === false) $cleanExterior = 0;
if ($json->exteriorClean === true) $cleanExterior = 1;
if ($json->interiorClean === false) $cleanInterior = 0;
if ($json->interiorClean === true) $cleanInterior = 1;


require_once 'class.data.php';
$db = new data();
$sql = "
  INSERT INTO trip_surveys SET
    trip_id = :trip_id,
    datetimestamp = NOW(),
    rating_trip = :rating_trip,
    rating_weather = :rating_weather,
    rating_road = :rating_road,
    vehicle_mileage = :vehicle_mileage,
    vehicle_fuel = :vehicle_fuel,
    vehicle_clean_interior = :vehicle_clean_interior,
    vehicle_clean_exterior = :vehicle_clean_exterior,
    vehicle_restock = :vehicle_restock,
    vehicle_issues = :vehicle_issues,
    guest_issues = :guest_issues,
    comments = :comments
";
$data = [
  'trip_id' => $json->tripId,
  'rating_trip' => $json->ratingTrip ?: NULL,
  'rating_weather' => $json->ratingWeather ?: NULL,
  'rating_road' => $json->ratingRoad ?: NULL,
  'vehicle_mileage' => $json->mileage ?: NULL,
  'vehicle_fuel' => $json->fuel ?: NULL,
  'vehicle_clean_interior' => $cleanInterior,
  'vehicle_clean_exterior' => $cleanExterior,
  'vehicle_restock' => $json->restock ? 1 : 0,
  'vehicle_issues' => $json->vehicleIssue ?: NULL,
  'guest_issues' => $json->guestIssue ?: NULL,
  'comments' => $json->comments ?: NULL
];
$result = $db->query($sql, $data);
echo json_encode($result);
ob_end_flush(); // No more output to the requestor

// Some information we need from the trip itself
require_once '../../classes/class.trip.php';
require_once '../../classes/class.vehicle.php';
$trip = new Trip($json->tripId);
$vehicle = new Vehicle($trip->vehicleId);

$sql = "
  INSERT INTO vehicle_locations SET
    datetimestamp = NOW(),
    vehicle_id = :vehicle_id,
    driver_id = :driver_id,
    location_id = :location_id,
    fuel_level = :fuel_level,
    mileage = :mileage,
    clean_exterior = :clean_exterior,
    clean_interior = :clean_interior,
    needs_restocking = :needs_restocking,
    concerns = :concerns,
    note = :note
";
$data = [
  'vehicle_id' => $trip->vehicleId,
  'driver_id' => $trip->driverId,
  'location_id' => $vehicle->stagingLocationId, // Presumably
  'fuel_level' => $json->fuel ?: NULL,
  'mileage' => $json->mileage ?: NULL,
  'clean_exterior' => $cleanExterior,
  'clean_interior' => $cleanInterior,
  'needs_restocking' => $json->restock ? 1 : 0,
  'concerns' => $json->vehicleIssue ?: NULL,
  'note' => $json->comments ?: NULL
];
$db->query($sql, $data);

if ($json->checkEngine) {
  $sql = "UPDATE vehicles SET check_engine = 1 WHERE id = :vehicle_id";
  $data = ['vehicle_id' => $trip->vehicleId];
  $db->query($sql, $data);
}

if ($json->mileage) {
  $sql = "UPDATE vehicles SET mileage = :mileage WHERE id = :vehicle_id";
  $data = [
    'mileage' => $json->mileage,
    'vehicle_id' => $trip->vehicleId
  ];
  $db->query($sql, $data);
}