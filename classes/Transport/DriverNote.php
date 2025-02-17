<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class DriverNote extends Base
{
	protected $tableName = 'driver_notes';
	protected $tableDescription = 'Driver Notes';
  
  public ?string $title = null;
  public ?string $note = null;

  public function getName(): string
  {
    return is_null($this->title) ? 'no-title' : $this->title;
  }

  public function load(int $id): bool
  {
    $db = Database::getInstance();
    $query = "SELECT * FROM driver_notes WHERE id = :id";
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
    $this->title = $row->title;
    $this->note = $row->note;
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
      'title' => $this->title,
      'note' => $this->note,
      'username' => $userResponsibleForOperation
    ];

    if ($this->action === 'update') {
      $audit->description = $this->tableDescription.' updated: '.$this->getName();
      $params['id'] = $this->id;
      $query = "
        UPDATE driver_notes SET 
          title = :title,
          note = :note,
          modified = NOW(),
          modified_by = :username
        WHERE id = :id
      ";
    } else {
      $audit->description = $this->tableDescription.' created: '.$this->getName();
      $query = "
        INSERT INTO driver_notes SET 
          title = :title,
          note = :note,
          created = NOW(),
          created_by = :username
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
    $query = "SELECT * FROM driver_notes WHERE archived IS NULL ORDER BY title";
    return $db->get_rows($query);
  }

  protected function reset(): void
  {
    parent::reset();

    $this->title = null;
    $this->note = null;
  }

}