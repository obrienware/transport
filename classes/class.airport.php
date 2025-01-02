<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.location.php';

class Airport
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $name;
	public $IATA;
	public $stagingLocationId;
	public $stagingLocation;
  public $leadTime;
	public $travelTime;
	public $arrivalInstructions;
	public $arrivalInstructionsGroup;


	public function __construct(int $id = null)
	{
    $this->db = new data();
    if ($id) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM airports WHERE id = :id';
    $params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->name = $row->name;
			$this->IATA = $row->iata;
			$this->stagingLocationId = $row->staging_location_id;
      $this->leadTime = $row->lead_time;
			$this->travelTime = $row->travel_time;
			$this->arrivalInstructions = $row->arrival_instructions_small;
			$this->arrivalInstructionsGroup = $row->arrival_instructions_group;
			$this->archived = $row->archived;

			if ($this->stagingLocationId) $this->stagingLocation = new Location($this->stagingLocationId);
			
			return true;
		}
		return false;
	}


  public function loadAirportByIATA(string $IATA): bool
  {
		$query = 'SELECT * FROM airports WHERE iata = :iata';
		$params = ['iata' => $IATA];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->name = $row->name;
			$this->IATA = $row->iata;
			$this->stagingLocationId = $row->staging_location_id;
      $this->leadTime = $row->lead_time;
			$this->travelTime = $row->travel_time;
			$this->arrivalInstructions = $row->arrival_instructions_small;
			$this->arrivalInstructionsGroup = $row->arrival_instructions_group;
			$this->archived = $row->archived;

			if ($this->stagingLocationId) $this->stagingLocation = new Location($this->stagingLocationId);
			
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
		$audit->table = 'airports';
		$audit->before = json_encode($this->row);

		$params = [
			'iata' => $this->IATA,
			'name' => $this->name,
			'lead_time' => $this->leadTime,
			'travel_time' => $this->travelTime,
			'staging_location_id' => $this->stagingLocationId,
			'arrival_instructions_small' => $this->arrivalInstructions,
			'arrival_instructions_group' => $this->arrivalInstructionsGroup,
			'user' => $user
		];

		if ($this->action == 'update') {
			$audit->description = 'Airport created: '.$this->name;
			$params['id'] = $this->id;
			$query = "
				UPDATE airports SET
					iata = :iata,
					name = :name,
					lead_time = :lead_time,
					travel_time = :travel_time,
					staging_location_id = :staging_location_id,
					arrival_instructions_small = :arrival_instructions_small,
					arrival_instructions_group = :arrival_instructions_group,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Airport updated: '.$this->name;
			$query = "
				INSERT INTO airports SET
					iata = :iata,
					name = :name,
					lead_time = :lead_time,
					travel_time = :travel_time,
					staging_location_id = :staging_location_id,
					arrival_instructions_small = :arrival_instructions_small,
					arrival_instructions_group = :arrival_instructions_group,
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
		$audit->table = 'airports';
		$audit->before = json_encode($this->row);

    $query = "UPDATE airports SET archived = NOW(), archived_by = :user WHERE id = :id";
    $params = ['id' => $this->id, 'user' => $user];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Airport deleted: '.$this->name;
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
		$query = 'SELECT * FROM airports WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
	}


	public function isArchived(): bool
	{
		return isset($this->archived);
	}


	private function reset()
	{
		$this->id = null;
		$this->row = null;
		$this->lastError = null;
		$this->action = 'create';
		$this->archived = null;

		$this->name = null;
		$this->IATA = null;
		$this->stagingLocationId = null;
		$this->stagingLocation = null;
		$this->leadTime = null;
		$this->travelTime = null;
		$this->arrivalInstructions = null;
		$this->arrivalInstructionsGroup = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}
	
}
