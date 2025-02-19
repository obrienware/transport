<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class ImageLibrary extends Base
{
  protected $tableName = 'image_library';
  protected $tableDescription = 'Image Library';

  public ?string $title = null;
  public ?string $fileName = null;
  public ?string $fileType = null;

  public function getName(): string
  {
    return is_null($this->title) ? 'no-name' : $this->title;
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
    $this->row = $row;
    $this->action = 'update';

    $this->id = $row->id;
    $this->title = $row->title;
    $this->fileName = $row->filename;
    $this->fileType = $row->file_type;
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
      'title' => $this->title,
      'filename' => $this->fileName,
      'file_type' => $this->fileType,
      'user' => $userResponsibleForOperation
    ];

    if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
      $params['id'] = $this->id;
      $query = "
        UPDATE {$this->tableName} SET
          title = :title,
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
          title = :title,
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

    $this->title = null;
    $this->fileName = null;
    $this->fileType = null;
  }
}