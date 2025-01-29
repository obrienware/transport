<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Blockout extends Base
{
	protected $tableName = 'user_blockouts';
	protected $tableDescription = 'Blockout Dates';
	
	public ?int $blockoutId = null;
	public ?int $userId = null;
	private ?DateTime $fromDateTime = null;
	private ?DateTime $toDateTime = null;
	public ?string $note = null;

	public function getName(): string
	{
		return '';
	}

	public function load(int $id): bool
	{
		$db = Database::getInstance();
		$query = 'SELECT * FROM user_blockouts WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $db->get_row($query, $params)) {
			$this->mapRowToProperties($row);
			return true;
		}
		return false;
	}

	protected function mapRowToProperties(object $row): void
	{
		$defaultTimezone = new DateTimeZone($_ENV['TZ'] ?? 'UTC');
		$this->row = $row;
		$this->action = 'update';

		$this->id = $row->id;
		$this->userId = $row->user_id;
		$this->note = $row->note;

		if (!empty($row->from_datetime)) {
			$this->fromDateTime = (new DateTime($row->from_datetime, $defaultTimezone))->setTimezone($this->timezone);
		}

		if (!empty($row->to_datetime)) {
			$this->toDateTime = (new DateTime($row->to_datetime, $defaultTimezone))->setTimezone($this->timezone);
		}

		if (!empty($row->archived)) {
			$this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
		}
	}

	public function __set($name, $value)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'fromDateTime':
        case 'toDateTime':
          if ($value instanceof DateTime) {
            $this->$name = $value;
            return;
          }
          $this->$name = is_null($value) ? null : new DateTime($value, $this->timezone);
          return;
      }
    }
  }
  
  public function __get($name)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'fromDateTime':
        case 'toDateTime':
          return is_null($this->$name) ? null : $this->$name->setTimezone($this->timezone)->format('Y-m-d H:i:s');
      }
    }
  }

	public function save(?string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$params = [
			'user_id' => $this->userId,
			'from_datetime' => is_null($this->fromDateTime) ? null : $this->fromDateTime->setTimezone($this->timezone)->format('Y-m-d H:i:s'),
			'to_datetime' => is_null($this->toDateTime) ? null : $this->toDateTime->setTimezone($this->timezone)->format('Y-m-d H:i:s'),
			'note' => $this->note,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated';
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					user_id = :user_id,
					from_datetime = :from_datetime,
					to_datetime = :to_datetime,
					note = :note,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created';
			$query = "
				INSERT INTO {$this->tableName} SET
					user_id = :user_id,
					from_datetime = :from_datetime,
					to_datetime = :to_datetime,
					note = :note,
					created = NOW(),
					created_by = :user
			";
		}
		try {
			$result = $db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (\Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}

	public static function getAll(): array
	{
		$db = Database::getInstance();
		$query = "
			SELECT b.*, CONCAT(u.first_name,' ',u.last_name) AS user
			FROM user_blockouts b
			LEFT OUTER JOIN users u ON u.id = b.user_id
			WHERE 
				to_datetime > NOW() ORDER BY from_datetime
		";
		return $db->get_rows($query);
	}
	
	protected function reset(): void
	{
		parent::reset();

		$this->blockoutId = null;
		$this->userId = null;
		$this->fromDateTime = null;
		$this->toDateTime = null;
		$this->note = null;
	}

	public static function getBlockoutsForUser(int $userId): array
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM user_blockouts WHERE to_datetime > NOW() AND user_id = :user_id ORDER BY from_datetime";
    $params = ['user_id' => $userId];
		return $db->get_rows($query, $params);
	}
	
}
