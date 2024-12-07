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
$pdf->Cell($pageWidth, $pdf->row_height, 'Transportation: Guest Sheet'); 
$pdf->ln(30);

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Your Driver', 1, 30, 'L', true);
$pdf->SetFont('Helvetica', '', 11);
$pdf->ln(2);
$saveY = $pdf->GetY();
if ($trip->driver->username) {
  $pdf->Image(dirname(__FILE__).'/images/drivers/'.$trip->driver->username.'.jpg', $pdf->GetX(), $pdf->GetY(), 100, 0, 'JPG');
}
$pdf->SetX($pdf->GetX() + 110);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->MultiCell($pageWidth - 110, 20, $trip->driver->getName(), 0, 'L', false);
$pdf->SetFont('Helvetica', '', 11);
$pdf->SetX($pdf->GetX() + 110);
$pdf->MultiCell($pageWidth - 110, 20, $trip->driver->phoneNumber, 0, 'L', false);

$xCenter = $pdf->GetPageWidth() /2;
$pdf->SetFont('Helvetica', '', 11);
$pdf->SetY($saveY);
$pdf->SetX($xCenter);
$pdf->MultiCell($xCenter - $pdf->rm, 20, 'Vehicle: '.$trip->vehicle->description, 0, 'L', false);



$pdf->SetY($saveY + 120);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Pick up information', 1, 1, 'L', true); $pdf->SetY($pdf->GetY() - 30);
$pdf->Cell($pageWidth, 30, Date('D F j', strtotime($trip->pickupDate)), 1, 1, 'R', false);
$pdf->SetFont('Helvetica', '', 11);
// $instructions = Date('D F j', strtotime($trip->pickupDate))."\n\n";
if ($trip->ETA) {
  // We will be picking up the guest(s) from the airport
  $instructions .= "Your flight is estimated to arrive at ".Date('g:ia', strtotime($trip->ETA))." (local time) at ".$trip->airport->name.".\n\n";
  if ((int)$trip->passengers > 4) {
    $instructions .= $trip->airport->arrivalInstructionsGroup;
  } else {
    $instructions .= "While you are more than welcome to keep your driver updated, we will be monitoring your flight so we'll know about any changes to your flight and when you've landed. :-)\n\n";
    $instructions .= $trip->airport->arrivalInstructions;
  }
} else {
  $instructions .= "Your driver will be at your pick up location (".$trip->puLocation->name.") shortly before ".Date('g:ia', strtotime($trip->pickupDate));
}
$pdf->MultiCell($pageWidth, 18, $instructions, 1, 'L');
$pdf->ln();


$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Drop off information', 1, 1, 'L', true);
$pdf->SetFont('Helvetica', '', 11);
$info = "Your driver will take you to ".$trip->doLocation->name;
if (!$trip->ETD && (int)$trip->passengers <=4) {
  // Not going to the airport and not a group
  $info .= "\n\nWe are here to serve you. Please feel free to ask your driver to stop if you need a bite to eat, or anything. Please help yourself to snacks and refreshments in the vehicle.";
}
$pdf->MultiCell($pageWidth, 18, $info, 1, 'L');
$pdf->ln();

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($pageWidth, 30, 'Guest Notes', 1, 30, 'L', true);
$pdf->SetFont('Helvetica', '', 11);
$pdf->MultiCell($pageWidth, 18, $trip->guestNotes, 1);

$qrcode = new QRcode('https://transport.obrienware.com/opt-in');
$qrcode->disableBorder();
$pdf->SetY(-160);
$qrcode->displayFPDF($pdf, $pdf->GetPageWidth() - $pdf->rm - 100, $pdf->GetY(), 100);

$pdf->SetY(-160);
$pdf->MultiCell($pageWidth - 110, 15, "We aim to provide excellent service.\nWe can text you real-time updates\n regarding your pickup.\n\nPlease SCAN to opt-in to text updates", 0,'R', false);

$pdf->output('I', 'DriverTripSheet.pdf');