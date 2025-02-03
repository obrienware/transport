<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\VehicleDocument;

$user = new User($_SESSION['user']->id);

if (!empty($_FILES)) {
  $targetFilename = uniqid().'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  $targetFolder = dirname( __FILE__ ).'/../documents/';
  
  if ( !file_exists( $targetFolder ) || !is_dir( $targetFolder) ) {
    if (!mkdir($targetFolder) && !is_dir($targetFolder)) {
      if (!chmod($targetFolder, 0755)) {
        exit(json_encode(['error' => 'Failed to set permissions on target folder']));
      }
    }
    chmod("$targetFolder", 0755);
  }

  if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFolder.$targetFilename)) {
    $doc = new VehicleDocument();
    $doc->vehicleId = $_POST['vehicleId'];
    $doc->name = $_POST['documentName'];
    $doc->filename = $targetFilename;
    $doc->fileType = $_FILES['file']['type'];
    if ($doc->save(userResponsibleForOperation: $user->getUsername())) {
      exit(json_encode(['result' => $doc->getId()]));
    }
    exit(json_encode(['result' => false, 'error' => $doc->getLastError()]));
  }
  exit(json_encode(['error' => 'Failed to save document']));
}
exit(json_encode(['error' => 'No file uploaded']));