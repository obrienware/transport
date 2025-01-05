<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';

class Blockout
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;
	
	public $blockoutId;
	public $userId;
	public $fromDateTime;
	public $toDateTime;
	public $note;
	
	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM user_blockouts WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

      $this->id = $row->id;
			$this->userId = $row->user_id;
			$this->fromDateTime = $row->from_datetime;
			$this->toDateTime = $row->to_datetime;
			$this->note = $row->note;
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
		$audit->table = 'user_blockouts';
		$audit->before = json_encode($this->row);

		$params = [
			'user_id' => $this->userId,
			'from_datetime' => $this->fromDateTime,
			'to_datetime' => $this->toDateTime,
			'note' => $this->note,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'Blockout Date updated';
			$params['id'] = $this->id;
			$query = "
				UPDATE user_blockouts SET
					user_id = :user_id,
					from_datetime = :from_datetime,
					to_datetime = :to_datetime,
					note = :note,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Blockout Date created';
			$query = "
				INSERT INTO user_blockouts SET
					user_id = :user_id,
					from_datetime = :from_datetime,
					to_datetime = :to_datetime,
					note = :note,
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
		$audit->table = 'user_blockouts';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE user_blockouts 
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
			$audit->description = 'Blockout Date deleted';
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
		$query = "
			SELECT b.*, CONCAT(u.first_name,' ',u.last_name) AS user
			FROM user_blockouts b
			LEFT OUTER JOIN users u ON u.id = b.user_id
			WHERE 
				to_datetime > NOW() ORDER BY from_datetime
		";
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

		$this->blockoutId = null;
		$this->userId = null;
		$this->fromDateTime = null;
		$this->toDateTime = null;
		$this->note = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}

	
	public function getLastError(): string | null
	{
		return $this->lastError;
	}


	public static function getBlockoutsForUser(int $userId): array
	{
		$db = data::getInstance();
		$query = "SELECT * FROM user_blockouts WHERE to_datetime > NOW() AND user_id = :user_id ORDER BY from_datetime";
    $params = ['user_id' => $userId];
		return $db->get_rows($query, $params);
	}
	
}
