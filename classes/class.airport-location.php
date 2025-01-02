<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.location.php';
require_once 'class.airport.php';
require_once 'class.airline.php';

class AirportLocation
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;
  
  public $airportId;
  public $airport;
  public $airlineId;
  public $airline;
  public $locationId;
  public $location;
  public $type;

  public function __construct(int $id = null)
  {
    $this->db = new data();
    if ($id) $this->load($id);
  }


  public function load(int $id): bool
  {
    $query = "SELECT * FROM airport_locations WHERE id = :id";
    $params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

      $this->id = $row->id;
      $this->airportId = $row->airport_id;
      $this->airlineId = $row->airline_id;
      $this->locationId = $row->location_id;
      $this->type = $row->type;
      $this->archived = $row->archived;

      if ($this->locationId) {
        $this->location = new Location($this->locationId);
      }

      if ($this->airportId) {
        $this->airport = new Airport($this->airportId);
      }

      if ($this->airlineId) {
        $this->airline = new Airline($this->airlineId);
      }

      return true;
    }
    return false;
  }


	public function getId(): int | null
	{
		return $this->id;
	}


  public function save(string $user = null): bool
  {
    $this->lastError = null;
		$audit = new Audit();
		$audit->user = $user;
		$audit->action = $this->action;
		$audit->table = 'airport_locations';
		$audit->before = json_encode($this->row);

    $params = [
      'airport_id' => $this->airportId,
      'airline_id' => $this->airlineId,
      'location_id' => $this->locationId,
      'type' => $this->type,
      'user' => $user
    ];

    if ($this->action == 'create') {
      $audit->description = 'Created airport location';
      $query = "
        INSERT INTO airport_locations (
          airport_id,
          airline_id,
          location_id,
          `type`,
          created,
          created_by
        ) VALUES (
          :airport_id,
          :airline_id,
          :location_id,
          :type,
          NOW(),
          :user
        )
      ";
    } else {
      $audit->description = 'Updated airport location';
      $query = "
        UPDATE airport_locations SET
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


  public function delete(string $user = null): bool
  {
    $this->lastError = null;
		$audit = new Audit();
		$audit->user = $user;
		$audit->action = 'delete';
		$audit->table = 'airport_locations';
		$audit->before = json_encode($this->row);

    $query = "UPDATE airport_locations SET archived = NOW(), archived_by = :user WHERE id = :id";
    $params = ['id' => $this->id, 'user' => $user];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Airport Location deleted';
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
    $db = new data();
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

    $this->airportId = null;
    $this->airport = null;
    $this->airlineId = null;
    $this->airline = null;
    $this->locationId = null;
    $this->location = null;
    $this->type = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
  }


	public function getLastError(): string
	{
		return $this->lastError;
	}


  public static function getAirportLocation(int $airportId, int $airlineId, string $type): int
  {
    $db = new data();
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