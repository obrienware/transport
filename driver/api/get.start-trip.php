<?php
header('Content-Type: application/json');

require_once 'class.data.php';
$db = new data();
$sql = "UPDATE trips SET started = NOW() WHERE id = :id";
$data = ['id' => $_REQUEST['tripId']];
$result = $db->query($sql, $data);
echo json_encode(['result' => $result]);
