<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.location.php';

class Vehicle
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

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


	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = "SELECT * FROM vehicles WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->color = $row->color;
			$this->name = $row->name;
			$this->description = $row->description;
			$this->licensePlate = $row->license_plate;
			$this->passengers = $row->passengers;
			$this->requireCDL = $row->require_cdl;
			$this->hasCheckEngine = $row->check_engine;
			$this->mileage = $row->mileage;
			$this->stagingLocationId = $row->default_staging_location_id;
			$this->lastUpdate = $row->last_update;
			$this->lastUpdatedBy = $row->last_updated_by;
			$this->locationId = $row->location_id;
			$this->fuelLevel = $row->fuel_level;
			$this->cleanInterior = $row->clean_interior;
			$this->cleanExterior = $row->clean_exterior;
			$this->restock = $row->restock;

			if ($this->stagingLocationId) $this->stagingLocation = new Location($this->stagingLocationId);
			if ($this->locationId) $this->currentLocation = new Location($this->locationId);
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
		$audit->table = 'vehicles';
		$audit->before = json_encode($this->row);

		$params = [
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
			'restock' => $this->restock,
			'user' => $user
		];

		if ($this->action === 'update') {
			$audit->description = 'Vehicle updated: '.$this->name;
			$params['id'] = $this->id;
			$query = "
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
					restock = :restock,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Vehicle created: '.$this->name;
			$query = "
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
					restock = :restock,
					created = NOW(),
					created_by = :user
			";
		}
		try {
			$result = $this->db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
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
		$audit->table = 'vehicles';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE vehicles 
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
			$audit->description = 'Vehicle deleted: '.$this->name;
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


	static public function getAll(): array
	{
		$db = new data();
		$query = 'SELECT * FROM vehicles WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
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

		$this->color = null;
		$this->name = null;
		$this->description = null;
		$this->licensePlate = null;
		$this->passengers = null;
		$this->requireCDL = null;
		$this->hasCheckEngine = null;
		$this->mileage = null;
		$this->stagingLocationId = null;
		$this->stagingLocation = null;
		$this->lastUpdate = null;
		$this->lastUpdatedBy = null;
		$this->locationId = null;
		$this->currentLocation = null;
		$this->fuelLevel = null;
		$this->cleanInterior = null;
		$this->cleanExterior = null;
		$this->restock = null;
		$this->archived = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}	


	public function getLastError(): string | null
	{
		return $this->lastError;
	}

}
