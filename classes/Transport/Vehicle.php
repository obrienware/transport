<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Vehicle extends Base
{
	use Log;

	protected $tableName = 'vehicles';
	protected $tableDescription = 'Vehicles';

	public ?string $color = null;
	public ?string $name = null;
	public ?string $description = null;
	public ?string $licensePlate = null;
	public ?int $passengers = null;
	public ?bool $requireCDL = null;
	public ?bool $hasCheckEngine = null;
	public ?int $mileage = null;
	public ?int $stagingLocationId = null;
	public ?Location $stagingLocation = null;

	private ?DateTime $lastUpdate = null;
	public ?string $lastUpdatedBy = null;
	public ?int $locationId = null;
	public ?Location $currentLocation = null;
	public ?int $fuelLevel = null;
	public ?bool $cleanInterior = null; // has clean interior
	public ?bool $cleanExterior = null; // has clean exterior
	public ?bool $restock = null; // needs restocking


	public function getName(): string
	{
		return is_null($this->name) ? 'no-name' : $this->name;
	}
	
	public function load(int $id): bool
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM vehicles WHERE id = :id";
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
		$this->color = $row->color;
		$this->name = $row->name;
		$this->description = $row->description;
		$this->licensePlate = $row->license_plate;
		$this->passengers = $row->passengers;
		$this->requireCDL = !is_null($row->require_cdl) ? ($row->require_cdl == 1) : null;
		$this->hasCheckEngine = !is_null($row->check_engine) ? ($row->check_engine == 1) : null;
		$this->mileage = $row->mileage;
		$this->stagingLocationId = $row->default_staging_location_id;
		if (!empty($row->last_update))
			$this->lastUpdate = (new DateTime($row->last_update, $utc))->setTimezone($this->timezone);
		$this->lastUpdatedBy = $row->last_updated_by;
		$this->locationId = $row->location_id;
		$this->fuelLevel = $row->fuel_level;
		$this->cleanInterior = !is_null($row->clean_interior) ? ($row->clean_interior == 1) : null;
		$this->cleanExterior = !is_null($row->clean_exterior) ? ($row->clean_exterior == 1) : null;
		$this->restock = !is_null($row->restock) ? ($row->restock == 1) : null;

		if (!is_null($this->stagingLocationId))
			$this->stagingLocation = new Location($this->stagingLocationId);
		if (!is_null($this->locationId))
			$this->currentLocation = new Location($this->locationId);
    if (!empty($row->archived))
      $this->archived = (new DateTime($row->archived, $utc))->setTimezone($this->timezone);
	}

  public function __set($name, $value)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'lastUpdate':
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
        case 'lastUpdate':
          return is_null($this->$name) ? null : $this->$name->format('Y-m-d H:i:s');
      }
    }
  }

	public function save(string $userResponsibleForOperation = null): bool
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
			'color' => $this->color,
			'name' => $this->name,
			'description' => $this->description,
			'license_plate' => $this->licensePlate,
			'passengers' => $this->passengers,
			'require_cdl' => is_null($this->requireCDL) ? null : ($this->requireCDL ? 1 : 0),
			'mileage' => $this->mileage,
			'check_engine' => is_null($this->hasCheckEngine) ? null : ($this->hasCheckEngine ? 1 : 0),

			'default_staging_location_id' => $this->stagingLocationId,
			'last_update' => is_null($this->lastUpdate) ? null : $this->lastUpdate->setTimezone($utc)->format('Y-m-d H:i:s'),
			'last_updated_by' => $this->lastUpdatedBy,
			'location_id' => $this->locationId,
			'fuel_level' => $this->fuelLevel,
			'clean_interior' => is_null($this->cleanInterior) ? null : ($this->cleanInterior ? 1 : 0),
			'clean_exterior' => is_null($this->cleanExterior) ? null : ($this->cleanExterior ? 1 : 0),
			'restock' => is_null($this->restock) ? null : ($this->restock ? 1 : 0),
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
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
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
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
	}

	public static function getAll(): array | false
	{
		$db = Database::getInstance();
		$query = 'SELECT * FROM vehicles WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
	}

	protected function reset(): void
	{
		parent::reset();

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
	}	
}
