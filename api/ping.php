<?php
header('Content-Type: application/json');
$result = (isset($_SESSION['user']));
echo json_encode([
  'result' => $result,
  'user' => $_SESSION['user']
]);