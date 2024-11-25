<?php

require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}

class Airline
{
	private $airlineId;
	public $name;
	public $flightNumberPrefix;
	public $imageFilename;

	public function __construct($airlineId)
	{
		if (isset($airlineId)) {
			$this->getAirline($airlineId);
		}
	}

	public function getAirline($airlineId)
	{
		global $db;
		$sql = 'SELECT * FROM airlines WHERE id = :airline_id';
		$data = ['airline_id' => $airlineId];
		if ($item = $db->get_row($sql, $data)) {
			$this->airlineId = $item->id;
			$this->name = $item->name;
			$this->flightNumberPrefix = $item->flight_number_prefix;
			$this->imageFilename = $item->image_filename;
			return true;
		}
		return false;
	}

	static public function getAirlines()
	{
		global $db;
		$sql = 'SELECT * FROM airlines ORDER BY name';
		return $db->get_results($sql);
	}
}
