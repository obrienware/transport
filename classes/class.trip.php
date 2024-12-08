<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');


require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}
require_once 'class.user.php';
require_once 'class.guest.php';
require_once 'class.location.php';
require_once 'class.vehicle.php';
require_once 'class.airline.php';
require_once 'class.airport.php';
class Trip
{
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
  public $finalized;
	public $started;
	public $completed;

	public function __construct($tripId = null)
	{
		if (isset($tripId)) {
			$this->getTrip($tripId);
		}
	}

	public function getTrip($tripId)
	{
		global $db;
		$sql = 'SELECT * FROM trips WHERE id = :id';
		$data = ['id' => $tripId];
		if ($item = $db->get_row($sql, $data)) {
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
      $this->finalized = $item->finalized;
			$this->started = $item->started;
			$this->completed = $item->completed;

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
			if ($this->ETA) {
				$location = new Location($this->puLocationId);
				$this->airport = new Airport();
				$this->airport->getAirportByIATA($location->IATA);
			}
			if ($this->ETD) {
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
		global $db;
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
		if ($this->tripId) {
			// Update
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
			// Insert
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
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
	}

	public function finalize()
	{
		global $db;
		$sql = 'UPDATE trips SET finalized = 1 WHERE id = :trip_id';
		$data = ['trip_id' => $this->tripId];
		$result = $db->query($sql, $data);
		return $result;
	}

	static public function deleteTrip($tripId)
	{
		global $db;
		$sql = 'UPDATE trips SET archived = NOW(), archived_by = :user WHERE id = :trip_id';
		$data = ['user' => $_SESSION['user']->username, 'trip_id' => $tripId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteTrip($this->tripId);
	}

	public function isEditable()
	{
		return !(
			$this->started 
			OR $this->completed 
			OR (strtotime($this->endDate) <= strtotime('now'))
		);
	}
}
