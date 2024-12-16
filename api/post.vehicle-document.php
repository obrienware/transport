<?php
require_once 'class.data.php';
$db = new data();

if (!empty($_FILES)) {
  $targetFilename = uniqid().'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  $targetFolder = dirname( __FILE__ ).'/../documents/';
  
  if ( !file_exists( $targetFolder ) || !is_dir( $targetFolder) ) {
    mkdir("$targetFolder");
    chmod("$targetFolder", 0755);
  }
  if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFolder.$targetFilename)) {
    $sql = "
      INSERT INTO vehicle_documents SET 
        vehicle_id = :vehicle_id,
        name = :document_name,
        filename = :filename,
        file_type = :file_type,
        uploaded = NOW(),
        uploaded_by = :uploaded_by
    ";
    $data = [
      'vehicle_id' => $_POST['vehicleId'],
      'document_name' => $_POST['documentName'],
      'filename' => $targetFilename,
      'file_type' => $_FILES['file']['type'],
      'uploaded_by' => $_SESSION['user']->username
    ];
    $db->query($sql, $data);
  }
}