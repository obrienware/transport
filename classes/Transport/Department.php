<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Department extends Base
{
	protected $tableName = 'departments';
	protected $tableDescription = 'Departments';
	
  public ?string $name = null;
  public ?bool $mayRequest = null;

	public function getName(): string
	{
		return is_null($this->name) ? 'no-name' : $this->name;
	}

	public function load(int $id): bool
  {
		$db = Database::getInstance();
    $query = "SELECT * FROM departments WHERE id = :id";
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
		$this->name = $row->name;
		$this->mayRequest = ($row->can_submit_requests == 1);

		if (!empty($row->archived)) {
			$this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
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
			'name' => $this->name,
			'can_submit_requests' => $this->mayRequest ? 1 : 0,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					name = :name,
					can_submit_requests = :can_submit_requests,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					name = :name,
					can_submit_requests = :can_submit_requests,
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
    $query = "SELECT * FROM departments WHERE archived IS NULL ORDER BY name";
    return $db->get_rows($query);
  }

	protected function reset(): void
	{
		parent::reset();

		$this->name = null;
		$this->mayRequest = null;
	}
}