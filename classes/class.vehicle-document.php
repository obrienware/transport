<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();

class VehicleDocument
{
  private $documentId;
  private $row;

  public function __construct($documentId)
  {
    if ($documentId) $this->getDocument($documentId);
  }

  public function getDocument($documentId)
  {
    global $db;
    $sql = "SELECT * FROM vehicle_documents WHERE id = :id";
    $data = ['id' => $documentId];
    if ($row = $db->get_row($sql, $data)) {
      $this->row = $row;
      return true;
    }
    return false;
  }

  static public function getDocuments($vehicleId)
  {
    global $db;
    $sql = "SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND archived IS NULL ORDER BY uploaded";
    $data = ['vehicle_id' => $vehicleId];
    return $db->get_results($sql, $data);
  }
}