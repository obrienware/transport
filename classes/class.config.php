<?php
require_once 'class.data.php';

class Config
{
  public static function get($node)
  {
    $db = new data();
    $query = 'SELECT config FROM config WHERE node = :node';
    $params = ['node' => $node];
    if ($configString = $db->get_var($query, $params)) {
      return json_decode($configString);
    }
    return false;
  }
}