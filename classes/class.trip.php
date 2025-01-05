<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.user.php';
require_once 'class.guest.php';
require_once 'class.location.php';
require_once 'class.vehicle.php';
require_once 'class.airline.php';
require_once 'class.airport.php';

class Trip
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $requestorId;
	public $summary;
	public $startDate;
	public $pickupDate;
	public $endDate;
	public $guests;
	public $guestId;
	public $passengers;
	public $puLocationId;
	public $puLocation;
	public $doLocationId;
	public $doLocation;
	public $driverId;
	public $vehicleId;
	public $airlineId;
	public $flightNumber;
	public $vehiclePUOptions;
	public $vehicleDOOptions;
	public $ETA;
	public $ETD;
	public $IATA;
	public $airport;
	public $guestNotes;
	public $driverNotes;
	public $generalNotes;
	public $driver;
	public $requestor;
	public $guest;
	public $vehicle;
	public $airline;
  public $confirmed;
	public $started;
	public $completed;
	public $originalRequest;
	public $cancelled;


	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM trips WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->requestorId = $row->requestor_id;
			$this->summary = $row->summary;
			$this->startDate = $row->start_date;
			$this->pickupDate = $row->pickup_date;
			$this->endDate = $row->end_date;
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
			$this->ETA = $row->eta;
			$this->ETD = $row->etd;
			$this->IATA = $row->iata;
			$this->guestNotes = $row->guest_notes;
			$this->driverNotes = $row->driver_notes;
			$this->generalNotes = $row->general_notes;
      $this->confirmed = $row->confirmed;
			$this->started = $row->started;
			$this->completed = $row->completed;
			$this->cancelled = $row->cancellation_requested;
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
			$this->archived = $row->archived;
			return true;
		}
		return false;
	}


	public function getId(): int | null
	{
		return $this->id;
	}


	public function save(string $userResponsibleForOperation = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);

		$params = [
			'requestor_id' => $this->requestorId,
			'summary' => $this->summary,
			'start_date' => $this->startDate,
			'pickup_date' => $this->pickupDate,
			'end_date' => $this->endDate,
			'guests' => $this->guests,
			'guest_id' => $this->guestId,
			'passengers' => $this->passengers,
			'pu_location' => $this->puLocationId,
			'do_location' => $this->doLocationId,
			'driver_id' => $this->driverId,
			'vehicle_id' => $this->vehicleId,
			'airline_id' => $this->airlineId,
			'flight_number' => $this->flightNumber,
			'vehicle_pu_options' => $this->vehiclePUOptions,
			'vehicle_do_options' => $this->vehicleDOOptions,
			'eta' => $this->ETA,
			'etd' => $this->ETD,
			'guest_notes' => $this->guestNotes,
			'driver_notes' => $this->driverNotes,
			'general_notes' => $this->generalNotes,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'Trip updated: '.$this->summary;
			$params['id'] = $this->id;
			$query = "
				UPDATE trips SET
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
			$audit->description = 'Trip created: '.$this->summary;
			$query = "
				INSERT INTO trips SET
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
			$result = $this->db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			if ($this->originalRequest) $this->db->query('UPDATE trips SET original_request = :original_request WHERE id = :id', ['original_request' => $this->originalRequest, 'id' => $id]);
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


	public function delete(string $userResponsibleForOperation = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = 'delete';
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE trips 
			SET 
				archived = NOW(), archived_by = :user 
			WHERE id = :id
		";
		$params = [
			'user' => $userResponsibleForOperation, 
			'id' => $this->id
		];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Trip deleted: '.$this->summary;
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


	public function isArchived(): bool
	{
		return isset($this->archived);
	}


	private function reset(): void
	{
		$this->id = null;
		$this->row = null;
		$this->lastError = null;
		$this->action = 'create';
		$this->archived = null;

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

		// Reinitialize the database connection if needed
		$this->db = new data();
	}


	public function getLastError(): string | null
	{
		return $this->lastError;
	}


	public function cancel(string $userResponsibleForOperation = null): bool
	{
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = 'update';
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);

		$query = 'UPDATE trips SET cancellation_requested = NOW() WHERE id = :id';
		$params = ['id' => $this->id];
		$this->db->query($query, $params);

		$audit->description = 'Trip cancellation requested: '.$this->summary;
		$this->load($this->id);
		$audit->after = json_encode($this->row);
		$audit->commit();
		return true;
	}


	public function isEditable(): bool
	{
		if (!$this->endDate) return true;
		if (!$this->confirmed) return true;
		return !(
			$this->started 
			OR $this->completed 
			OR (strtotime($this->endDate) <= strtotime('now'))
		);
	}


	public function isConfirmed(): bool
	{
		return $this->confirmed ? true : false;
	}


	static public function nextTripByVehicle(int $vehicleId): int | null
	{
		$db = data::getInstance();
		$query = "
			SELECT id FROM trips 
				WHERE  NOW() < start_date AND vehicle_id = :vehicle_id
				ORDER BY start_date
			LIMIT 1
		";
		$params = ['vehicle_id' => $vehicleId];
		return $db->get_var($query, $params);	
	}

	static public function upcomingTrips($limit = 5): array
	{
		$db = data::getInstance();
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
			ORDER BY t.start_date
			LIMIT {$limit}
		";
		$params = ['id' => $_SESSION['user']->id];
		return $db->get_rows($query, $params);	
	}


	public function confirm(string $userResponsibleForOperation = null): bool
	{
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = 'update';
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);
	
		$query = 'UPDATE trips SET confirmed = NOW() WHERE id = :id';
		$params = ['id' => $this->id];
		$this->db->query($query, $params);

		$audit->description = 'Trip confirmed: '.$this->summary;
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
