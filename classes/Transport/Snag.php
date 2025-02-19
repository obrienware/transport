<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Snag extends Base
{
  protected $tableName = 'snags';
  protected $tableDescription = 'Snags';

  public ?int $vehicleId = null;
  public ?int $userId = null;
  private ?DateTime $logged = null;
  public ?string $summary = null;
  public ?string $description = null;
  private ?DateTime $acknowledged = null;
  public ?string $acknowledgedBy = null;
  private ?DateTime $resolved = null; // Date/time the issue was resolved
  public ?string $resolution = null; // What was done to resolve the issue
  public ?string $resolvedBy = null; // Person who resolved the issue
  public ?string $comments = null; // In case you need to monitor the situation for example.


  public function getName(): string
  {
    return is_null($this->summary) ? 'no-name' : $this->summary;
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
    $this->userId = $row->user_id;
    $this->vehicleId = $row->vehicle_id;
    $this->summary = $row->summary;
    $this->description = $row->description;
    $this->acknowledgedBy = $row->acknowledged_by;
    $this->resolvedBy = $row->resolved_by;
    $this->resolution = $row->resolution;
    $this->comments = $row->comments;

    if (!empty($row->logged)) {
      $this->logged = (new DateTime($row->logged, $defaultTimezone))->setTimezone($this->timezone);
    }

    if (!empty($row->acknowledged)) {
      $this->acknowledged = (new DateTime($row->acknowledged, $defaultTimezone))->setTimezone($this->timezone);
    }

    if (!empty($row->resolved)) {
      $this->resolved = (new DateTime($row->resolved, $defaultTimezone))->setTimezone($this->timezone);
    }

    if (!empty($row->archived)) {
      $this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
    }
  }

  public function __set($name, $value)
  {
    if (property_exists($this, $name)) {
      switch ($name) {
        case 'logged':
        case 'acknowledged':
        case 'resolved':
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
        case 'logged':
        case 'acknowledged':
        case 'resolved':
          return $this->$name->format('Y-m-d');
      }
    }
  }

	public function save(?string $userResponsibleForOperation = null): bool
	{
		$defaultTimezone = new DateTimeZone($_ENV['TZ'] ?? 'UTC');
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

    $params = [
      'logged' => (!is_null($this->logged)) ? $this->logged->setTimezone($defaultTimezone)->format('Y-m-d H:i:s') : null,
      'user_id' => $this->userId,
      'vehicle_id' => $this->vehicleId,
      'summary' => $this->summary,
      'description' => $this->description,
      'acknowledged' => (!is_null($this->acknowledged)) ? $this->acknowledged->setTimezone($defaultTimezone)->format('Y-m-d H:i:s') : null,
      'acknowledged_by' => $this->acknowledgedBy,
      'resolved' => (!is_null($this->resolved)) ? $this->resolved->setTimezone($defaultTimezone)->format('Y-m-d H:i:s') : null,
      'resolved_by' => $this->resolvedBy,
      'resolution' => $this->resolution,
      'comments' => $this->comments,
      'user' => $userResponsibleForOperation
    ];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
      $query = "
        UPDATE {$this->tableName} SET
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
      $audit->description = $this->tableDescription.' created: '.$this->getName();
      $query ="
        INSERT INTO {$this->tableName} SET
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
  }

  public static function getSnags(?int $vehicleId = null): array | false
  {
    $db = Database::getInstance();
    if ($vehicleId) {
      $query = "
      SELECT 
        s.*,
        GROUP_CONCAT(il.filename ORDER BY il.filename SEPARATOR ', ') AS image_filenames
      FROM snags s
      LEFT JOIN snag_images si ON (s.id = si.snag_id AND si.archived IS NULL)
      LEFT JOIN image_library il ON si.image_id = il.id
      WHERE 
        s.vehicle_id = :vehicle_id
        AND s.archived IS NULL
      GROUP BY s.id
      ORDER BY s.logged DESC
      ";
      $params = ['vehicle_id' => $vehicleId];
    } else {
      $query = "
        SELECT s.*, v.name AS vehicle
        FROM snags s
        LEFT OUTER JOIN vehicles v ON v.id = s.vehicle_id
        WHERE 
          s.archived IS NULL 
        ORDER BY v.name, s.logged DESC
      ";
    }
    return $db->get_rows($query, $params);
  }

  public function attachImage(int $imageId): bool
  {
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $_SESSION['user']->username;
		$audit->action = 'attach';
		$audit->tableName = 'snag_images';
    $audit->description = 'Image attached to snag: '.$this->id;

    $query = "
      INSERT INTO snag_images SET 
        snag_id = :snag_id, 
        image_id = :image_id,
        created = NOW(),
        created_by = :user
    ";
    $params = [
      'snag_id' => $this->id, 
      'image_id' => $imageId,
      'user' => $_SESSION['user']->username
    ];
    try {
			$db->query($query, $params);
      $audit->commit();
			return true;
		} catch (\Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
 }
}