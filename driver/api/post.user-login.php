<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));

require_once 'class.data.php';
$db = new data();
$sql = "SELECT * FROM users WHERE username = :username AND password = :password";
$data = [
  'username' => $json->username,
  'password' => md5($json->password)
];
if ($result = $db->get_row($sql, $data)) {
  $_SESSION['user'] = $result;
  die(json_encode($result));
}

echo json_encode(false);