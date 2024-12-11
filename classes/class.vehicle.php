<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();

require_once 'class.location.php';

class Vehicle
{
	private $row;
	private $vehicleId;

	public $color;
	public $name;
	public $description;
	public $licensePlate;
	public $passengers;
	public $requireCDL;
	public $hasCheckEngine;
	public $mileage;
	public $stagingLocationId;
	public $stagingLocation;

	public $lastUpdate;
	public $lastUpdatedBy;
	public $locationId;
	public $currentLocation;
	public $fuelLevel;
	public $cleanInterior;
	public $cleanExterior;
	public $restock;

	public function __construct($vehicleId)
	{
		if (isset($vehicleId)) {
			$this->getVehicle($vehicleId);
		}
	}

	public function getVehicle($vehicleId)
	{
		global $db;
		$sql = "SELECT * FROM vehicles WHERE id = :vehicle_id";
		$data = ['vehicle_id' => $vehicleId];
		if ($item = $db->get_row($sql, $data)) {
			$this->row = $item;

			$this->vehicleId = $item->id;
			$this->color = $item->color;
			$this->name = $item->name;
			$this->description = $item->description;
			$this->licensePlate = $item->license_plate;
			$this->passengers = $item->passengers;
			$this->requireCDL = $item->require_cdl;
			$this->hasCheckEngine = $item->check_engine;
			$this->mileage = $item->mileage;
			$this->stagingLocationId = $item->default_staging_location_id;
			$this->lastUpdate = $item->last_update;
			$this->lastUpdatedBy = $item->last_updated_by;
			$this->locationId = $item->location_id;
			$this->fuelLevel = $item->fuel_level;
			$this->cleanInterior = $item->clean_interior;
			$this->cleanExterior = $item->clean_exterior;
			$this->restock = $item->restock;

			if ($this->stagingLocationId) $this->stagingLocation = new Location($this->stagingLocationId);
			if ($this->locationId) $this->currentLocation = new Location($this->locationId);
			return true;
		}
		return false;
	}

	public function getId(): int|null
	{
		return $this->vehicleId;
	}

	static public function getVehicles()
	{
		global $db;
		$sql = 'SELECT * FROM vehicles WHERE archived IS NULL ORDER BY name';
		if ($rs = $db->get_results($sql)) {
			return $rs;
		}
		return false;
	}

	public function save()
	{
		global $db;
		$data = [
			'color' => $this->color,
			'name' => $this->name,
			'description' => $this->description,
			'license_plate' => $this->licensePlate,
			'passengers' => $this->passengers,
			'require_cdl' => $this->requireCDL,
			'mileage' => $this->mileage,
			'check_engine' => $this->hasCheckEngine,

			'default_staging_location_id' => $this->stagingLocationId,
			'last_update' => $this->lastUpdate,
			'last_updated_by' => $this->lastUpdatedBy,
			'location_id' => $this->locationId,
			'fuel_level' => $this->fuelLevel,
			'clean_interior' => $this->cleanInterior,
			'clean_exterior' => $this->cleanExterior,
			'restock' => $this->restock
		];
		if ($this->vehicleId) {
			$sql = "
				UPDATE vehicles SET
					color = :color,
					name = :name,
					description = :description,
					license_plate = :license_plate,
					passengers = :passengers,
					require_cdl = :require_cdl,
					mileage = :mileage,
					check_engine = :check_engine,

					default_staging_location_id = :default_staging_location_id,
					last_update = :last_update,
					last_updated_by = :last_updated_by,
					location_id = :location_id,
					fuel_level = :fuel_level,
					clean_interior = :clean_interior,
					clean_exterior = :clean_exterior,
					restock = :restock
				WHERE id = :vehicle_id
			";
			$data['vehicle_id'] = $this->vehicleId;
		} else {
			$sql = "
				INSERT INTO vehicles SET
					color = :color,
					name = :name,
					description = :description,
					license_plate = :license_plate,
					passengers = :passengers,
					require_cdl = :require_cdl,
					mileage = :mileage,
					check_engine = :check_engine,

					default_staging_location_id = :default_staging_location_id,
					last_update = :last_update,
					last_updated_by = :last_updated_by,
					location_id = :location_id,
					fuel_level = :fuel_level,
					clean_interior = :clean_interior,
					clean_exterior = :clean_exterior,
					restock = :restock
			";
		}
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
	}

	static function deleteVehicle($vehicleId)
	{
		global $db;
		$sql = 'UPDATE vehicles SET archived = NOW(), archived_by = :user WHERE id = :vehicle_id';
		$data = ['user' => $_SESSION['user']->username, 'vehicle_id' => $vehicleId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteVehicle($this->vehicleId);
	}

	public function getState(): string
	{
		return json_encode($this->row);
	}

}
