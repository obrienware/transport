<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Airline extends Base
{
	protected $tableName = 'airlines';
	protected $tableDescription = 'Airlines';

	public ?string $name = null;
	public ?string $flightNumberPrefix = null;
	public ?string $imageFilename = null;

	public function getName(): string
	{
		return is_null($this->name) ? 'no-name' : $this->name;
	}

	public function load(int $id): bool
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM {$this->tableName} WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $db->get_row($query, $params)) {
			$this->mapRowToProperties($row);			return true;
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
		$this->flightNumberPrefix = $row->flight_number_prefix;
		$this->imageFilename = $row->image_filename;

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
			'flight_number_prefix' => $this->flightNumberPrefix,
			'image_filename' => $this->imageFilename,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					name = :name,
					flight_number_prefix = :flight_number_prefix,
					image_filename = :image_filename,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					name = :name,
					flight_number_prefix = :flight_number_prefix,
					image_filename = :image_filename,
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
		$query = 'SELECT * FROM airlines WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
	}


	public function isArchived(): bool
	{
		return isset($this->archived);
	}


	protected function reset(): void
	{
		parent::reset();

		$this->name = null;
		$this->flightNumberPrefix = null;
		$this->imageFilename = null;
	}
}
