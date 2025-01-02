<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.location.php';
require_once 'class.user.php';

class Event
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

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
  public $cancelled;
  public $originalRequest;

  
  public function __construct($id = null)
  {
		$this->db = new data();
    if ($id) $this->load($id);
  }


  public function load(int $id): bool
  {
	 $query = "SELECT * FROM events WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
      $this->name = $row->name;
      $this->requestorId = $row->requestor_id;
      $this->locationId = $row->location_id;
      $this->startDate = $row->start_date;
      $this->endDate = $row->end_date;
      $this->drivers = explode(',', $row->driver_ids);
      $this->vehicles = explode(',', $row->vehicle_ids);
      $this->notes = $row->notes;
      $this->confirmed = $row->confirmed;
      $this->originalRequest = $row->original_request;
      $this->cancelled = $row->cancellation_requested;

      if ($this->requestorId) $this->requestor = new User($this->requestorId);
      if ($this->locationId) $this->location = new Location($this->locationId);
      $this->archived = $row->archived;
			return true;
		}
		return false;
  }


	public function getId(): int | null
	{
		return $this->id;
	}


	public function save(string $user = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $user;
		$audit->action = $this->action;
		$audit->table = 'events';
		$audit->before = json_encode($this->row);

    $params = [
      'name' => $this->name,
      'requestor_id' => $this->requestorId,
      'location_id' => $this->locationId,
      'start_date' => $this->startDate,
      'end_date' => $this->endDate,
      'driver_ids' => implode(',', $this->drivers),
      'vehicle_ids' => implode(',', $this->vehicles),
      'notes' => $this->notes,
      'user' => $user
    ];

    if ($this->action === 'update') {
			$audit->description = 'Event updated: '.$this->name;
      $params['id'] = $this->id;
      $query = "
        UPDATE events SET
          name = :name,
          requestor_id = :requestor_id,
          location_id = :location_id,
          start_date = :start_date,
          end_date = :end_date,
          driver_ids = :driver_ids,
          vehicle_ids = :vehicle_ids,
          notes = :notes,
          modified = NOW(),
          modified_by = :user
        WHERE id = :id
      ";
    } else {
			$audit->description = 'Event created: '.$this->name;
      $query = "
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
    }
		try {
			$result = $this->db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
      if ($this->originalRequest) $this->db->query('UPDATE events SET original_request = :original_request WHERE id = :id', ['original_request' => $this->originalRequest, 'id' => $id]);
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
  }


	public function delete(string $user = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $user;
		$audit->action = 'delete';
		$audit->table = 'events';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE events 
			SET 
				archived = NOW(), archived_by = :user 
			WHERE id = :id
		";
		$params = [
			'user' => $user, 
			'id' => $this->id
		];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Event deleted: '.$this->name;
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


  private function reset()
  {
    $this->id = null;
    $this->row = null;
    $this->lastError = null;
    $this->action = 'create';
    $this->archived = null;

    $this->name = null;
    $this->requestorId = null;
    $this->requestor = null;
    $this->locationId = null;
    $this->location = null;
    $this->startDate = null;
    $this->endDate = null;
    $this->drivers = [];
    $this->vehicles = [];
    $this->notes = null;
    $this->confirmed = null;
    $this->cancelled = null;
    $this->originalRequest = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
  }


  public function getLastError(): string
	{
		return $this->lastError;
	}


  public function confirm(string $user = null): bool
	{
		$audit = new Audit();
		$audit->user = $user;
		$audit->action = 'update';
		$audit->table = 'events';
		$audit->before = json_encode($this->row);
	
		$query = 'UPDATE events SET confirmed = NOW() WHERE id = :id';
		$params = ['id' => $this->id];
		$this->db->query($query, $params);

		$audit->description = 'Event confirmed: '.$this->name;
		$this->load($this->id);
		$audit->after = json_encode($this->row);
		$audit->commit();
		return true;
	}
  

	public function cancel(string $user = null): bool
	{
		$audit = new Audit();
    $audit->user = $user;
		$audit->action = 'update';
		$audit->table = 'events';
		$audit->before = json_encode($this->row);

		$query = 'UPDATE events SET cancellation_requested = NOW() WHERE id = :event_id';
		$params = ['event_id' => $this->id];
		$result = $this->db->query($query, $params);

		$audit->description = 'Event cancellation requested: '.$this->name;
		$this->load($this->id);
		$audit->after = json_encode($this->row);
		$audit->commit();
		return true;
	}


	public function isArchived(): bool
	{
		return isset($this->archived);
	}


	public function isEditable(): bool
	{
		if (!$this->endDate) return true;
    if (!$this->confirmed) return true;
		return !(strtotime($this->endDate) <= strtotime('now'));
	}


	public function isConfirmed(): bool
	{
		return $this->confirmed ? true : false;
	}


  public function isCancelled(): bool
  {
    return $this->cancelled ? true : false;
  }


  static public function nextEventByVehicle(int $vehicleId): int | null
  {
    $db = new data();
    $query = "
      SELECT id FROM events 
      WHERE  NOW() < start_date AND FIND_IN_SET(:vehicle_id, vehicle_ids)
      ORDER BY start_date
      LIMIT 1
    ";
    $params = ['vehicle_id' => $vehicleId];
    return $db->get_var($query, $params);
  }

}