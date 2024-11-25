<?php
header('Content-Type: application/json');
require_once 'class.trip.php';
$sourceTrip = new Trip($_REQUEST['id']);
$targetTrip = new Trip();

$targetTrip->summary = $sourceTrip->summary.' (copy)';
$targetTrip->requestorId = $sourceTrip->requestorId;
$targetTrip->startDate = $sourceTrip->startDate;
$targetTrip->endDate = $sourceTrip->endDate;
$targetTrip->guestId = $sourceTrip->guestId;
$targetTrip->passengers = $sourceTrip->passengers;
$targetTrip->puLocationId = $sourceTrip->puLocationId;
$targetTrip->doLocationId = $sourceTrip->doLocationId;
$targetTrip->driverId = $sourceTrip->driverId;
$targetTrip->vehicleId = $sourceTrip->vehicleId;
$targetTrip->airlineId = $sourceTrip->airlineId;
$targetTrip->flightNumber = $sourceTrip->flightNumber;
$targetTrip->vehiclePUOptions = $sourceTrip->vehiclePUOptions;
$targetTrip->vehicleDOOptions = $sourceTrip->vehicleDOOptions;
$targetTrip->ETA = $sourceTrip->ETA;
$targetTrip->ETD = $sourceTrip->ETD;
$targetTrip->IATA = $sourceTrip->IATA;
$targetTrip->guestNotes = $sourceTrip->guestNotes;
$targetTrip->driverNotes = $sourceTrip->driverNotes;
$targetTrip->generalNotes = $sourceTrip->generalNotes;
$targetTrip->finalized = $sourceTrip->finalized;

die(json_encode($targetTrip->save()));