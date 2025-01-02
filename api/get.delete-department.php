<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.department.php';
$department = new Department($_REQUEST['id']);
$result = $department->delete($user->getUsername());
die(json_encode(['result' => $result]));