<?php
header('Content-Type: application/json');

require_once 'class.data.php';
$db = new data();
$sql = "SELECT * FROM users WHERE username = :username";
$data = ['username' => $_REQUEST['username']];
$result = $db->get_row($sql, $data);
$_SESSION['user'] = $result;
echo json_encode($result);
