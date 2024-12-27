<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();

class Authenticate
{
  public static function logIn ($username, $password)
  {
    global $db;
    $sql = 'SELECT * FROM users WHERE (username = :username OR email_address = :username) AND password = :password';
    $data = ['username' => $username, 'password' => md5($password)];
    if ($id = $db->get_var($sql, $data)) {
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