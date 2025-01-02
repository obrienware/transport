<?php
header('Content-Type: application/json');
require_once 'class.department.php';
$department = new Department($_REQUEST['id']);
$result = $department->delete();
die(json_encode(['result' => $result]));