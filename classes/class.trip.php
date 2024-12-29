<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

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
	private $row;
	private $action = 'create';

	public $tripId;
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

	public function __construct($tripId = null)
	{
		$this->db = new data();
		if (isset($tripId)) {
			$this->getTrip($tripId);
		}
	}

	public function getTrip($tripId)
	{
		$sql = 'SELECT * FROM trips WHERE id = :id';
		$data = ['id' => $tripId];
		if ($item = $this->db->get_row($sql, $data)) {
			$this->action = 'update';
			$this->row = $item;

			$this->tripId = $item->id;
			$this->requestorId = $item->requestor_id;
			$this->summary = $item->summary;
			$this->startDate = $item->start_date;
			$this->pickupDate = $item->pickup_date;
			$this->endDate = $item->end_date;
			$this->guests = $item->guests;
			$this->guestId = $item->guest_id;
			$this->passengers = $item->passengers;
			$this->puLocationId = $item->pu_location;
			$this->doLocationId = $item->do_location;
			$this->driverId = $item->driver_id;
			$this->vehicleId = $item->vehicle_id;
			$this->airlineId = $item->airline_id;
			$this->flightNumber = $item->flight_number;
			$this->vehiclePUOptions = $item->vehicle_pu_options;
			$this->vehicleDOOptions = $item->vehicle_do_options;
			$this->ETA = $item->eta;
			$this->ETD = $item->etd;
			$this->IATA = $item->iata;
			$this->guestNotes = $item->guest_notes;
			$this->driverNotes = $item->driver_notes;
			$this->generalNotes = $item->general_notes;
      $this->confirmed = $item->confirmed;
			$this->started = $item->started;
			$this->completed = $item->completed;
			$this->cancelled = $item->cancellation_requested;
			$this->originalRequest = $item->original_request;

			if ($this->requestorId) {
				$this->getRequestor($this->requestorId);
			}
			if ($this->guestId) {
				$this->getGuest($this->guestId);
			}
			if ($this->puLocationId) {
				$this->getPULocation($this->puLocationId);
			}
			if ($this->doLocationId) {
				$this->getDOLocation($this->doLocationId);
			}
			if ($this->driverId) {
				$this->getDriver($this->driverId);
			}
			if ($this->vehicleId) {
				$this->getVehicle($this->vehicleId);
			}
			if ($this->airlineId) {
				$this->getAirline($this->airlineId);
			}
			if ($this->ETA && $this->puLocationId) {
				$location = new Location($this->puLocationId);
				$this->airport = new Airport();
				$this->airport->getAirportByIATA($location->IATA);
			}
			if ($this->ETD && $this->doLocationId) {
				$location = new Location($this->doLocationId);
				$this->airport = new Airport();
				$this->airport->getAirportByIATA($location->IATA);
			}
			
			return true;
		}
		return false;
	}

	public function getDriver($driverId)
	{
		$this->driver = new User($driverId);
	}

	public function getRequestor($requestorId)
	{
		$this->requestor = new User($requestorId);
	}

	public function getGuest($guestId)
	{
		$this->guest = new Guest($guestId);
	}

	public function getPULocation($locationId)
	{
		$this->puLocation = new Location(($locationId));
	}

	public function getDOLocation($locationId)
	{
		$this->doLocation = new Location($locationId);
	}

	public function getVehicle($vehicleId)
	{
		$this->vehicle = new Vehicle($vehicleId);
	}

	public function getAirline($ailineId)
	{
		$this->airline = new Airline($ailineId);
	}

	public function save(): array
	{
		$audit = new Audit();
		$audit->action = $this->action;
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);

		$data = [
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
			'general_notes' => $this->generalNotes
		];
		if ($this->action === 'update') {
			$audit->description = 'Trip updated: '.$this->summary;
			$data['id'] = $this->tripId;
			$sql = "
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
					general_notes = :general_notes
				WHERE id = :id
			";
		} else {
			$audit->description = 'Trip created: '.$this->summary;
			$sql = "
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
			$data['user'] = $_SESSION['user']->username;
		}
		try {
			$result = $this->db->query($sql, $data);
			$id = ($this->action === 'create') ? $result : $this->tripId;
			// We also want to add the original request if it exists here, but without creating a new audit entry
			if ($this->originalRequest) $this->db->query('UPDATE trips SET original_request = :original_request WHERE id = :id', ['original_request' => $this->originalRequest, 'id' => $id]);
			$this->getTrip($id);
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
		$sql = 'UPDATE trips SET confirmed = NOW() WHERE id = :trip_id';
		$data = ['trip_id' => $this->tripId];
		$result = $this->db->query($sql, $data);
		return $result;
	}

	public function delete()
	{
		$audit = new Audit();
		$audit->action = 'delete';
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);

		$sql = 'UPDATE trips SET archived = NOW(), archived_by = :user WHERE id = :trip_id';
		$data = ['user' => $_SESSION['user']->username, 'trip_id' => $this->tripId];
		$result = $this->db->query($sql, $data);
		$audit->description = 'Trip deleted: '.$this->summary;
		$audit->commit();
		return $result;
	}

	public function cancel()
	{
		$audit = new Audit();
		$audit->action = 'update';
		$audit->table = 'trips';
		$audit->before = json_encode($this->row);

		$sql = 'UPDATE trips SET cancellation_requested = NOW() WHERE id = :trip_id';
		$data = ['trip_id' => $this->tripId];
		$result = $this->db->query($sql, $data);

		$audit->description = 'Trip cancellation requested: '.$this->summary;
		$this->getTrip($this->tripId);
		$audit->after = json_encode($this->row);
		$audit->commit();
		return $result;
	}

	public function isEditable()
	{
		if (!$this->endDate) return true;
		return !(
			$this->started 
			OR $this->completed 
			OR (strtotime($this->endDate) <= strtotime('now'))
		);
	}

	public function isConfirmed()
	{
		return $this->confirmed ? true : false;
	}

	public function getState(): string
	{
		return json_encode($this->row);
	}

	static public function nextTripByVehicle(int $vehicleId)
	{
		$db = new data();
		$sql = "
			SELECT id FROM trips 
				WHERE  NOW() < start_date AND vehicle_id = :vehicle_id
				ORDER BY start_date
			LIMIT 1
		";
		$data = ['vehicle_id' => $vehicleId];
		return $db->get_var($sql, $data);	
	}

	static public function upcomingTrips($limit = 5)
	{
		$db = new data();
		$sql = "
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
		$data = ['id' => $_SESSION['user']->id];
		return $db->get_results($sql, $data);	
	}

}
