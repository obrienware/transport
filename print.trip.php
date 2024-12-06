<?php
require_once 'class.qrcode.php';
require_once 'class.pdf.php';
require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);

$pdf = new PDF('P', 'pt', 'letter');
$pdf->AddPage();
$pageWidth = $pdf->GetPageWidth() - $pdf->lm - $pdf->rm;

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
  
  $pdf->SetFont('Helvetica', '', 11);
  $pdf->SetAligns(['L', 'L']);
  $pdf->SetWidths([150, $pageWidth - 150]);
  $pdf->Row(['Airline', $trip->airline->name]);
  $pdf->Row(['Flight Number', $trip->airline->flightNumberPrefix.' '.$trip->flightNumber]);
  if ($trip->ETA) {
    $pdf->Row(['ETA', Date('g:i a', strtotime($trip->ETA))]);
  } else {
    $pdf->Row(['ETD', Date('g:i a', strtotime($trip->ETD))]);
  }
  $pdf->ln();
}

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Driver Notes', 1, 30, 'L', true);
$pdf->SetFont('Helvetica', '', 11);
$pdf->MultiCell($pageWidth, 30, $trip->driverNotes, 1);

$qrcode = new QRcode('https://transport.obrienware.com/opt-in');
$qrcode->disableBorder();
$pdf->SetY(-160);
$qrcode->displayFPDF($pdf, $pdf->GetPageWidth() - $pdf->rm - 100, $pdf->GetY(), 100);

$pdf->SetY(-160);
$pdf->Cell($pageWidth - 110, 30, 'SCAN to opt-in to text updates', 0, 30, 'R', false);

$pdf->output('I', 'Trip.pdf');