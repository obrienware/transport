<?php

require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}

class Vehicle
{
	private $vehicleId;

	public $color;
	public $name;
	public $description;
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
		$sql = '
			SELECT v.*, l.name AS location, b.name AS current_location
			FROM vehicles v
			LEFT OUTER JOIN locations l ON l.id = v.default_staging_location_id
			LEFT OUTER JOIN locations b ON b.id = v.location_id
			WHERE v.id = :vehicle_id
		';
		$data = ['vehicle_id' => $vehicleId];
		if ($item = $db->get_row($sql, $data)) {
			$this->vehicleId = $item->id;
			$this->color = $item->color;
			$this->name = $item->name;
			$this->description = $item->description;
			$this->passengers = $item->passengers;
			$this->requireCDL = $item->require_cdl;
			$this->hasCheckEngine = $item->check_engine;
			$this->mileage = $item->mileage;
			$this->stagingLocationId = $item->default_staging_location_id;
			$this->stagingLocation = $item->location;

			$this->lastUpdate = $item->last_update;
			$this->lastUpdatedBy = $item->last_updated_by;
			$this->locationId = $item->location_id;
			$this->currentLocation = $item->current_location;
			$this->fuelLevel = $item->fuel_level;
			$this->cleanInterior = $item->clean_interior;
			$this->cleanExterior = $item->clean_exterior;
			$this->restock = $item->restock;
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

	static public function delete($vehicleId)
	{
		global $db;
		$sql = 'UPDATE vehicles SET archived = NOW(), archived_by = :user WHERE id = :vehicle_id';
		$data = ['user' => $_SESSION['user']->username, 'vehicle_id' => $vehicleId];
		return $db->query($sql, $data);
	}
}
