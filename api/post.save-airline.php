<?php
header('Content-Type: application/json');
require_once 'class.airline.php';

if (!empty($_FILES)) {
  $targetFilename = uniqid().'.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
  $targetFolder = dirname( __FILE__ ).'/../images/airlines/';
  
  if ( !file_exists( $targetFolder ) || !is_dir( $targetFolder) ) {
    mkdir("$targetFolder");
    chmod("$targetFolder", 0755);
  }
  move_uploaded_file($_FILES['image']['tmp_name'], $targetFolder.$targetFilename);
}

if ($_POST['id']) {
  $airline = new Airline($_POST['id']);
} else {
  $airline = new Airline();
}
$airline->name = $_POST['name'];
$airline->flightNumberPrefix = $_POST['flightNumberPrefix'];
if (isset($targetFilename)) $airline->imageFilename = $targetFilename;

if ($airline->save()) {
  $result = $airline->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));