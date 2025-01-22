<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Airport extends Base
{
	protected $tableName = 'airports';
	protected $tableDescription = 'Airports';

	public ?string $name = null;
	public ?string $IATA = null;
	public ?int $stagingLocationId = null;
	public ?Location $stagingLocation = null;
  public ?int $leadTime = null;
	public ?int $travelTime = null;
	public ?string $arrivalInstructions = null;
	public ?string $arrivalInstructionsGroup = null;

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
		$this->name = $row->name;
		$this->IATA = $row->iata;
		$this->stagingLocationId = $row->staging_location_id;
		$this->leadTime = $row->lead_time;
		$this->travelTime = $row->travel_time;
		$this->arrivalInstructions = $row->arrival_instructions_small;
		$this->arrivalInstructionsGroup = $row->arrival_instructions_group;

		if (!empty($this->stagingLocationId)) {
			$this->stagingLocation = new Location($this->stagingLocationId);
		}
    if (!empty($row->archived)) {
      $this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
    }
	}

  public function loadAirportByIATA(string $IATA): bool
  {
		$db = Database::getInstance();
		$query = 'SELECT * FROM airports WHERE iata = :iata';
		$params = ['iata' => $IATA];
		if ($row = $db->get_row($query, $params)) {
			$this->mapRowToProperties($row);
			return true;
		}
		return false;
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
			'iata' => $this->IATA,
			'name' => $this->name,
			'lead_time' => $this->leadTime,
			'travel_time' => $this->travelTime,
			'staging_location_id' => $this->stagingLocationId,
			'arrival_instructions_small' => $this->arrivalInstructions,
			'arrival_instructions_group' => $this->arrivalInstructionsGroup,
			'user' => $userResponsibleForOperation
		];

		if ($this->action == 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					iata = :iata,
					name = :name,
					lead_time = :lead_time,
					travel_time = :travel_time,
					staging_location_id = :staging_location_id,
					arrival_instructions_small = :arrival_instructions_small,
					arrival_instructions_group = :arrival_instructions_group,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					iata = :iata,
					name = :name,
					lead_time = :lead_time,
					travel_time = :travel_time,
					staging_location_id = :staging_location_id,
					arrival_instructions_small = :arrival_instructions_small,
					arrival_instructions_group = :arrival_instructions_group,
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
		$query = 'SELECT * FROM airports WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
	}

	protected function reset(): void
	{
		parent::reset();

		$this->name = null;
		$this->IATA = null;
		$this->stagingLocationId = null;
		$this->stagingLocation = null;
		$this->leadTime = null;
		$this->travelTime = null;
		$this->arrivalInstructions = null;
		$this->arrivalInstructionsGroup = null;
	}
}
