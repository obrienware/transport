<?php
require_once 'class.qrcode.php';
require_once 'class.pdf.php';
require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);

$pdf = new PDF('P', 'pt', 'letter');
$pdf->AddPage();
$pageWidth = $pdf->GetPageWidth() - $pdf->lm - $pdf->rm;
$pdf->setFillColor(230, 230, 230);

$pdf->Image(dirname(__FILE__).'/images/organization/logo.png', $pdf->GetPageWidth() - $pdf->rm - 250, $pdf->GetY(), 250, 0, 'PNG');
$pdf->SetY(60);


$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell($pageWidth, $pdf->row_height, 'Transport: Driver Trip Sheet'); 
$pdf->ln(30);

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->setFillColor(230, 230, 230);
$pdf->Cell($pageWidth, 30, $trip->summary, 1, 30, 'L', true);

$pdf->SetFont('Helvetica', '', 11);
$pdf->SetAligns(['L', 'L']);
$pdf->SetWidths([150, $pageWidth - 150]);
$pdf->Row(['Driver', $trip->driver->getName()]);
$pdf->Row(['Vehicle', $trip->vehicle->name."\n".$trip->vehiclePUOptions.' - '.$trip->vehicleDOOptions]);
$pdf->Row(['Trip start', Date('D M j @ g:ia', strtotime($trip->startDate))]);
$pdf->ln();

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Pick Up', 1, 30, 'L', true); 

$pdf->SetFont('Helvetica', '', 11);
$pdf->SetAligns(['L', 'L']);
$pdf->SetWidths([150, $pageWidth - 150]);
$pdf->Row(['Date/Time', Date('D M j @ g:ia', strtotime($trip->pickupDate))]);
$pdf->Row(['Who', $trip->guests]);
$pdf->Row(['Contact Person', $trip->guest->getName().' '.$trip->guest->phoneNumber]);
$pdf->Row(['Where', $trip->puLocation->name."\n".$trip->puLocation->mapAddress]);
$pdf->ln();

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Drop Off', 1, 30, 'L', true); 

$pdf->SetFont('Helvetica', '', 11);
$pdf->SetAligns(['L', 'L']);
$pdf->SetWidths([150, $pageWidth - 150]);
$pdf->Row(['Where', $trip->doLocation->name."\n".$trip->doLocation->mapAddress]);
$pdf->ln();

if ($trip->flightNumber) {
  $pdf->SetFont('Helvetica', 'B', 14);
  $pdf->Cell($pageWidth, 30, 'Flight Info', 1, 30, 'L', true); 

  $pdf->Rect($pdf->GetX(), $pdf->GetY(), $pageWidth, 3*17);
  if ($trip->airline->imageFilename) {
    $pdf->Image(dirname(__FILE__).'/images/airlines/'.$trip->airline->imageFilename, $pdf->GetX() +5, $pdf->GetY() +10, 140, 0);
  }

  $pdf->SetFont('Helvetica', '', 11);
  $pdf->SetAligns(['L', 'L']);
  $pdf->SetWidths([100, $pageWidth - 100 - 152]);
  $pdf->SetX($pdf->lm + 150);
  $pdf->Row(['Airline', $trip->airline->name]);
  $pdf->SetX($pdf->lm + 150);
  $pdf->Row(['Flight Number', $trip->airline->flightNumberPrefix.' '.$trip->flightNumber]);
  $pdf->SetX($pdf->lm + 150);
  if ($trip->ETA) {
    $pdf->Row(['ETA', Date('g:i a', strtotime($trip->ETA)).' - '.$trip->airport->name]);
  } else {
    $pdf->Row(['ETD', Date('g:i a', strtotime($trip->ETD)).' - '.$trip->airport->name]);
  }
  $pdf->ln();
}

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Driver Notes', 1, 30, 'L', true);
$pdf->SetFont('Helvetica', '', 11);
$pdf->MultiCell($pageWidth, 30, $trip->driverNotes, 1);

$pdf->output('I', 'DriverTripSheet.pdf');