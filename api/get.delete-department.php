<?php
header('Content-Type: application/json');
require_once 'class.department.php';
$result = Department::deleteDepartment($_REQUEST['id']);
die(json_encode(['result' => $result]));