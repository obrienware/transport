<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';

class Department
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;
	
  public $name;
  public $mayRequest;


	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
  {
    $query = "SELECT * FROM departments WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
      $this->name = $row->name;
      $this->mayRequest = $row->can_submit_requests;
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
		$audit->table = 'departments';
		$audit->before = json_encode($this->row);

		$params = [
			'name' => $this->name,
			'can_submit_requests' => $this->mayRequest ? 1 : 0,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'Department updated: '.$this->name;
			$params['id'] = $this->id;
			$query = "
				UPDATE departments SET
					name = :name,
					can_submit_requests = :can_submit_requests,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Department created: '.$this->name;
			$query = "
				INSERT INTO departments SET
					name = :name,
					can_submit_requests = :can_submit_requests,
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
		$audit->table = 'departments';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE departments 
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
			$audit->description = 'Department deleted: '.$this->name;
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}
	

  public static function getAll(): array
  {
    $db = data::getInstance();
    $query = "SELECT * FROM departments WHERE archived IS NULL ORDER BY name";
    return $db->get_rows($query);
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

		$this->name = null;
		$this->mayRequest = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}


	function getLastError(): string | null
	{
		return $this->lastError;
	}
}