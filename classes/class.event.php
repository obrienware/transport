<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.location.php';
require_once 'class.user.php';

class Event
{
	private $db;
	private $row;
	private $action = 'create';
  private $eventId;

  public $name;
  public $requestorId;
  public $requestor;
  public $locationId;
  public $location;
  public $startDate;
  public $endDate;
  public $drivers = [];
  public $vehicles = [];
  public $notes;
  public $confirmed;
  public $originalRequest;

  public function __construct($eventId = null)
  {
		$this->db = new data();
    if ($eventId) $this->getEvent($eventId);
  }

  public function getEvent(int $eventId)
  {
	 $sql = "SELECT * FROM events WHERE id = :event_id";
		$data = ['event_id' => $eventId];
		if ($item = $this->db->get_row($sql, $data)) {
      $this->row = $item;
			$this->action = 'update';

			$this->eventId = $item->id;
      $this->name = $item->name;
      $this->requestorId = $item->requestor_id;
      $this->locationId = $item->location_id;
      $this->startDate = $item->start_date;
      $this->endDate = $item->end_date;
      $this->drivers = explode(',', $item->driver_ids);
      $this->vehicles = explode(',', $item->vehicle_ids);
      $this->notes = $item->notes;
      $this->confirmed = $item->confirmed;
      $this->originalRequest = $item->original_request;

      if ($this->requestorId) $this->requestor = new User($this->requestorId);
      if ($this->locationId) $this->location = new Location($this->locationId);
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
		$audit = new Audit();
		$audit->action = $this->action;
		$audit->table = 'events';
		$audit->before = json_encode($this->row);

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
    if ($this->action === 'update') {
			$audit->description = 'Event updated: '.$this->name;
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
			$audit->description = 'Event created: '.$this->name;
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
		try {
			$result = $this->db->query($sql, $data);
			$id = ($this->action === 'create') ? $result : $this->eventId;
			// We also want to add the original request if it exists here, but without creating a new audit entry
			if ($this->originalRequest) $this->db->query('UPDATE events SET original_request = :original_request WHERE id = :id', ['original_request' => $this->originalRequest, 'id' => $id]);
			$this->getEvent($id);
			$audit->after = json_encode($this->row);
			$audit->commit();

			return ['result' => $result];
		} catch (Exception $e) {
			return [
				'result' => false,
				'error' => $e->getMessage()
			];
		}
  }

	public function confirm()
	{
		$sql = 'UPDATE events SET confirmed = NOW() WHERE id = :event_id';
		$data = ['event_id' => $this->eventId];
		$result = $this->db->query($sql, $data);
		return $result;
	}

	public function delete()
	{
		$audit = new Audit();
		$audit->action = 'delete';
		$audit->table = 'events';
		$audit->before = json_encode($this->row);

		$sql = 'UPDATE events SET archived = NOW(), archived_by = :user WHERE id = :event_id';
		$data = ['user' => $_SESSION['user']->username, 'trip_id' => $this->eventId];
		$result = $this->db->query($sql, $data);
		$audit->description = 'Event deleted: '.$this->name;
		$audit->commit();
		return $result;
	}

	public function getState(): string
	{
		return json_encode($this->row);
	}

  static public function nextEventByVehicle(int $vehicleId)
  {
    $db = new data();
    $sql = "
      SELECT id FROM events 
      WHERE  NOW() < start_date AND FIND_IN_SET(:vehicle_id, vehicle_ids)
      ORDER BY start_date
      LIMIT 1
    ";
    $data = ['vehicle_id' => $vehicleId];
    return $db->get_var($sql, $data);
  }

}