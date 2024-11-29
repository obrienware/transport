<?php

require_once 'class.data.php';
if (!isset($db)) $db = new data();


class Snag
{
  private $snagId;
  public $vehicleId;
  public $createdBy;
  public $dateTimeStamp;
  public $description;
  public $acknowledged;
  public $acknowledgedBy;
  public $resolved; // Date/time the issue was resolved
  public $resolution; // What was done to resolve the issue
  public $resolvedBy; // Person who resolved the issue
  public $comments; // In case you need to monitor the situation for example.

  public function __construct($snagId = null)
  {
    if ($snagId) {
      $this->getSnag($snagId);
    }
  }

  public function getSnag(int $snagId): bool
  {
    global $db;
    $sql = "SELECT * FROM snags WHERE id = :snag_id";
    $data = ['snag_id' => $snagId];
    if ($item = $db->get_row($sql, $data)) {
      $this->snagId = $item->id;
      $this->vehicleId = $item->vehicle_id;
      $this->dateTimeStamp = $item->datetimestamp;
      $this->createdBy = $item->created_by;
      $this->description = $item->description;
      $this->acknowledged = $item->acknowledged;
      $this->acknowledgedBy = $item->acknowledged_by;
      $this->resolved = $item->resolved;
      $this->resolvedBy = $item->resolved_by;
      $this->resolution = $item->resolution;
      $this->comments = $item->comments;
      return true;
    }
    return false;
  }

  public function getId(): int
  {
    return $this->snagId;
  }

  public static function getSnags($vehicleId = null) {
    global $db;
    $data = [];
    if ($vehicleId) {
      $sql = "SELECT * FROM snags WHERE vehicle_id = :vehicle_id ORDER BY datetimestamp";
      $data = ['vehicle_id' => $vehicleId];
    } else {
      $sql = "
        SELECT s.*, v.name AS vehicle
        FROM snags s
        LEFT OUTER JOIN vehicles v ON v.id = s.vehicle_id
        WHERE 
          s.archived IS NULL 
        ORDER BY v.name, s.datetimestamp
      ";
    }
    return $db->get_results($sql, $data);
  }

  public function save()
  {
    global $db;
    $data = [
      'vehicle_id' => $this->vehicleId,
      'created_by' => $this->createdBy,
      'description' => $this->description,
      'acknowledged' => $this->acknowledged,
      'acknowledged_by' => $this->acknowledgedBy,
      'resolved' => $this->resolved,
      'resolved_by' => $this->resolvedBy,
      'resolution' => $this->resolution,
      'comments' => $this->comments
    ];
    if ($this->snagId) {
      $data['id'] = $this->snagId;
      $data['datetimestamp'] = $this->dateTimeStamp;
      $sql = "
        UPDATE snags SET  
          vehicle_id = :vehicle_id,
          datetimestamp = :datetimestamp,
          created_by = :created_by,
          description = :description,
          acknowledged = :acknowledged,
          acknowledged_by = :acknowledged_by,
          resolved = :resolved,
          resolved_by = :resolved_by,
          resolution = :resolution,
          comments = :comments
        WHERE id = :id
      ";
    } else {
      $sql ="
        INSERT INTO snags SET
          vehicle_id = :vehicle_id,
          datetimestamp = NOW(),
          created_by = :created_by,
          description = :description,
          acknowledged = :acknowledged,
          acknowledged_by = :acknowledged_by,
          resolved = :resolved,
          resolved_by = :resolved_by,
          resolution = :resolution,
          comments = :comments
      ";
    }
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
  }

	static public function delete($snagId)
	{
		global $db;
		$sql = 'UPDATE snags SET archived = NOW(), archived_by = :user WHERE id = :snag_id';
		$data = ['user' => $_SESSION['user']->username, 'snag_id' => $snagId];
		return $db->query($sql, $data);
	}
}