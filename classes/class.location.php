<?php

require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}

class Location
{
	private $locationId;
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

	public function __construct($locationId)
	{
		if (isset($locationId)) {
			$this->getLocation($locationId);
		}
	}

	public function getLocation($locationId)
	{
		global $db;
		$sql = 'SELECT * FROM locations WHERE id = :location_id';
		$data = ['location_id' => $locationId];
		if ($item = $db->get_row($sql, $data)) {
			$this->locationId = $item->id;
			$this->name = $item->name;
			$this->description = $item->description;
			$this->shortName = $item->short_name;
			$this->mapAddress = $item->map_address;
			$this->lat = $item->lat;
			$this->lon = $item->lon;
			$this->placeId = $item->place_id;
			$this->type = $item->type;
			$this->IATA = $item->iata;
			$this->meta = $item->meta;
			return true;
		}
		return false;
	}

	public function getLocationId()
	{
		return $this->locationId;
	}

	static public function getLocations()
	{
		global $db;
		$sql = "SELECT * FROM locations WHERE archived IS NULL ORDER BY name";
		return $db->get_results($sql);
	}

	public function save()
	{
		global $db;
		$data = [
			'name' => $this->name,
			'short_name' => $this->shortName,
			'description' => $this->description,
			'type' => $this->type,
			'iata' => $this->IATA,
			'map_address' => $this->mapAddress,
			'lat' => $this->lat,
			'lon' => $this->lon,
			'place_id' => $this->placeId,
		];
		if ($this->locationId) {
			// Update
			$data['id'] = $this->locationId;
			$sql = "
				UPDATE locations SET
					name = :name,
					short_name = :short_name,
					description = :description,
					type = :type,
					iata = :iata,
					map_address = :map_address,
					lat = :lat,
					lon = :lon,
					place_id = :place_id
				WHERE id = :id
			";
		} else {
			// Insert
			$sql = "
				INSERT INTO locations SET
					name = :name,
					short_name = :short_name,
					description = :description,
					type = :type,
					iata = :iata,
					map_address = :map_address,
					lat = :lat,
					lon = :lon,
					place_id = :place_id
			";
		}
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
	}

	static public function deleteLocation(int $locationId)
	{
		global $db;
		$sql = "UPDATE locations SET archived = NOW(), archived_by = :user WHERE id = :location_id";
		$data = ['user' => $_SESSION['user']->username, 'location_id' => $locationId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteLocation($this->locationId);
	}

}
