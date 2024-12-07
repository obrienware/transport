<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();

class Airport
{
	private $airportId;
	public $name;
	public $IATA;
	public $stagingLocationId;
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
			$this->airportId = $item->id;
			$this->name = $item->name;
			$this->IATA = $item->iata;
			$this->stagingLocationId = $item->staging_location_id;
      $this->leadTime = $item->lead_time;
			$this->arrivalInstructions = $item->arrival_instructions_small;
			$this->arrivalInstructionsGroup = $item->arrival_instructions_group;
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
			$this->airportId = $item->id;
			$this->name = $item->name;
			$this->IATA = $item->iata;
      $this->leadTime = $item->lead_time;
			$this->stagingLocationId = $item->staging_location_id;
			$this->arrivalInstructions = $item->arrival_instructions_small;
			$this->arrivalInstructionsGroup = $item->arrival_instructions_group;
			return true;
		}
		return false;
  }

	static public function getAirports(): mixed // false | array
	{
		global $db;
		$sql = 'SELECT * FROM airports ORDER BY name';
		return $db->get_results($sql);
	}
}
