<?php
header('Content-Type: application/json');
require_once 'class.department.php';
$json = json_decode(file_get_contents("php://input"));

$department = new Department($json->id);
$department->name = $json->name;
$department->mayRequest = $json->mayRequest ? 1 : 0;

if ($department->save()) {
  $result = $department->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
