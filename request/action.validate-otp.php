<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

if (User::validateOTP($_GET['email'], $_GET['otp'])) {
  $user = new User();
  $user->getUserByEmail($_GET['email']);
  $_SESSION['user'] = (object)[
    'id' => $user->getId(),
    'authenticated' => true,
  ];
  exit(json_encode([
    'result' => true,
    'name' => $user->firstName
  ]));
}
echo json_encode(['result' => false]);