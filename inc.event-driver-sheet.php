<?php
require_once 'autoload.php';

use Transport\Event;
use Transport\User;
use Transport\Vehicle;

if (!isset($event)) {
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
  $id = $id === false ? null : $id;
  $event = new Event($id);
}

$pdf = new PDF('P', 'pt', 'letter');
$pdf->AddPage();
$pageWidth = $pdf->GetPageWidth() - $pdf->lm - $pdf->rm;
$pdf->setFillColor(230, 230, 230);

$pdf->Image(dirname(__FILE__).'/images/organization/logo.png', $pdf->GetPageWidth() - $pdf->rm - 250, $pdf->GetY(), 250, 0, 'PNG');
$pdf->SetY(60);


$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell($pageWidth, $pdf->row_height, 'Transport: Driver Event Sheet'); 
$pdf->ln(30);

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->setFillColor(230, 230, 230);
$pdf->Cell($pageWidth, 30, $event->getName(), 1, 30, 'L', true);

$pdf->SetFont('Helvetica', '', 11);
$pdf->SetAligns(['L', 'L']);
$pdf->SetWidths([150, $pageWidth - 150]);

$listOfDrivers = [];
foreach ($event->drivers as $driverId) {
  $driver = new User($driverId);
  $listOfDrivers[] = $driver->getName();
}
$pdf->Row(['Drivers', implode("\n", $listOfDrivers)]);

$listOfVehicles = [];
foreach ($event->vehicles as $vehicleId) {
  $vehicle = new Vehicle($vehicleId);
  $listOfVehicles[] = $vehicle->name;
}
$pdf->Row(['Vehicles', implode("\n", $listOfVehicles)]);
$pdf->Row(['Event starts', Date('D M j @ g:ia', strtotime($event->startDate))]);
$pdf->Row(['Event ends', Date('D M j @ g:ia', strtotime($event->endDate))]);

if ($event->location) {
  $pdf->Row(['Location', $event->location->name]);
}
$pdf->ln();

if ($event->notes) {
  $pdf->SetFont('Helvetica', 'B', 14);
  $pdf->Cell($pageWidth, 30, 'Additional Notes', 1, 30, 'L', true);
  $pdf->SetFont('Helvetica', '', 11);
  $pdf->MultiCell($pageWidth, 18, $event->notes, 1);
}
