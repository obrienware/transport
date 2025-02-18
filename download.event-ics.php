<?php
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=invite.ics');

require_once 'autoload.php';

use Transport\Event;
use Transport\User;
use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$event = new Event($id);

$listOfDrivers = [];
foreach ($event->drivers as $driverId) {
  $driver = new User($driverId);
  $listOfDrivers[] = $driver->getName();
}
$description .= "Drivers: ".implode(", ", $listOfDrivers)."\\n\\n";

$listOfVehicles = [];
foreach ($event->vehicles as $vehicleId) {
  $vehicle = new Vehicle($vehicleId);
  $listOfVehicles[] = $vehicle->name;
}
$description .= "Vehicles: ".implode(", ", $listOfVehicles)."\\n\\n";

if ($event->notes) {
  $description .= "Additional Notes:\\n";
  $description .= str_replace("\n", "\\n", $event->notes)."\\n";
}

$icsOptions = [
  'dtstart' => $event->startDate,
  'dtend' => $event->endDate,
  'description' => $description,
  'summary' => 'EVENT: '.$event->name,
  'url' => 'https://'.$_SERVER['HTTP_HOST'].'/print.event-driver-sheet.php?id='.$event->getId()
];
if ($event->location) {
  $icsOptions['location'] = str_replace("\n", "\\n", $event->location->mapAddress);
}

$ics = new ICS($icsOptions);

echo $ics->to_string();