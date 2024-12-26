<?php
require_once 'class.data.php';
global $db;
if (!isset($db)) $db = new data();

class Audit
{
  static public function log(string $action, string $table, string $description, $before = null, $after = null)
  {
    global $db;
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