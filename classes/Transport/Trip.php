<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Trip extends Base
{
	protected $tableName = 'trips';
	protected $tableDescription = 'Trips';

	public ?int $requestorId = null;
	public ?string $summary = null;
	private ?DateTime $startDate = null;
	private ?DateTime $pickupDate = null;
	private ?DateTime $endDate = null;
	public ?string $guests = null;
	public ?int $guestId = null;
	public ?int $passengers = null;
	public ?int $puLocationId = null;
	public ?Location $puLocation = null;
	public ?int $doLocationId = null;
	public ?Location $doLocation = null;
	public ?int $driverId = null;
	public ?int $vehicleId = null;
	public ?int $airlineId = null;
	public ?string $flightNumber = null;
	public ?string $vehiclePUOptions = null;
	public ?string $vehicleDOOptions = null;
	private ?DateTime $ETA = null;
	private ?DateTime $ETD = null;
	public ?string $IATA = null;
	public ?Airport $airport = null;
	public ?string $guestNotes = null;
	public ?string $driverNotes = null;
	public ?string $generalNotes = null;
	public ?User $driver = null;
	public ?User $requestor = null;
	public ?Guest $guest = null;
	public ?Vehicle $vehicle = null;
	public ?Airline $airline = null;
  private ?DateTime $confirmed = null;
	private ?DateTime $started = null;
	private ?DateTime $completed = null;
	public ?string $originalRequest = null;
	private ?DateTime $cancelled = null;

	public ?string $created = null;
	public ?string $createdBy = null;
	public ?string $modified = null;
	public ?string $modifiedBy = null;

	
	public function getName(): string
	{
		return is_null($this->summary) ? 'no-name' : $this->summary;
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
		$this->requestorId = $row->requestor_id;
		$this->summary = $row->summary;
		if (!empty($row->start_date)) {
			$this->startDate = (new DateTime($row->start_date, $defaultTimezone))->setTimezone($this->timezone);
		}
		if (!empty($row->pickup_date)) {
			$this->pickupDate = (new DateTime($row->pickup_date, $defaultTimezone))->setTimezone($this->timezone);
		}
		if (!empty($row->end_date)) {
			$this->endDate = (new DateTime($row->end_date, $defaultTimezone))->setTimezone($this->timezone);
		}
		$this->guests = $row->guests;
		$this->guestId = $row->guest_id;
		$this->passengers = $row->passengers;
		$this->puLocationId = $row->pu_location;
		$this->doLocationId = $row->do_location;
		$this->driverId = $row->driver_id;
		$this->vehicleId = $row->vehicle_id;
		$this->airlineId = $row->airline_id;
		$this->flightNumber = $row->flight_number;
		$this->vehiclePUOptions = $row->vehicle_pu_options;
		$this->vehicleDOOptions = $row->vehicle_do_options;
		if (!empty($row->eta)) {
			$this->ETA = (new DateTime($row->eta, $defaultTimezone))->setTimezone($this->timezone);
		}
		if (!empty($row->etd)) {
			$this->ETD = (new DateTime($row->etd, $defaultTimezone))->setTimezone($this->timezone);
		}
		$this->IATA = $row->iata;
		$this->guestNotes = $row->guest_notes;
		$this->driverNotes = $row->driver_notes;
		$this->generalNotes = $row->general_notes;
		if (!empty($row->confirmed)) {
			$this->confirmed = (new DateTime($row->confirmed, $defaultTimezone))->setTimezone($this->timezone);
		}
		if (!empty($row->started)) {
			$this->started = (new DateTime($row->started, $defaultTimezone))->setTimezone($this->timezone);
		}
		if (!empty($row->completed)) {
			$this->completed = (new DateTime($row->completed, $defaultTimezone))->setTimezone($this->timezone);
		}
		if (!empty($row->cancellation_requested)) {
			$this->cancelled = (new DateTime($row->cancellation_requested, $defaultTimezone))->setTimezone($this->timezone);
		}
		$this->originalRequest = $row->original_request;

		if ($this->requestorId) $this->requestor = new User($this->requestorId);
		if ($this->guestId) $this->guest = new Guest($this->guestId);
		if ($this->puLocationId) $this->puLocation = new Location($this->puLocationId);
		if ($this->doLocationId) $this->doLocation = new Location($this->doLocationId);
		if ($this->driverId) $this->driver = new User($this->driverId);
		if ($this->vehicleId) $this->vehicle = new Vehicle($this->vehicleId);
		if ($this->airlineId) $this->airline = new Airline($this->airlineId);
		if ($this->ETA && $this->puLocationId) {
			$location = new Location($this->puLocationId);
			$this->airport = new Airport();
			$this->airport->loadAirportByIATA($location->IATA);
		}
		if ($this->ETD && $this->doLocationId) {
			$location = new Location($this->doLocationId);
			$this->airport = new Airport();
			$this->airport->loadAirportByIATA($location->IATA);
		}
    if (!empty($row->archived)) {
      $this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
    }
		$this->created = $row->created;
		$this->createdBy = $row->created_by;
		$this->modified = $row->modified;
		$this->modifiedBy = $row->modified_by;
	}

  public function __set($name, $value)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'startDate':
        case 'pickupDate':
				case 'endDate':
				case 'ETA':
				case 'ETD':
				case 'confirmed':
				case 'started':
				case 'completed':
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
				case 'pickupDate':
				case 'endDate':
				case 'ETA':
				case 'ETD':
				case 'confirmed':
				case 'started':
				case 'completed':
				case 'cancelled':
					return is_null($this->$name) ? null : $this->$name->format('Y-m-d H:i:s');
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
			'requestor_id' => $this->requestorId,
			'summary' => $this->summary,
			'start_date' => is_null($this->startDate) ? null : $this->startDate->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
			'pickup_date' => is_null($this->pickupDate) ? null : $this->pickupDate->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
			'end_date' => is_null($this->endDate) ? null : $this->endDate->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
			'guests' => $this->guests,
			'guest_id' => $this->guestId,
			'passengers' => $this->passengers,
			'pu_location' => $this->puLocationId,
			'do_location' => $this->doLocationId,
			'driver_id' => $this->driverId,
			'vehicle_id' => $this->vehicleId,
			'airline_id' => $this->airlineId,
			'flight_number' => $this->flightNumber,
			'iata' => $this->IATA,
			'vehicle_pu_options' => $this->vehiclePUOptions,
			'vehicle_do_options' => $this->vehicleDOOptions,
			'eta' => is_null($this->ETA) ? null : $this->ETA->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
			'etd' => is_null($this->ETD) ? null : $this->ETD->setTimezone($defaultTimezone)->format('Y-m-d H:i:s'),
			'guest_notes' => $this->guestNotes,
			'driver_notes' => $this->driverNotes,
			'general_notes' => $this->generalNotes,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					requestor_id = :requestor_id,
					summary = :summary,
					start_date = :start_date,
					pickup_date = :pickup_date,
					end_date = :end_date,
					guests = :guests,
					guest_id = :guest_id,
					passengers = :passengers,
					pu_location = :pu_location,
					do_location = :do_location,
					driver_id = :driver_id,
					vehicle_id = :vehicle_id,
					airline_id = :airline_id,
					flight_number = :flight_number,
					iata = :iata,
					vehicle_pu_options = :vehicle_pu_options,
					vehicle_do_options = :vehicle_do_options,
					eta = :eta,
					etd = :etd,
					guest_notes = :guest_notes,
					driver_notes = :driver_notes,
					general_notes = :general_notes,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					requestor_id = :requestor_id,
					summary = :summary,
					start_date = :start_date,
					pickup_date = :pickup_date,
					end_date = :end_date,
					guests = :guests,
					guest_id = :guest_id,
					passengers = :passengers,
					pu_location = :pu_location,
					do_location = :do_location,
					driver_id = :driver_id,
					vehicle_id = :vehicle_id,
					airline_id = :airline_id,
					flight_number = :flight_number,
					iata = :iata,
					vehicle_pu_options = :vehicle_pu_options,
					vehicle_do_options = :vehicle_do_options,
					eta = :eta,
					etd = :etd,
					guest_notes = :guest_notes,
					driver_notes = :driver_notes,
					general_notes = :general_notes,
					created = NOW(),
					created_by = :user
			";
		}
		try {
			$result = $db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			if ($this->originalRequest) $db->query('UPDATE trips SET original_request = :original_request WHERE id = :id', ['original_request' => $this->originalRequest, 'id' => $id]);
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

		$this->requestorId = null;
		$this->summary = null;
		$this->startDate = null;
		$this->pickupDate = null;
		$this->endDate = null;
		$this->guests = null;
		$this->guestId = null;
		$this->passengers = null;
		$this->puLocationId = null;
		$this->doLocationId = null;
		$this->driverId = null;
		$this->vehicleId = null;
		$this->airlineId = null;
		$this->flightNumber = null;
		$this->vehiclePUOptions = null;
		$this->vehicleDOOptions = null;
		$this->ETA = null;
		$this->ETD = null;
		$this->IATA = null;
		$this->airport = null;
		$this->guestNotes = null;
		$this->driverNotes = null;
		$this->generalNotes = null;
		$this->driver = null;
		$this->requestor = null;
		$this->guest = null;
		$this->vehicle = null;
		$this->airline = null;
		$this->confirmed = null;
		$this->started = null;
		$this->completed = null;
		$this->cancelled = null;
		$this->originalRequest = null;
	}

	public function cancel(string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$query = "UPDATE {$this->tableName} SET cancellation_requested = NOW() WHERE id = :id";
		$params = ['id' => $this->id];
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
		return !(
			!is_null($this->started)
			OR !is_null($this->completed)
			OR ($this->endDate->getTimestamp() <= strtotime('now'))
		);
	}

	public function isConfirmed(): bool
	{
		return is_null($this->confirmed) ? false : true;
	}

	public function isStarted(): bool
	{
		return is_null($this->started) ? false : true;
	}

	public function isCompleted(): bool
	{
		return is_null($this->completed) ? false : true;
	}

	public function isCancelled(): bool
	{
		return is_null($this->cancelled) ? false : true;
	}

	public static function nextTripByVehicle(int $vehicleId): ?int
	{
		$db = Database::getInstance();
		$query = "
			SELECT id FROM trips 
				WHERE  NOW() < start_date AND vehicle_id = :vehicle_id
				ORDER BY start_date
			LIMIT 1
		";
		$params = ['vehicle_id' => $vehicleId];
		return $db->get_var($query, $params);	
	}

	public static function upcomingTrips($limit = 5): array
	{
		$db = Database::getInstance();
		$query = "
			SELECT 
				t.*,
				CONCAT(g.first_name,' ',g.last_name) AS guest,
				CASE WHEN pu.short_name IS NULL THEN pu.name ELSE pu.short_name END AS pickup_from,
				CASE WHEN do.short_name IS NULL THEN do.name ELSE do.short_name END AS dropoff
				-- v.name AS vehicle,
				-- a.flight_number_prefix
			FROM trips t
			LEFT OUTER JOIN guests g ON g.id = t.guest_id
			LEFT OUTER JOIN locations pu on pu.id = t.pu_location
			LEFT OUTER JOIN locations do on do.id = t.do_location
			-- LEFT OUTER JOIN vehicles v ON v.id = t.vehicle_id
			-- LEFT OUTER JOIN airlines a ON a.id = t.airline_id
			WHERE 
				t.driver_id = :id
				AND t.end_date >= CURDATE()
				AND t.archived IS NULL
				-- AND t.start_date < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
				AND completed IS NULL
				AND t.start_date IS NOT NULL
				AND t.end_date IS NOT NULL
			ORDER BY t.start_date
			LIMIT {$limit}
		";
		$params = ['id' => $_SESSION['user']->id];
		return $db->get_rows($query, $params);	
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


	public function __clone()
	{
			// If you need to perform any deep copying or reset certain properties, do it here
			$this->id = null; // Reset the ID for the cloned instance
			$this->action = 'create'; // Reset the action to 'create'
			$this->summary = $this->summary.' (copy)';
			$this->confirmed = null;
			$this->archived = null;
	}

	public function clone(): Trip
	{
			return clone $this;
	}	
}
