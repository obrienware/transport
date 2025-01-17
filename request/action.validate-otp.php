<?php
header('Content-Type: application/json');
require_once 'class.user.php';
if (User::validateOTP($_REQUEST['email'], $_REQUEST['otp'])) {
  $user = new User();
  $user->getUserByEmail($_REQUEST['email']);
  $_SESSION['user'] = (object)[
    'id' => $user->getId(),
    'authenticated' => true,
  ];
  die(json_encode([
    'result' => true,
    'name' => $user->firstName
  ]));
}
echo json_encode(['result' => false]);