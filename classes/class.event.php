<?php

require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}

class Event
{
  private $eventId;
  public $name;
  public $requestorId;
  public $requestor;
  public $locationId;
  public $location;
  public $startDate;
  public $endDate;
  public $drivers;
  public $vehicles;
  public $notes;

  public function __construct($eventId = null)
  {
    $this->drivers = [];
    $this->vehicles = [];
    if ($eventId) $this->getEvent($eventId);
  }

  public function getEvent(int $eventId)
  {
		global $db;
		$sql = "
      SELECT e.*, l.name AS location, CONCAT(first_name,' ',last_name) AS requestor FROM events e
      LEFT OUTER JOIN locations l ON l.id = e.location_id
      LEFT OUTER JOIN users r ON r.id = e.requestor_id
      WHERE e.id = :event_id
    ";
		$data = ['event_id' => $eventId];
		if ($item = $db->get_row($sql, $data)) {
			$this->eventId = $item->id;
      $this->name = $item->name;
      $this->requestorId = $item->requestor_id;
      $this->requestor = $item->requestor;
      $this->locationId = $item->location_id;
      $this->location = $item->location;
      $this->startDate = $item->start_date;
      $this->endDate = $item->end_date;
      $this->drivers = explode(',', $item->driver_ids);
      $this->vehicles = explode(',', $item->vehicle_ids);
      $this->notes = $item->notes;
			return true;
		}
		return false;
  }

  public function getId()
  {
    return $this->eventId;
  }

  public function save()
  {
    global $db;
    $data = [
      'name' => $this->name,
      'requestor_id' => $this->requestorId,
      'location_id' => $this->locationId,
      'start_date' => $this->startDate,
      'end_date' => $this->endDate,
      'driver_ids' => implode(',', $this->drivers),
      'vehicle_ids' => implode(',', $this->vehicles),
      'notes' => $this->notes
    ];
    if ($this->eventId) {
      // Update
      $data['id'] = $this->eventId;
      $sql = "
        UPDATE events SET
          name = :name,
          requestor_id = :requestor_id,
          location_id = :location_id,
          start_date = :start_date,
          end_date = :end_date,
          driver_ids = :driver_ids,
          vehicle_ids = :vehicle_ids,
          notes = :notes
        WHERE id = :id
      ";
    } else {
      // Insert
      $sql = "
        INSERT INTO events SET
          name = :name,
          requestor_id = :requestor_id,
          location_id = :location_id,
          start_date = :start_date,
          end_date = :end_date,
          driver_ids = :driver_ids,
          vehicle_ids = :vehicle_ids,
          notes = :notes,
          created = NOW(),
          created_by = :user
      ";
      $data['user'] = $_SESSION['user']->username;
    }
    $result = $db->query($sql, $data);
    return [
      'result' => $result,
      'errors' => $db->errorInfo
    ];
  }

	static function deleteEvent($eventId)
	{
		global $db;
		$sql = 'UPDATE events SET archived = NOW(), archived_by = :user WHERE id = :event_id';
		$data = ['user' => $_SESSION['user']->username, 'event_id' => $eventId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteEvent($this->eventId);
	}


}