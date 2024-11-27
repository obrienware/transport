<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));

require_once 'class.trip-survey.php';
require_once 'class.vehicle.php';

require_once 'class.snag.php';
require_once 'class.trip.php';
require_once 'class.vehicle.php';

$survey = new TripSurvey();
$survey->tripId = $json->tripId;
$survey->ratingRoad = $json->ratingRoad ?: NULL;
$survey->ratingTrip = $json->ratingTrip ?: NULL;
$survey->ratingWeather = $json->ratingWeather ?: NULL;
$survey->guestIssues = $json->guestIssue ?: NULL;
$survey->comments = $json->comments ?: NULL;
$surveyId = $survey->save();

$trip = new Trip($json->tripId);

$vehicle = new Vehicle($trip->vehicleId);
$vehicle->lastUpdate = Date('Y-m-d H:i:s'); // Basically now...
$vehicle->lastUpdatedBy = $_SESSION['user']->id;
if ($json->locationId) $vehicle->locationId = $json->locationId;
if ($json->fuel) $vehicle->fuelLevel = $json->fuel;
if ($json->mileage) $vehicle->mileage = $json->mileage;
if ($json->checkEngine) $vehicle->hasCheckEngine = 1;
if ($json->exteriorClean === false) $vehicle->cleanExterior = 1;
if ($json->interiorClean === false) $vehicle->cleanInterior = 1;
if ($json->restock) $vehicle->restock = 1;
$vehicle->save();

if ($json->vehicleIssue) {
  $snag = new Snag();
  $snag->vehicleId = $trip->vehicleId;
  $snag->dateTimeStamp = Date('Y-m-d H:i:s'); // Basically now...
  $snag->createdBy = $_SESSION['user']->id;
  $snag->description = $json->vehicleIssue;
  $snag->save();
}

echo json_encode(['result' => true]);