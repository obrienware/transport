<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class VehicleDocument extends Base
{
	protected $tableName = 'vehicle_documents';
	protected $tableDescription = 'Vehicle Documents';

	public ?int $vehicleId = null;
	public ?string $name = null;
	public ?string $filename = null;
	public ?string $fileType = null;

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
		$this->vehicleId = $row->vehicle_id;
		$this->name = $row->name;
		$this->filename = $row->filename;
		$this->fileType = $row->file_type;

		if (!empty($row->archived)) {
			$this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
		}
	}

	public function save(string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$params = [
			'vehicle_id' => $this->vehicleId,
			'name' => $this->name,
			'filename' => $this->filename,
			'file_type' => $this->fileType,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					vehicle_id = :vehicle_id,
					name = :name,
					filename = :filename,
					file_type = :file_type,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					vehicle_id = :vehicle_id,
					name = :name,
					filename = :filename,
					file_type = :file_type,
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

	protected function reset(): void
	{
		parent::reset();

		$this->vehicleId = null;
		$this->name = null;
		$this->filename = null;
		$this->fileType = null;
	}

	public static function getDocuments($vehicleId): array
  {
    $db = Database::getInstance();
    $query = "SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND archived IS NULL ORDER BY created";
    $params = ['vehicle_id' => $vehicleId];
    return $db->get_rows($query, $params);
  }
}