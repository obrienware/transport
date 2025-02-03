<?php
declare(strict_types=1);

namespace Transport;

require_once __DIR__.'/../../autoload.php';

class Audit
{
  public ?string $action = null;
  public ?string $tableName = null;
  public ?string $description = null;
  public ?string $before = null;
  public ?string $after = null;
  public ?string $username = null;

  public function commit(): void
  {
    if ($this->before == 'null') $this->before = null;
    if ($this->after == 'null') $this->after = null;
    Audit::log($this->action, $this->tableName, $this->description, $this->before, $this->after, $this->username);
  }

  public static function log(string $action, string $tableName, string $description, ?string $before = null, ?string $after = null, ?string $username = null)
  {
    $db = Database::getInstance();
    $query = "
    INSERT INTO audit_trail SET
      datetimestamp = NOW(),
      `user` = :user,
      `action` = :action,
      affected_tables = :table,
      description = :description,
      `before` = :before,
      `after` = :after
    ";
    $params = array (
      'user' => $username,
      'action' => $action,
      'table' => $tableName,
      'description' => $description,
      'before' => $before,
      'after' => $after
    );
    $db->query($query, $params);  
  }
}