<?php
require_once 'class.data.php';

class Authenticate
{
  public static function logIn ($username, $password)
  {
    $db = new data();
    $sql = 'SELECT id FROM users WHERE username = :username AND password = :password';
    $data = ['username' => $username, 'password' => md5($password)];
    return $db->get_var($sql, $data); // Which will either return the user id if found, or false
  }
}