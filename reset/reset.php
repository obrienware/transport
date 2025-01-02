<?php
require_once 'class.data.php';
$db = new data();
$query = "SELECT * FROM users WHERE reset_token = :token AND token_expiration >= NOW()";
$params = ['token' => $_REQUEST['path']];
if ($user = $db->get_row($query, $params)) {
  $db->query("UPDATE users SET reset_token = NULL, token_expiration = NULL, change_password = 1 WHERE id = :id", ['id' => $user->id]);
  $_SESSION['user'] = (object) [
    'id' => $user->id,
    'username' => $user->username,
    'authenticated' => true
  ];
  return header('Location: /page.new-password.php');
}
?>
Seems you have an invalid token, or this token has expired. Please try and reset your password again. <a href="/page.forgot-password.php">Forgot Password</a>