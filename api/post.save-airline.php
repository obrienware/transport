<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.airline.php';

// print_r($_POST);
// print_r($_FILES);
// die();
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
$previousName = $airline->name;
$airline->name = $_POST['name'];
$airline->flightNumberPrefix = $_POST['flightNumberPrefix'];
if (isset($targetFilename)) {
  $airline->imageFilename = $targetFilename;
}

$before = $airline->getState();
$result = $airline->save();
if ($_POST['id']) {
  $action = 'modified';
  $description = 'Modified airline: '.$previousName;
} else {
  $action = 'added';
  $description = 'Added airline: '.$airline->name;
}
$after = $airline->getState();
Audit::log($action, 'airlines', $description, $before, $after);
echo json_encode(['result' => $result]);