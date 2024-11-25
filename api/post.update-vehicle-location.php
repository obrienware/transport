<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$json = json_decode(file_get_contents("php://input"));

$db = new data();

$sql = "
  INSERT INTO vehicle_locations SET
    datetimestamp = NOW(),
    vehicle_id = :vehicle_id,
    location_id = :location_id,
    fuel_level = :fuel_level,
    mileage = :mileage,
    clean_exterior = :clean_exterior,
    clean_interior = :clean_interior,
    needs_restocking = :needs_restocking
";
$data = [
  'vehicle_id' => $json->vehicleId,
  'location_id' => $json->locationId,
  'fuel_level' => $json->fuelLevel,
  'mileage' => $json->mileage ?: NULL,
  'clean_exterior' => $json->cleanExterior ? 1 : 0,
  'clean_interior' => $json->cleanInterior ? 1 : 0,
  'needs_restocking' => $json->needsRestocking ? 1 : 0
];
$result = $db->query($sql, $data);

if ($json->mileage) {
  $sql = "UPDATE vehicles SET mileage = :mileage WHERE id = :vehicle_id";
  $data = ['mileage' => $json->mileage, 'vehicle_id' => $json->vehicleId];
  $db->query($sql, $data);
}

echo json_encode(['result' => $result]);
