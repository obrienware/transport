<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Event extends Base
{
  protected $tableName = 'events';
  protected $tableDescription = 'Events';

  public ?string $name = null;
  public ?int $requestorId = null;
  public ?User $requestor = null;
  public ?int $locationId = null;
  public ?Location $location = null;
  private ?DateTime $startDate = null;
  private ?DateTime $endDate = null;
  public array $drivers = [];
  public array $vehicles = [];
  public ?string $notes = null;
  private ?DateTime $confirmed = null;
  private ?DateTime $cancelled = null;
  public ?string $originalRequest = null;

  
  public function getName(): string
  {
    return is_null($this->name) ? 'no-name' : $this->name;
  }

  public function load(int $id): bool
  {
    $db = Database::getInstance();
    $query = "SELECT * FROM {$this->tableName} WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $db->get_row($query, $params)) {
      $this->mapRowToProperties($row);
			return true;
		}
		return false;
  }

  protected function mapRowToProperties(object $row): void
  {
    $utc = new DateTimeZone('UTC');
    $this->row = $row;
    $this->action = 'update';

    $this->id = $row->id;
    $this->name = $row->name;
    $this->requestorId = $row->requestor_id;
    $this->locationId = $row->location_id;
    $this->notes = $row->notes;
    // $this->originalRequest = $row->original_request;

    if ($row->requestor_id) {
      $this->requestor = new User($row->requestor_id);
    }
    if ($row->location_id) {
      $this->location = new Location($row->location_id);
    }
    if (!empty($row->drivers)) {
      $this->drivers = explode(',', $row->drivers);
    }
    if (!empty($row->vehicles)) {
      $this->vehicles = explode(',', $row->vehicles);
    }
    if (!empty($row->startDate)) {
      $this->startDate = (new DateTime($row->start_date, $utc))->setTimezone($this->timezone);
    }
    if (!empty($row->endDate)) {
      $this->endDate = (new DateTime($row->end_date, $utc))->setTimezone($this->timezone);
    }
    if (!empty($row->confirmed)) {
      $this->confirmed = (new DateTime($row->confirmed, $utc))->setTimezone($this->timezone);
    }
    if (!empty($row->cancellation_requested)) {
      $this->cancelled = (new DateTime($row->cancellation_requested, $utc))->setTimezone($this->timezone);
    }
    if (!empty($row->archived)) {
      $this->archived = (new DateTime($row->archived, $utc))->setTimezone($this->timezone);
    }
  }

  public function __set($name, $value)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'startDate':
        case 'endDate':
        case 'confirmed':
        case 'cancelled':
          if ($value instanceof DateTime) {
            $this->$name = $value;
            return;
          }
          $this->$name = is_null($value) ? null : new DateTime($value, $this->timezone);
          return;
      }
    }
  }
  
  public function __get($name)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'startDate':
        case 'endDate':
        case 'confirmed':
        case 'cancelled':
          return is_null($this->$name) ? null : $this->$name->format('Y-m-d H:i:s');
      }
    }
  }

	public function save(?string $userResponsibleForOperation = null): bool
	{
    $utc = new DateTimeZone('UTC');
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

    $params = [
      'name' => $this->name,
      'requestor_id' => $this->requestorId,
      'location_id' => $this->locationId,
      'start_date' => is_null($this->startDate) ? null : $this->startDate->setTimezone($utc)->format('Y-m-d H:i:s'),
      'end_date' => is_null($this->endDate) ? null : $this->endDate->setTimezone($utc)->format('Y-m-d H:i:s'),
      'driver_ids' => implode(',', $this->drivers),
      'vehicle_ids' => implode(',', $this->vehicles),
      'notes' => $this->notes,
      'user' => $userResponsibleForOperation
    ];

    if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
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
			$audit->description = $this->tableDescription.' created: '.$this->getName();
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
			$result = $db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
      if ($this->originalRequest) $db->query('UPDATE events SET original_request = :original_request WHERE id = :id', ['original_request' => $this->originalRequest, 'id' => $id]);
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (\Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
  }

  protected function reset(): void
  {
    parent::reset();

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
  }

  public function confirm(?string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = 'update';
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);
	
		$query = "UPDATE {$this->tableName} SET confirmed = NOW() WHERE id = :id";
		$params = ['id' => $this->id];
		$db->query($query, $params);

		$audit->description = $this->tableDescription.' confirmed: '.$this->getName();
		$this->load($this->id);
		$audit->after = json_encode($this->row);
		$audit->commit();
		return true;
	}
  

	public function cancel(?string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = 'update';
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$query = "UPDATE {$this->tableName} SET cancellation_requested = NOW() WHERE id = :event_id";
		$params = ['event_id' => $this->id];
		$db->query($query, $params);

		$audit->description = $this->tableDescription.' cancellation requested: '.$this->getName();
		$this->load($this->id);
		$audit->after = json_encode($this->row);
		$audit->commit();
		return true;
	}

	public function isEditable(): bool
	{
		if (is_null($this->endDate)) return true;
    if (is_null($this->confirmed)) return true;
		return !($this->endDate->getTimestamp() <= strtotime('now'));
	}


	public function isConfirmed(): bool
	{
		return (!is_null($this->confirmed));
	}


  public function isCancelled(): bool
  {
    return (!is_null($this->cancelled));
  }

  public static function nextEventByVehicle(int $vehicleId): ?int
  {
    $db = Database::getInstance();
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