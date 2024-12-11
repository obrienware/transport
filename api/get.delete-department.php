<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.department.php';
$department = new Department($_REQUEST['id']);

$before = $department->getState();
$description = 'Deleted department: '.$department->name;
Audit::log('deleted', 'departments', $description, $before);
$result = $department->delete();

die(json_encode(['result' => $result]));