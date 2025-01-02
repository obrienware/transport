<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=invite.ics');
// echo '<pre>';

require_once 'class.trip.php';
require_once 'class.ics.php';
$trip = new Trip($_REQUEST['id']);

$description = "";
$description .= "Using ".$trip->vehicle->name.": ".$trip->vehiclePUOptions.' - '.$trip->vehicleDOOptions."\\n\\n";
$description .= "PU ".$trip->guests." at ".$trip->puLocation->name."\\n\\n";
$description .= "DO ".$trip->doLocation->name."\\n\\n";
if ($trip->flightNumber) {
  $description .= "Flight ".$trip->airline->name." ".$trip->airline->flightNumberPrefix.$trip->flightNumber." ";
  if ($trip->ETA) {
    $description .= "ETA ".Date('g:ia', strtotime($trip->ETA))."\\n\\n";
  } else {
    $description .= "ETD ".Date('g:ia', strtotime($trip->ETD))."\\n\\n";
  }
}
$description .= "Contact: ".$trip->guest->getName()." ".$trip->guest->phoneNumber."\\n\\n";
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
  'url' => 'https://'.$_SERVER['HTTP_HOST'].'/print.trip-driver-sheet.php?id='.$trip->getId()
]);

echo $ics->to_string();