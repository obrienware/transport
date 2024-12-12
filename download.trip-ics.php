<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=invite.ics');

require_once 'class.trip.php';
require_once 'class.ics.php';
$trip = new Trip($_REQUEST['id']);

$description = "";
$description .= "Using ".$trip->vehicle->name.": ".$trip->vehiclePUOptions.' - '.$trip->vehicleDOOptions."\\n";
$description .= "PU ".$trip->guests." at ".$trip->puLocation->name."\\n";
$description .= "DO ".$trip->doLocation->name."\\n";
if ($trip->flightNumber) {
  $description .= "Flight ".$trip->airline->name." ".$trip->airline->flightNumberPrefix.$trip->flightNumber." ";
  if ($trip->ETA) {
    $description .= "ETA ".Date('i:ga', strtotime($trip->ETA))."\\n";
  } else {
    $description .= "ETD ".Date('i:ga', strtotime($trip->ETD))."\\n";
  }
}
$description .= "Contact: ".$trip->guest->getName()." ".$trip->guest->phoneNumber."\\n";
if ($trip->driverNotes) {
  $description .= "Additional Driver Notes:\\n";
  $description .= str_replace("\n", "\\n", $trip->driverNotes)."\\n";
}

$ics = new ICS([
  'dtstart' => $trip->startDate,
  'dtend' => $trip->endDate,
  'description' => $description,
  'summary' => $trip->summary,
  'location' => str_replace("\n", "\\n", $trip->puLocation->mapAddress),
  'X-WR-TIMEZONE' => $_ENV['TZ'] ?: 'America/Denver'
]);

echo $ics->to_string();