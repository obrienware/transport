<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.department.php';
$json = json_decode(file_get_contents("php://input"));

$department = new Department($json->id);
$previousName = $department->name;

$department->name = $json->name;
$department->mayRequest = $json->mayRequest ? 1 : 0;

$result = $department->save();
if ($json->id) {
  $before = $department->getState();
  $id = $json->id;
  $action = 'modified';
  $description = 'Changed department: '.$previousName;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added department: '.$json->name;
}
$department->getDepartment($id);
$after = $department->getState();
Audit::log($action, 'departments', $description, $before, $after);

echo json_encode(['result' => $result]);
