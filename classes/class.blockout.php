<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();


class Blockout
{
	private $row; // This will contain the initial row object as provided by the database
	
	public $blockoutId;
	public $userId;
	public $fromDateTime;
	public $toDateTime;
	public $note;
	
	public function __construct(int $blockoutId = null)
	{
		if (isset($blockoutId)) {
			$this->getBlockout($blockoutId);
		}
	}
	
	public function getBlockout(int $blockoutId): bool
	{
		global $db;
		$sql = 'SELECT * FROM user_blockouts WHERE id = :blockout_id';
		$data = ['blockout_id' => $blockoutId];
		if ($row = $db->get_row($sql, $data)) {
			$this->row = $row;
			
			$this->blockoutId = $row->id;
			$this->userId = $row->user_id;
			$this->fromDateTime = $row->from_datetime;
			$this->toDateTime = $row->to_datetime;
			$this->note = $row->note;
			return true;
		}
		return false;
	}
	
	public static function getBlockouts(): mixed
	{
		global $db;
		$sql = "
			SELECT b.*, CONCAT(u.first_name,' ',u.last_name) AS user
			FROM user_blockouts b
			LEFT OUTER JOIN users u ON u.id = b.user_id
			WHERE 
				to_datetime > NOW() ORDER BY from_datetime
		";
		return $db->get_results($sql);
	}
	
	public static function getBlockoutsForUser(int $userId): mixed
	{
		global $db;
		$sql = "SELECT * FROM user_blockouts WHERE to_datetime > NOW() AND user_id = :user_id ORDER BY from_datetime";
    $data = ['user_id' => $userId];
		return $db->get_results($sql, $data);
	}
	
	public function save(): array
	{
		global $db;
		$data = [
			'user_id' => $this->userId,
			'from_datetime' => $this->fromDateTime,
			'to_datetime' => $this->toDateTime,
			'note' => $this->note
		];
		if ($this->blockoutId) {
			// Update
			$data['id'] = $this->blockoutId;
			$sql = "
				UPDATE user_blockouts SET
					user_id = :user_id,
					from_datetime = :from_datetime,
					to_datetime = :to_datetime,
					note = :note
				WHERE id = :id
			";
		} else {
			// Insert
			$sql = "
				INSERT INTO user_blockouts SET
					user_id = :user_id,
					from_datetime = :from_datetime,
					to_datetime = :to_datetime,
					note = :note
			";
		}
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
	}
	
	public static function deleteBlockout(int $blockoutId): mixed
	{
		global $db;
		$sql = 'DELETE FROM user_blockouts WHERE id = :blockout_id';
		$data = ['blockout_id' => $blockoutId];
		return $db->query($sql, $data);
	}
	
	public function delete()
	{
		return $this->deleteBlockout($this->blockoutId);
	}
	
	/**
	 * getState: 
	 * 	Returns a JSON string representing the current database record. 
	 * 	Used by the audit trail to show what might have changed.
	 *
	 * @return string
	 */
	public function getState(): string
	{
		return json_encode($this->row);
	}

}
