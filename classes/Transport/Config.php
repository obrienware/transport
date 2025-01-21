<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

class Config
{
  public static function get($node): object | false
  {
    $db = Database::getInstance();
    $query = 'SELECT config FROM config WHERE node = :node';
    $params = ['node' => $node];
    if ($configString = $db->get_var($query, $params)) {
      return json_decode($configString);
    }
    return false;
  }
}