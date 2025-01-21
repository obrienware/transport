<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class EmailTemplates extends Base
{
  protected $tableName = 'email_templates';
  protected $tableDescription = 'Email Templates';

  public ?string $name = null;
  public ?string $content = null;
  public array $availableVariables = [];

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
    $utc = new DateTimeZone('UTC');
    $this->row = $row;
    $this->action = 'update';

    $this->id = $row->id;
    $this->name = $row->name;
    $this->content = $row->content;
    $this->availableVariables = $row->available_variables ? explode(', ', $row->available_variables) : [];

    if (!empty($row->archived)) {
      $this->archived = (new DateTime($row->archived, $utc))->setTimezone($this->timezone);
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
      'content' => $this->content,
      'user' => $userResponsibleForOperation
		];

    if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE email_templates SET
					content = :content,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		}
    // In this case the user won't be creating a new email template

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
		$query = 'SELECT * FROM email_templates WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
	}
  
  public static function get($templateName) {
    $db = Database::getInstance();
    $sqlQuery = "SELECT content FROM email_templates WHERE name = :name";
    $params = ['name' => $templateName];
    return $db->get_var($sqlQuery, $params);
  }
}