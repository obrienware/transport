<?php
require_once 'class.audit.php';
require_once 'class.data.php';
require_once 'class.location.php';

class AirportLocation
{
	private $db;
	private $row;
	private $action = 'create';
  
  public $id;
  public $airportId;
  public $airlineId;
  public $locationId;
  public $location;
  public $type;

  public function __construct(int $id = null)
  {
    $this->db = new data();
    if ($id) $this->load($id);
  }


  public function load($id)
  {
    $sql = "SELECT * FROM airport_locations WHERE id = :id";
    $params = [':id' => $id];
		if ($item = $this->db->get_row($sql, $params)) {
			$this->action = 'update';
			$this->row = $item;

      $this->id = $item->id;
      $this->airportId = $item->airport_id;
      $this->airlineId = $item->airline_id;
      $this->locationId = $item->location_id;
      $this->type = $item->type;

      if ($this->locationId) {
        $this->location = new Location($this->locationId);
      }

      return true;
    }
    return false;
  }


  public function save(): array
  {
		$audit = new Audit();
		$audit->action = $this->action;
		$audit->table = 'airport_locations';
		$audit->before = json_encode($this->row);

    $params = [
      'airport_id' => $this->airportId,
      'airline_id' => $this->airlineId,
      'location_id' => $this->locationId,
      'type' => $this->type
    ];
    if ($this->action == 'create') {
      $audit->description = 'Created airport location';
      $sql = "
        INSERT INTO airport_locations (
          airport_id,
          airline_id,
          location_id,
          `type`
        ) VALUES (
          :airport_id,
          :airline_id,
          :location_id,
          :type
        )
      ";
    } else {
      $audit->description = 'Updated airport location';
      $sql = "
        UPDATE airport_locations SET
          airport_id = :airport_id,
          airline_id = :airline_id,
          location_id = :location_id,
          `type` = :type
        WHERE id = :id
      ";
      $params['id'] = $this->id;
    }
    try {
      $result = $this->db->query($sql, $params);
      $id = ($this->action === 'create') ? $result : $this->id;
      $this->load($id);
      $audit->after = json_encode($params);
      $audit->commit();
      return ['result' => $result];
    } catch (Exception $e) {
			return [
				'result' => false,
				'error' => $e->getMessage()
			];
    }
  }


  public function delete(): bool
  {
		$audit = new Audit();
		$audit->action = 'delete';
		$audit->table = 'airport_locations';
		$audit->before = json_encode($this->row);

    $sql = "UPDATE airport_locations SET archived = NOW() WHERE id = :id";
    $params = [':id' => $this->id];
    $result = $this->db->query($sql, $params);

		$audit->description = 'Airport Location deleted';
		$audit->commit();
    return $result;
  }


  public static function getAll(): array
  {
    $db = new data();
    $sql = "
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
    return $db->get_results($sql);
  }

  public static function getAirportLocation(int $airportId, int $airlineId, string $type): int
  {
    $db = new data();
    $sql = "
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
    return $db->get_var($sql, $params);
  }
}