<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';

class Airline
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $name;
	public $flightNumberPrefix;
	public $imageFilename;

	
	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM airlines WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->name = $row->name;
			$this->flightNumberPrefix = $row->flight_number_prefix;
			$this->imageFilename = $row->image_filename;
			$this->archived = $row->archived;
			return true;
		}
		return false;
	}


	public function getId(): int | null
	{
		return $this->id;
	}


	public function save(string $userResponsibleForOperation = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->table = 'airlines';
		$audit->before = json_encode($this->row);

		$params = [
			'name' => $this->name,
			'flight_number_prefix' => $this->flightNumberPrefix,
			'image_filename' => $this->imageFilename,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'Airline updated: '.$this->name;
			$params['id'] = $this->id;
			$query = "
				UPDATE airlines SET
					name = :name,
					flight_number_prefix = :flight_number_prefix,
					image_filename = :image_filename,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Airline created: '.$this->name;
			$query = "
				INSERT INTO airlines SET
					name = :name,
					flight_number_prefix = :flight_number_prefix,
					image_filename = :image_filename,
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


	public function delete(string $userResponsibleForOperation = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = 'delete';
		$audit->table = 'airlines';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE airlines 
			SET 
				archived = NOW(), archived_by = :user 
			WHERE id = :id
		";
		$params = [
			'user' => $userResponsibleForOperation, 
			'id' => $this->id
		];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Airline deleted: '.$this->name;
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
		$query = 'SELECT * FROM airlines WHERE archived IS NULL ORDER BY name';
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
		$this->flightNumberPrefix = null;
		$this->imageFilename = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}


	public function getLastError(): string | null
	{
		return $this->lastError;
	}
}
