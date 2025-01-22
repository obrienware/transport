<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class AirportLocation extends Base
{
  protected $tableName = 'airport_locations';
  protected $tableDescription = 'Airport locations';
  
  public ?int $airportId = null;
  public ?Airport $airport = null;
  public ?int $airlineId = null;
  public ?Airline $airline = null;
  public ?int $locationId = null;
  public ?Location $location = null;
  public ?string $type = null;

  public function getName(): string
  {
    return ''; // <- weird that we don't have a name for this
  }
  
  public function load(int $id): bool
  {
    $db = Database::getInstance();
    $query = "SELECT * FROM airport_locations WHERE id = :id";
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
    $this->airportId = $row->airport_id;
    $this->airlineId = $row->airline_id;
    $this->locationId = $row->location_id;
    $this->type = $row->type;

    if ($this->locationId) {
      $this->location = new Location($this->locationId);
    }

    if ($this->airportId) {
      $this->airport = new Airport($this->airportId);
    }

    if ($this->airlineId) {
      $this->airline = new Airline($this->airlineId);
    }

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
      'airport_id' => $this->airportId,
      'airline_id' => $this->airlineId,
      'location_id' => $this->locationId,
      'type' => $this->type,
      'user' => $userResponsibleForOperation
    ];

    if ($this->action == 'create') {
      $audit->description = $this->tableDescription.' created';
      $query = "
        INSERT INTO {$this->tableName} SET
          airport_id = :airport_id,
          airline_id = :airline_id,
          location_id = :location_id,
          `type` = :type,
          created = NOW(),
          created_by = :user
      ";
    } else {
      $audit->description = $this->tableDescription.' updated';
      $query = "
        UPDATE {$this->tableName} SET
          airport_id = :airport_id,
          airline_id = :airline_id,
          location_id = :location_id,
          `type` = :type,
          modified = NOW(),
          modified_by = :user
        WHERE id = :id
      ";
      $params['id'] = $this->id;
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
      SELECT 
        loc.*,
        l.name AS location,
        ap.name AS airport,
        al.name AS airline
      FROM airport_locations loc
      LEFT OUTER JOIN locations l ON loc.location_id = l.id
      LEFT OUTER JOIN airports ap ON loc.airport_id = ap.id
      LEFT OUTER JOIN airlines al ON loc.airline_id = al.id
      WHERE 
        loc.archived IS NULL
      ORDER BY ap.name, loc.type, al.name
    ";
    return $db->get_rows($query);
  }

  protected function reset(): void
  {
    parent::reset();

    $this->airportId = null;
    $this->airport = null;
    $this->airlineId = null;
    $this->airline = null;
    $this->locationId = null;
    $this->location = null;
    $this->type = null;
  }

  public static function getAirportLocation(int $airportId, int $airlineId, string $type): ?int
  {
    $db = Database::getInstance();
    $query = "
      SELECT location_id FROM airport_locations
      WHERE 
        airport_id = :airport_id
        AND airline_id = :airline_id
        AND `type` = :type
        AND archived IS NULL
    ";
    $params = [
      'airport_id' => $airportId,
      'airline_id' => $airlineId,
      'type' => $type
    ];
    return $db->get_var($query, $params);
  }

}