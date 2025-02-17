<?php
declare(strict_types=1);

namespace Transport;

require_once __DIR__.'/../../autoload.php';

class Authenticate
{
  public static function logIn (string $username, string $password): int | false
  {
    $db = Database::getInstance();
    $query = 'SELECT * FROM users WHERE (username = :username OR email_address = :username) AND password = :password';
    $params = ['username' => $username, 'password' => md5($password)];
    if ($id = $db->get_var($query, $params)) {
      // Update last login
      $db->query(
        "UPDATE users SET last_logged_in = NOW() WHERE id = :user_id",
        ['user_id' => $id]
      );
      $db->query(
        "INSERT INTO authentication_log SET datetimestamp = NOW(), username = :username, password = '*****', successful = 1",
        ['username' => $username]
      );
      return $id;
    }
    $db->query(
      "INSERT INTO authentication_log SET datetimestamp = NOW(), username = :username, password = :password, successful = 0",
      ['username' => $username, 'password' => $password] // We're only logging incorrect passwords
    );
    return false;
  }
}