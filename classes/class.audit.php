<?php
require_once 'class.data.php';

class Audit
{
  public $action;
  public $table;
  public $description;
  public $before;
  public $after;
  public $user;

  public function commit()
  {
    if ($this->before == 'null') $this->before = null;
    if ($this->after == 'null') $this->after = null;
    Audit::log($this->action, $this->table, $this->description, $this->before, $this->after, $this->user);
  }

  static public function log(string $action, string $table, string $description, $before = null, $after = null, $user = null)
  {
    $db = data::getInstance();
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
      'user' => $user,
      'action' => $action,
      'table' => $table,
      'description' => $description,
      'before' => $before,
      'after' => $after
    );
    $db->query($query, $params);  
  }
}