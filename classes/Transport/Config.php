<?php
declare(strict_types=1);

namespace Transport;

require_once __DIR__.'/../../autoload.php';

use Transport\Database;

class Config
{
  private string $node;
  public object $config;
  public ?string $configString = null;

  public function __construct(string $node)
  {
    $this->config = (object) [];

    $db = Database::getInstance();
    $query = 'SELECT * FROM config WHERE node = :node';
    $params = ['node' => $node];
    if ($row = $db->get_row($query, $params)) {
      $this->node = $row->node;
      $this->config = json_decode($row->config);
      $this->configString = $row->json5;
    }
  }

  public function save(?string $userResponsibleForOperation = null): void
  {
    $db = Database::getInstance();
    $query = 'UPDATE config SET config = :config, json5 = :json5 WHERE node = :node';
    $params = [
      'config' => json_encode($this->config, JSON_PRETTY_PRINT),
      'json5' => $this->configString,
      'node' => $this->node
    ];
    $db->query($query, $params);

    $query = "
      INSERT INTO config_log SET
        datetimestamp = NOW(),
        node = :node,
        config = :config,
        user = :user
    ";
    $params = [
      'node' => $this->node,
      'config' => $this->configString,
      'user' => $userResponsibleForOperation
    ];
    $db->query($query, $params);
  }

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