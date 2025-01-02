<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';

class Location
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $name;
	public $shortName;
	public $mapAddress;
	public $description;
	public $lat;
	public $lon;
	public $placeId;
	public $type;
	public $IATA;
	public $meta;


	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM locations WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->name = $row->name;
			$this->description = $row->description;
			$this->shortName = $row->short_name;
			$this->mapAddress = $row->map_address;
			$this->lat = $row->lat;
			$this->lon = $row->lon;
			$this->placeId = $row->place_id;
			$this->type = $row->type;
			$this->IATA = $row->iata;
			$this->meta = $row->meta;
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
		$audit->table = 'locations';
		$audit->before = json_encode($this->row);

		$params = [
			'name' => $this->name,
			'short_name' => $this->shortName,
			'description' => $this->description,
			'type' => $this->type,
			'iata' => $this->IATA,
			'map_address' => $this->mapAddress,
			'lat' => $this->lat,
			'lon' => $this->lon,
			'place_id' => $this->placeId,
			'user' => $user,
		];

		if ($this->action === 'update') {
			$audit->description = 'Location updated: '.$this->name;
			$params['id'] = $this->id;
			$query = "
				UPDATE locations SET
					name = :name,
					short_name = :short_name,
					description = :description,
					type = :type,
					iata = :iata,
					map_address = :map_address,
					lat = :lat,
					lon = :lon,
					place_id = :place_id,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Location created: '.$this->name;
			$query = "
				INSERT INTO locations SET
					name = :name,
					short_name = :short_name,
					description = :description,
					type = :type,
					iata = :iata,
					map_address = :map_address,
					lat = :lat,
					lon = :lon,
					place_id = :place_id,
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
		$audit->table = 'locations';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE locations 
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
			$audit->description = 'Location deleted: '.$this->name;
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
		$query = "SELECT * FROM locations WHERE archived IS NULL ORDER BY name";
		return $db->get_rows($query);
	}


	public function isArchived(): bool
	{
		return isset($this->archived);
	}


	public function reset(): void
	{
		$this->id = null;
		$this->row = null;
		$this->lastError = null;
		$this->action = 'create';
		$this->archived = null;

		$this->name = null;
		$this->shortName = null;
		$this->mapAddress = null;
		$this->description = null;
		$this->lat = null;
		$this->lon = null;
		$this->placeId = null;
		$this->type = null;
		$this->IATA = null;
		$this->meta = null;
	
		// Reinitialize the database connection if needed
		$this->db = new data();
}

}
