<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Transport\User;

$user = new User($_SESSION['user']->id);


if (!empty($_FILES)) {
  $targetFilename = uniqid().'.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
  $targetFolder = dirname( __FILE__ ).'/../images/airlines/';
  
  if ( !file_exists( $targetFolder ) || !is_dir( $targetFolder) ) {
    mkdir($targetFolder, 0755, true);
    chmod("$targetFolder", 0755);
  }
  if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFolder.$targetFilename)) {
    exit(json_encode(['result' => false, 'error' => 'Failed to move uploaded file.']));
  }
}

$airline = new Airline($_POST['id']);
$airline->name = $_POST['name'];
$airline->flightNumberPrefix = $_POST['flightNumberPrefix'];
if (isset($targetFilename)) $airline->imageFilename = $targetFilename;

if ($airline->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $airline->getId()]));
}

exit(json_encode(['result' => false, 'error' => $airline->getLastError()]));