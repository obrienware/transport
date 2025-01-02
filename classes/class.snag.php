<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';


class Snag
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

  public $vehicleId;
  public $userId;
  public $logged;
  public $summary;
  public $description;
  public $acknowledged;
  public $acknowledgedBy;
  public $resolved; // Date/time the issue was resolved
  public $resolution; // What was done to resolve the issue
  public $resolvedBy; // Person who resolved the issue
  public $comments; // In case you need to monitor the situation for example.

  
	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


  public function load(int $id): bool
  {
    $query = "SELECT * FROM snags WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
      $this->logged = $row->logged;
      $this->userId = $row->user_id;
      $this->vehicleId = $row->vehicle_id;
      $this->summary = $row->summary;
      $this->description = $row->description;
      $this->acknowledged = $row->acknowledged;
      $this->acknowledgedBy = $row->acknowledged_by;
      $this->resolved = $row->resolved;
      $this->resolvedBy = $row->resolved_by;
      $this->resolution = $row->resolution;
      $this->comments = $row->comments;
      $this->archived = $row->archived;
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
		$audit->table = 'snags';
		$audit->before = json_encode($this->row);

    $params = [
      'logged' => $this->logged,
      'user_id' => $this->userId,
      'vehicle_id' => $this->vehicleId,
      'summary' => $this->summary,
      'description' => $this->description,
      'acknowledged' => $this->acknowledged,
      'acknowledged_by' => $this->acknowledgedBy,
      'resolved' => $this->resolved,
      'resolved_by' => $this->resolvedBy,
      'resolution' => $this->resolution,
      'comments' => $this->comments,
      'user' => $user
    ];

		if ($this->action === 'update') {
			$audit->description = 'Snag updated: '.$this->summary;
			$params['id'] = $this->id;
      $query = "
        UPDATE snags SET
          logged = :logged,
          user_id = :user_id,
          vehicle_id = :vehicle_id,
          summary = :summary,
          description = :description,
          acknowledged = :acknowledged,
          acknowledged_by = :acknowledged_by,
          resolved = :resolved,
          resolved_by = :resolved_by,
          resolution = :resolution,
          comments = :comments,
          modified = NOW(),
          modified_by = :user
        WHERE id = :id
      ";
    } else {
      $audit->description = 'Snag created: '.$this->summary;
      $query ="
        INSERT INTO snags SET
          logged = :logged,
          user_id = :user_id,
          vehicle_id = :vehicle_id,
          summary = :summary,
          description = :description,
          acknowledged = :acknowledged,
          acknowledged_by = :acknowledged_by,
          resolved = :resolved,
          resolved_by = :resolved_by,
          resolution = :resolution,
          comments = :comments,
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


	public function delete(string $user = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $user;
		$audit->action = 'delete';
		$audit->table = 'snags';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE snags 
			SET 
				archived = NOW(), archived_by = :user 
			WHERE id = :id
		";
		$params = [
			'user' => $user, 
			'id' => $this->id
		];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Snag deleted: '.$this->summary;
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


  public function isArchived(): bool
	{
		return isset($this->archived);
	}


  public function reset(): void
  {
    $this->id = null;
    $this->row = null;
		$this->lastError = null;
    $this->action = 'create';
    $this->archived = null;

    $this->logged = null;
    $this->userId = null;
    $this->vehicleId = null;
    $this->summary = null;
    $this->description = null;
    $this->acknowledged = null;
    $this->acknowledgedBy = null;
    $this->resolved = null;
    $this->resolvedBy = null;
    $this->resolution = null;
    $this->comments = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
  }


  public static function getSnags($vehicleId = null): array
  {
    $db = new data();
    if ($vehicleId) {
      $query = "SELECT * FROM snags WHERE vehicle_id = :vehicle_id ORDER BY datetimestamp";
      $params = ['vehicle_id' => $vehicleId];
    } else {
      $query = "
        SELECT s.*, v.name AS vehicle
        FROM snags s
        LEFT OUTER JOIN vehicles v ON v.id = s.vehicle_id
        WHERE 
          s.archived IS NULL 
        ORDER BY v.name, s.datetimestamp
      ";
    }
    return $db->get_rows($query, $params);
  }
}