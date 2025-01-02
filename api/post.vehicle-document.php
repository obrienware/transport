<?php
require_once 'class.vehicle-document.php';

if (!empty($_FILES)) {
  $targetFilename = uniqid().'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  $targetFolder = dirname( __FILE__ ).'/../documents/';
  
  if ( !file_exists( $targetFolder ) || !is_dir( $targetFolder) ) {
    mkdir("$targetFolder");
    chmod("$targetFolder", 0755);
  }
  if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFolder.$targetFilename)) {
    $doc = new VehicleDocument();
    $doc->vehicleId = $_POST['vehicleId'];
    $doc->name = $_POST['documentName'];
    $doc->filename = $targetFilename;
    $doc->fileType = $_FILES['file']['type'];
    if ($doc->save($_SESSION['user']->username)) {
      $result = $doc->getId();
      die(json_encode(['result' => $result]));
    }
  }
  die(json_encode(['error' => 'Failed to save document']));
}
die(json_encode(['error' => 'No file uploaded']));