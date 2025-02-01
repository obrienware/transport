<?php
declare(strict_types=1);

namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class VehicleReservation extends Base
{
  protected $tableName = 'vehicle_reservations';
	protected $tableDescription = 'Vehicle reservation';

	private ?int $guestId = null;
  public ?Guest $guest = null;
  private ?int $vehicleId = null;
  public ?Vehicle $vehicle = null;
  public ?int $requestorId = null;
  public ?User $requestor = null;
  public ?int $startTripId = null;
  public ?Trip $startTrip = null;
  public ?int $endTripId = null;
  public ?Trip $endTrip = null;
  private ?DateTime $startDateTime = null;
  private ?DateTime $endDateTime = null;
	public ?string $reason = null;
  private ?DateTime $confirmed = null;

	public function getName(): string
	{
		return is_null($this->reason) ? 'no-name' : $this->reason;
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
    $defaultTimezone = new DateTimeZone($_ENV['TZ'] ?? 'UTC');
    $this->row = $row;
    $this->action = 'update';

    $this->id = $row->id;
    $this->guestId = $row->guest_id;
    $this->vehicleId = $row->vehicle_id;
    $this->requestorId = $row->requestor_id;
    $this->startTripId = $row->start_trip_id;
    $this->endTripId = $row->end_trip_id;
    $this->reason = $row->reason;

    if (!empty($row->guest_id)) $this->guest = new Guest($row->guest_id);
    if (!empty($row->vehicle_id)) $this->vehicle = new Vehicle($row->vehicle_id);
    if (!empty($row->requestor_id)) $this->requestor = new User($row->requestor_id);
    if (!empty($row->start_trip_id)) $this->startTrip = new Trip($row->start_trip_id);
    if (!empty($row->end_trip_id)) $this->endTrip = new Trip($row->end_trip_id);

    if (!empty($row->start_datetime)) {
      $this->startDateTime = (new DateTime($row->start_datetime, $defaultTimezone))->setTimezone($this->timezone);
    }
    if (!empty($row->end_datetime)) {
      $this->endDateTime = (new DateTime($row->end_datetime, $defaultTimezone))->setTimezone($this->timezone);
    }
    if (!empty($row->confirmed)) {
      $this->confirmed = (new DateTime($row->confirmed, $defaultTimezone))->setTimezone($this->timezone);
    }
  }

  public function __set($name, $value)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'startDateTime':
        case 'endDateTime':
        case 'confirmed':
          if ($value instanceof DateTime) {
            $this->$name = $value;
            return;
          }
          $this->$name = is_null($value) ? null : new DateTime($value, $this->timezone);
          return;
        case 'guestId':
          $this->guestId = is_null($value) ? null : (int)$value;
          $this->guest = is_null($this->guestId) ? null : new Guest($this->guestId);
          break;
        case 'vehicleId':
          $this->vehicleId = is_null($value) ? null : (int)$value;
          $this->vehicle = is_null($this->vehicleId) ? null : new Vehicle($this->vehicleId);
          break;
      }
    }
  }
  
  public function __get($name)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'startDateTime':
				case 'endDateTime':
        case 'confirmed':
          return is_null($this->$name) ? null : $this->$name->format('Y-m-d H:i:s');
        case 'guestId':
          return $this->guestId;
        case 'vehicleId':
          return $this->vehicleId;
      }
    }
  }


  public function save(string $userResponsibleForOperation = null): bool
  {
    $defaultTimezone = new DateTimeZone($_ENV['TZ'] ?? 'UTC');
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

    $params = [
      'guest_id' => $this->guestId,
      'vehicle_id' => $this->vehicleId,
      'requestor_id' => $this->requestorId,
      'start_trip_id' => $this->startTripId,
      'end_trip_id' => $this->endTripId,
      'start_datetime' => is_null($this->startDateTime) ? null : $this->startDateTime->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
      'end_datetime' => is_null($this->endDateTime) ? null : $this->endDateTime->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
      'reason' => $this->reason,
      'username' => $userResponsibleForOperation
    ];

    if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated for: '.$this->guest->getName(); // We're technically not allowing a reservation without a guest (since it's actually _for_ a guest), so we'll assume the guest won't be blank
			$params['id'] = $this->id;
      $query = "
        UPDATE {$this->tableName} SET
          guest_id = :guest_id,
          vehicle_id = :vehicle_id,
          requestor_id = :requestor_id,
          start_trip_id = :start_trip_id,
          end_trip_id = :end_trip_id,
          start_datetime = :start_datetime,
          end_datetime = :end_datetime,
          reason = :reason,
          modified = NOW(),
          modified_by = :username
        WHERE id = :id
      ";
    } else {
			$audit->description = $this->tableDescription.' created for: '.$this->guest->getName(); // We're technically not allowing a reservation without a guest (since it's actually _for_ a guest), so we'll assume the guest won't be blank
			$query = "
        INSERT INTO {$this->tableName} SET
          guest_id = :guest_id,
          vehicle_id = :vehicle_id,
          requestor_id = :requestor_id,
          start_trip_id = :start_trip_id,
          end_trip_id = :end_trip_id,
          start_datetime = :start_datetime,
          end_datetime = :end_datetime,
          reason = :reason,
          created = NOW(),
          created_by = :username
      ";
    }
		try {
			$result = $db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (\Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
    return true;
  }

  protected function reset(): void
  {
    parent::reset();

    $this->guestId = null;
    $this->guest = null;
    $this->vehicleId = null;
    $this->vehicle = null;
    $this->requestorId = null;
    $this->requestor = null;
    $this->startTripId = null;
    $this->startTrip = null;
    $this->endTripId = null;
    $this->endTrip = null;
    $this->startDateTime = null;
    $this->endDateTime = null;
    $this->reason = null;
		$this->confirmed = null;
  }

  public function isConfirmed(): bool
	{
		return is_null($this->confirmed) ? false : true;
	}

  public function confirm(string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
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


  public static function getAll(): array
  {
    $db = Database::getInstance();
    $query = "
      SELECT 
        r.*, 
        CONCAT(g.first_name,' ',g.last_name) AS guest,
        CONCAT(u.first_name,' ',u.last_name) AS requestor, 
        v.name AS vehicle
      FROM vehicle_reservations r
      LEFT OUTER JOIN guests g ON g.id = r.guest_id
      LEFT OUTER JOIN vehicles v ON v.id = r.vehicle_id
      LEFT OUTER JOIN users u ON u.id = r.requestor_id
      WHERE 
        r.archived IS NULL
        AND r.start_datetime > DATE_SUB(CURDATE(), INTERVAL 7 DAY) -- Going back 7 days
      ORDER BY r.start_datetime ASC
    ";
    return $db->get_rows($query);
  }

}
