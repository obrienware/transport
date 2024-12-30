<?php
require_once 'class.data.php';

class Airline
{
	private $db;
	private $airlineId;
	private $row;

	public $name;
	public $flightNumberPrefix;
	public $imageFilename;

	public function __construct(int $airlineId = null)
	{
		$this->db = new data();
		if (isset($airlineId)) $this->getAirline($airlineId);
	}


	public function getAirline(int $airlineId): bool
	{
		$sql = 'SELECT * FROM airlines WHERE id = :airline_id';
		$data = ['airline_id' => $airlineId];
		if ($item = $this->db->get_row($sql, $data)) {
			$this->row = $item;
			$this->airlineId = $item->id;
			$this->name = $item->name;
			$this->flightNumberPrefix = $item->flight_number_prefix;
			$this->imageFilename = $item->image_filename;
			return true;
		}
		return false;
	}


	public function getAirlineId(): int | null
	{
		return $this->airlineId;
	}


	public function save()
	{
		$data = [
			'name' => $this->name,
			'flight_number_prefix' => $this->flightNumberPrefix,
			'image_filename' => $this->imageFilename
		];
		if ($this->airlineId) {
			// Updating
			$data['id'] = $this->airlineId;
			$sql = "
				UPDATE airlines SET
					name = :name,
					flight_number_prefix = :flight_number_prefix,
					image_filename = :image_filename
				WHERE id = :id
			";
		} else {
			// Adding
			$sql = "
				INSERT INTO airlines SET
					name = :name,
					flight_number_prefix = :flight_number_prefix,
					image_filename = :image_filename
			";
		}
		$result = $this->db->query($sql, $data);
		if ($this->airlineId) $this->getAirline($this->airlineId);
		else $this->getAirline($result);
		return $result;
	}


	public function getState(): string
	{
		return json_encode($this->row);
	}
	
	
	public function delete()
	{
		if ($this->airlineId) return Airline::deleteAirline($this->airlineId);
		return false;
	}


	/**
	 * Caution: This is used internally by unit testing to cleanly remove data. 
	 * Use the delete method instead in production to archive the user instead of completely removing the data
	 */
	public function remove()
	{
		$result = $this->db->query(
			"DELETE FROM airlines WHERE id = :id",
			['id' => $this->airlineId]
		);
		// Reset the object (as best we can)
		foreach (get_class_vars(get_class($this)) as $name => $default) 
  		$this->$name = $default;
		unset($this->db);
		unset($this->row);
		unset($this->airlineId);
		return $result;
	}


	static public function deleteAirline(int $airlineId)
	{
		$db = new data();
		$sql = "UPDATE airlines SET archived = NOW(), archived_by = :archived_by WHERE id = :id";
		$data = ['id' => $airlineId, 'archived_by' => isset($_SESSION['user']->username) ? $_SESSION['user']->username : null];
		return $db->query($sql, $data);
	}

	static public function getAirlines(): mixed
	{
		$db = new data();
		$sql = 'SELECT * FROM airlines WHERE archived IS NULL ORDER BY name';
		return $db->get_results($sql);
	}

}
