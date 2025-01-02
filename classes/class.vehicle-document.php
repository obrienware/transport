<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';

class VehicleDocument
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $vehicleId;
	public $name;
	public $filename;
	public $fileType;

	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


  public function load(int $id): bool
  {
    $query = "SELECT * FROM vehicle_documents WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->vehicleId = $row->vehicle_id;
			$this->id = $row->id;
			$this->name = $row->name;
			$this->filename = $row->filename;
			$this->fileType = $row->file_type;

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
		$audit->table = 'vehicle_documents';
		$audit->before = json_encode($this->row);

		$params = [
			'vehicle_id' => $this->vehicleId,
			'name' => $this->name,
			'filename' => $this->filename,
			'file_type' => $this->fileType,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'Vehicle Document updated: '.$this->name;
			$params['id'] = $this->id;
			$query = "
				UPDATE vehicle_documents SET
					vehicle_id = :vehicle_id,
					name = :name,
					filename = :filename,
					file_type = :file_type,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Vehicle Document created: '.$this->name;
			$query = "
				INSERT INTO vehicle_documents SET
					vehicle_id = :vehicle_id,
					name = :name,
					filename = :filename,
					file_type = :file_type,
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
		$audit->table = 'vehicle_documents';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE vehicle_documents 
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
			$audit->description = 'Vehicle Document deleted: '.$this->name;
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
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

		$this->vehicleId = null;
		$this->name = null;
		$this->filename = null;
		$this->fileType = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}


	public function getLastError(): string | null
	{
		return $this->lastError;
	}


	static public function getDocuments($vehicleId): array
  {
    $db = new data();
    $query = "SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND archived IS NULL ORDER BY uploaded";
    $params = ['vehicle_id' => $vehicleId];
    return $db->get_rows($query, $params);
  }
}