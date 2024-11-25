<?php
require_once 'class.data.php';

class Config
{
  public static function get($node)
  {
    $db = new data();
    $sql = 'SELECT config FROM config WHERE node = :node';
    $data = ['node' => $node];
    if ($configString = $db->get_var($sql, $data)) {
      return json_decode($configString);
    }
    return false;
  }
}