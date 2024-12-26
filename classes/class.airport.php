<?php
require_once 'class.data.php';
global $db;
if (!isset($db)) $db = new data();

require_once 'class.location.php';

class Airport
{
	private $airportId;
	private $row;

	public $name;
	public $IATA;
	public $stagingLocationId;
	public $stagingLocation;
  public $leadTime;
	public $arrivalInstructions;
	public $arrivalInstructionsGroup;


	public function __construct($airportId = null)
	{
		if (isset($airportId)) {
			$this->getAirport($airportId);
		}
	}

	public function getAirport(int $airportId): bool
	{
		global $db;
		$sql = 'SELECT * FROM airports WHERE id = :airport_id';
		$data = ['airport_id' => $airportId];
		if ($item = $db->get_row($sql, $data)) {
			$this->row = $item;
			$this->airportId = $item->id;
			$this->name = $item->name;
			$this->IATA = $item->iata;
			$this->stagingLocationId = $item->staging_location_id;
      $this->leadTime = $item->lead_time;
			$this->arrivalInstructions = $item->arrival_instructions_small;
			$this->arrivalInstructionsGroup = $item->arrival_instructions_group;
			if ($this->stagingLocationId) $this->stagingLocation = new Location($this->stagingLocationId);
			return true;
		}
		return false;
	}

  public function getAirportByIATA(string $IATA): bool
  {
		global $db;
		$sql = 'SELECT * FROM airports WHERE iata = :iata';
		$data = ['iata' => $IATA];
		if ($item = $db->get_row($sql, $data)) {
			$this->row = $item;
			$this->airportId = $item->id;
			$this->name = $item->name;
			$this->IATA = $item->iata;
      $this->leadTime = $item->lead_time;
			$this->stagingLocationId = $item->staging_location_id;
			$this->arrivalInstructions = $item->arrival_instructions_small;
			$this->arrivalInstructionsGroup = $item->arrival_instructions_group;
			if ($this->stagingLocationId) $this->stagingLocation = new Location($this->stagingLocationId);
			return true;
		}
		return false;
  }

	public function getAirportId(): int
	{
		return $this->airportId;
	}

	public function save()
	{
		global $db;
		$data = [
			'iata' => $this->IATA,
			'name' => $this->name,
			'lead_time' => $this->leadTime,
			'staging_location_id' => $this->stagingLocationId,
			'arrival_instructions_small' => $this->arrivalInstructions,
			'arrival_instructions_group' => $this->arrivalInstructionsGroup
		];
		if ($this->airportId) {
			$data['id'] = $this->airportId;
			$sql = "
				UPDATE airports SET
					iata = :iata,
					name = :name,
					lead_time = :lead_time,
					staging_location_id = :staging_location_id,
					arrival_instructions_small = :arrival_instructions_small,
					arrival_instructions_group = :arrival_instructions_group
				WHERE id = :id
			";
		} else {
			$sql = "
				INSERT INTO airports SET
					iata = :iata,
					name = :name,
					lead_time = :lead_time,
					staging_location_id = :staging_location_id,
					arrival_instructions_small = :arrival_instructions_small,
					arrival_instructions_group = :arrival_instructions_group
			";
		}
		$result = $db->query($sql, $data);
		if ($this->airportId) { 
			$this->getAirport($this->airportId);
		} else {
			$this->getAirport($result);
		}
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
	}

	public function getState(): string
	{
		return json_encode($this->row);
	}	

	static public function getAirports(): mixed // false | array
	{
		global $db;
		$sql = 'SELECT * FROM airports WHERE archived IS NULL ORDER BY name';
		return $db->get_results($sql);
	}
	
	static public function deleteAirport(int $airportId): mixed
	{
		global $db;
		$sql = 'UPDATE airports SET archived = NOW(), archived_by = :user WHERE id = :airport_id';
		$data = ['user' => $_SESSION['user']->username, 'airport_id' => $airportId];
		return $db->query($sql, $data);
	}

	public function delete(): mixed
	{
		return $this->deleteAirport($this->airportId);
	}
}
