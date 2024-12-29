<?php
require_once 'class.data.php';

class Audit
{
  public $action;
  public $table;
  public $description;
  public $before;
  public $after;

  public function commit()
  {
    Audit::log($this->action, $this->table, $this->description, $this->before, $this->after);
  }

  static public function log(string $action, string $table, string $description, $before = null, $after = null)
  {
    $db = new data();
    $sql = "
    INSERT INTO audit_trail SET
      datetimestamp = NOW(),
      `user` = :user,
      `action` = :action,
      affected_tables = :table,
      description = :description,
      `before` = :before,
      `after` = :after
    ";
    $data = array (
      'user' => $_SESSION['user']->username,
      'action' => $action,
      'table' => $table,
      'description' => $description,
      'before' => $before,
      'after' => $after
    );
    $db->query($sql, $data);  
  }
}